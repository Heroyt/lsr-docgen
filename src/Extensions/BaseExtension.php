<?php

namespace Lsr\Doc\Extensions;

use Lsr\Doc\Config\Config;
use Lsr\Doc\Symbols\ClassSymbol;
use Lsr\Doc\Symbols\FunctionSymbol;
use Lsr\Doc\Symbols\InterfaceSymbol;
use Lsr\Doc\Symbols\TraitSymbol;

class BaseExtension implements SymbolExtension
{

	public function __construct(
		protected readonly Config $config
	) {
	}

	/**
	 * @inheritDoc
	 */
	public function getSymbols() : array {
		return [
			ClassSymbol::class,
			FunctionSymbol::class,
			TraitSymbol::class,
			InterfaceSymbol::class,
		];
	}
}