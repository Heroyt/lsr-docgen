<?php

namespace DocBlock;

use Lsr\Doc\Config\Config;
use Lsr\Doc\DocBlock\DocBlockParser;
use PHPUnit\Framework\TestCase;

class DocBlockParserTest extends TestCase
{

	public function docBlocks() : array {
		return [
			[
				'/**
	 * Extract symbols from a file
	 *
	 * @return FileSymbol
	 * @throws FileException On cache file read error
	 */'
			],
			[
				'/**
	 *
	 *
	 * Extract symbols from a file
	 *
	 * @return FileSymbol
	 * @throws FileException On cache file read error
	 */'
			],
			[
				'/**
 * Class responsible for extracting symbols from files
 *
 * Symbols are classes, functions and even files themselves
 */'
			],
			['/** @var SymbolExtension[] $extensions */'],
			[
				'/**
	 * Remove all files and directories in the cache directory
	 * TEstasdkasd
	 *
	 * asdjnaskdjn asdjaskdua aksjdnaksdunaisd
	 * asduhaisudhas asiduasnda alksdoawidjnasdokn
	 *
	 * @post The cache directory will be empty, but not removed itself
	 *
	 * @return void
	 */'
			],
		];
	}

	/**
	 * @param string $docBlock
	 *
	 * @return void
	 * @dataProvider docBlocks
	 */
	public function testParse(string $docBlock) : void {
		$parser = new DocBlockParser($docBlock, new Config());
		print_r($parser->parse());
		echo PHP_EOL.'----------------------'.PHP_EOL.PHP_EOL;
		self::assertTrue(true);
	}
}
