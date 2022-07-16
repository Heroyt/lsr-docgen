<?php

namespace Lsr\Doc\Extensions;

use Lsr\Doc\Symbols\Symbol;

interface SymbolExtension extends Extension
{

	/**
	 * Get a list of all Symbols that should be added by the extension
	 *
	 * @return Symbol[]
	 */
	public function getSymbols() : array;

}