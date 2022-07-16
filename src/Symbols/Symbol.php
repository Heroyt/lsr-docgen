<?php

namespace Lsr\Doc\Symbols;

abstract class Symbol implements SymbolInterface
{

	public const PRIORITY = 1;

	/** @var Symbol[] */
	public array $symbols = [];

	public function __construct(
		public readonly string $name,
		public readonly string $namespace,
		public readonly string $file,
	) {
	}

}