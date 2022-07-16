<?php

namespace Lsr\Doc\Symbols;

class FileSymbol extends Symbol
{

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
		$fileSymbol = new static($file, $namespace, $file);
		if (isset($parent)) {
			$parent->symbols[] = $fileSymbol;
		}
		return [$fileSymbol];
	}
}