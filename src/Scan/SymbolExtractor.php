<?php

namespace Lsr\Doc\Scan;

use Lsr\Doc\Config\Config;
use Lsr\Doc\Exceptions\FileException;
use Lsr\Doc\Extensions\SymbolExtension;
use Lsr\Doc\Services\Cache;
use Lsr\Doc\Symbols\FileSymbol;
use Lsr\Doc\Symbols\Symbol;

/**
 * Class responsible for extracting symbols from files
 *
 * Symbols are classes, functions and even files themselves
 */
class SymbolExtractor
{

	public const CACHE_CATEGORY = 'symbols';

	public function __construct(
		public string    $file,
		protected Config $config,
	) {
	}

	/**
	 * Extract symbols from a file
	 *
	 * @return FileSymbol
	 * @throws FileException On cache file read error
	 */
	public function extract() : FileSymbol {
		$cache = Cache::getInstance($this->config);
		$cached = $cache->getCache(self::CACHE_CATEGORY, $this->file);
		if (!is_null($cached)) {
			return $cached;
		}

		// Extract symbols from a file using a reflection API
		$contents = file_get_contents($this->file);

		// Get namespace
		$namespace = '';
		preg_match('/^namespace\s+((?:[a-zA-Z_\x80-\xff][a-zA-Z\d_\x80-\xff]*\\\\?)+)\s*;/m', $contents, $matches);
		if (!empty($matches[1])) {
			$namespace = $matches[1];
		}

		$file = new FileSymbol($this->file, $namespace, $this->file);

		// Get all symbols from extensions
		/** @var SymbolExtension[] $extensions */
		$extensions = $this->config->getExtensions(SymbolExtension::class);
		/** @var Symbol[] $symbolClasses */
		$symbolClasses = [];
		foreach ($extensions as $extension) {
			$symbolClasses[] = $extension->getSymbols();
		}
		// Flatten the array
		$symbolClasses = array_merge(...$symbolClasses);
		// Sort symbols
		usort($symbolClasses, static function($symbolA, $symbolB) {
			/** @var Symbol $symbolA */
			/** @var Symbol $symbolB */
			return $symbolA::PRIORITY - $symbolB::PRIORITY;
		});

		// Load all symbols
		foreach ($symbolClasses as $symbol) {
			$symbol::scan($contents, $this->file, $file, $namespace);
		}

		$cache->saveCache(self::CACHE_CATEGORY, $this->file, $file);
		return $file;
	}

}