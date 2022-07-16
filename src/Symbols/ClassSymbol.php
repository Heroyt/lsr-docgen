<?php

namespace Lsr\Doc\Symbols;

class ClassSymbol extends Symbol
{

	public const PRIORITY = 1;

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
		preg_match_all('/^class\s+([a-zA-Z_\x80-\xff][a-zA-Z\d_\x80-\xff]*)(?:\s+[a-zA-Z_\x80-\xff][a-zA-Z\d_\x80-\xff\s]*)?\s*{/m', $content, $matches);
		$symbols = [];
		foreach ($matches[1] as $className) {
			$class = new static($className, $namespace, $file);
			$symbols[] = $class;
			if (isset($parent)) {
				$parent->symbols[] = $class;
			}
		}
		return $symbols;
	}
}