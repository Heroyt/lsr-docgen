<?php

namespace Lsr\Doc\Symbols;

abstract class Symbol implements SymbolInterface, \JsonSerializable
{

	public const PRIORITY = 1;

	/** @var Symbol[] */
	public array $symbols = [];

	/** @var array Any metadata that can be stored in the symbol */
	public array $metadata = [];

	public function __construct(
		public readonly string     $name,
		public readonly string     $namespace,
		public readonly string     $file,
		/** @var Symbol|null $parent Back-reference for parent symbol */
		protected readonly ?Symbol $parent = null,
	) {
	}

	public function jsonSerialize() : array {
		$data = get_object_vars($this);
		$data['class'] = $this::class;
		unset($data['parent']);
		return $data;
	}

}