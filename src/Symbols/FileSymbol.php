<?php

namespace Lsr\Doc\Symbols;

class FileSymbol extends Symbol
{

	/** @var array<string, string> "use" tokens calls. Can be used to look-up full import names when parsing */
	public array $uses = [];

	/**
	 * Scan a file for symbols of this type and construct them
	 *
	 * @param string      $content
	 * @param string      $file
	 * @param Symbol|null $parent
	 * @param string      $namespace
	 *
	 * @return static[]
	 * @post If $parent is given, add the created symbols to it (Symbol::$symbols)
	 */
	public static function scan(string $content, string $file, ?Symbol $parent = null, string $namespace = '') : array {
		$fileSymbol = new static($file, $namespace, $file, $parent);
		if (isset($parent)) {
			$parent->symbols[] = $fileSymbol;
		}

		// Scan for "use" tokens
		preg_match_all('/^use\s+((?:function\s+)?(?:[a-zA-Z_\x80-\xff][a-zA-Z\d_\x80-\xff]*\\\\?)+)\s*;$/m', $content, $matches);
		foreach ($matches[1] as $match) {
			// Convert function import from "function Namespace\someFunction" to "Namespace\someFunction()"
			if (str_starts_with($match, 'function')) {
				$match = trim(substr($match, 8)).'()';
			}

			$explode = explode('\\', $match);
			$name = end($explode);
			$fileSymbol->uses[$name] = $match;
		}

		return [$fileSymbol];
	}
}