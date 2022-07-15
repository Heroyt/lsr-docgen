<?php

namespace Lsr\Doc\Scan;

use Lsr\Doc\Config\Config;
use Lsr\Doc\Exceptions\FileException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;

class FileScanner
{

	public function __construct(
		protected Config $config
	) {
	}

	/**
	 * Get valid files that should be extracted based on set config
	 *
	 * @return string[]
	 * @throws FileException
	 */
	public function getFiles() : array {
		$files = [];

		foreach ($this->config->sources as $source) {
			if (is_dir($source)) {
				$this->scanDirectory($source, $files);
				continue;
			}

			if (!is_readable($source)) {
				throw new FileException('File "'.$source.'" is not readable.');
			}
			$files[] = $source;
		}

		return $files;
	}

	/**
	 * Scan a directory for all valid files
	 *
	 * @param string   $dir
	 * @param string[] $files
	 *
	 * @return void
	 * @throws FileException
	 */
	private function scanDirectory(string $dir, array &$files) : void {
		$Directory = new RecursiveDirectoryIterator($dir);
		$Iterator = new RecursiveIteratorIterator($Directory);

		// Check each defined file extension
		foreach ($this->config->fileExtensions as $extension) {
			// Trim to prevent some malformed strings
			$extension = trim($extension);
			// Transform non-regex strings to regex
			if (!str_starts_with($extension, '/')) {
				if (!str_starts_with($extension, '.')) {
					$extension = '.'.$extension;
				}
				$extension = '/^.+\\'.$extension.'$/i';
			}
			$Regex = new RegexIterator($Iterator, $extension, RegexIterator::MATCH);

			foreach ($Regex as $file) {
				if (!is_readable($file)) {
					throw new FileException('File "'.$file.'" is not readable.');
				}
				$files[] = $file;
			}
		}
	}

}