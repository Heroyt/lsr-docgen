<?php

namespace Lsr\Doc\Pipeline;

use Lsr\Doc\DocBlock\DocBlockParser;
use Lsr\Doc\Symbols\ClassSymbol;
use Lsr\Doc\Symbols\FileSymbol;
use Lsr\Doc\Symbols\FunctionSymbol;
use Lsr\Doc\Symbols\InterfaceSymbol;
use Lsr\Doc\Symbols\Symbol;
use Lsr\Doc\Symbols\TraitSymbol;
use ReflectionClass;
use ReflectionException;
use ReflectionFunction;

class DocBlockExtract extends PipelineBase
{

	public const ORDER = 0;

	/** @var string[] List of entities that have their doc-block extractable via reflection API */
	public const COMMENTABLE_ENTITIES = [
		'class',
		'abstract',
		'function',
		'public',
		'protected',
		'private',
		'trait',
		'interface',
		'enum',
		'const',
	];

	/**
	 * @param Symbol[] $symbols
	 *
	 * @return void
	 * @throws ReflectionException
	 */
	public function process(array $symbols) : void {
		foreach ($symbols as $symbol) {
			if (!empty($symbol->symbols)) {
				$this->process($symbol->symbols);
			}

			if ($symbol instanceof FileSymbol) {
				$docBlocks = $this->extractFreeDocBlocks($symbol->file);
				$symbol->metadata['doc-blocks'] = [];
				foreach ($docBlocks as $docBlock) {
					$parser = new DocBlockParser($docBlock, $this->config);
					$symbol->metadata['doc-blocks'][] = $parser->parse();
				}
				continue;
			}
			$reflection = $this->getReflection($symbol);
			if (!isset($reflection)) {
				continue;
			}
			$symbol->metadata['reflection'] = $reflection;
			$comment = $reflection->getDocComment();
			if (!empty($comment)) {
				$parser = new DocBlockParser($comment, $this->config);
				$symbol->metadata['doc-block'] = $parser->parse();
			}
		}
	}

	/**
	 * Extract free doc-blocks in a file
	 *
	 * Doc block is considered "free" if there is an empty line after it or another doc-block.
	 *
	 * @param string $file
	 *
	 * @return array
	 */
	protected function extractFreeDocBlocks(string $file) : array {
		$docBlocks = [];
		$content = file_get_contents($file);
		preg_match_all('/(\/\*\*\s(?:.|\s)*\*\/)/mU', $content, $matches);

		foreach ($matches[0] as $match) {
			// End position of the docblock
			$end = strpos($content, $match) + strlen($match) + 1;
			$newLine = strpos($content, PHP_EOL, $end); // New line right after the doc-block
			$newLine = strpos($content, PHP_EOL, $newLine);
			$nextLine = trim(substr($content, $end, $newLine - $end));

			$firstToken = strtolower(substr($nextLine, 0, strpos($nextLine, ' ')));

			// Filter only the free doc-blocks
			if (empty($nextLine) || str_starts_with($nextLine, '/**') || !in_array($firstToken, self::COMMENTABLE_ENTITIES, true)) {
				$docBlocks[] = $match;
			}
		}

		return $docBlocks;
	}

	/**
	 * @param Symbol $symbol
	 *
	 * @return ReflectionClass|ReflectionFunction|null
	 * @throws ReflectionException
	 */
	private function getReflection(Symbol $symbol) : ReflectionClass|ReflectionFunction|null {
		$fullName = (!empty($symbol->namespace) ? $symbol->namespace.'\\' : '').$symbol->name;
		if ($symbol instanceof ClassSymbol || $symbol instanceof InterfaceSymbol || $symbol instanceof TraitSymbol) {
			return new ReflectionClass($fullName);
		}
		if ($symbol instanceof FunctionSymbol) {
			return new ReflectionFunction($fullName);
		}
		return null;
	}
}