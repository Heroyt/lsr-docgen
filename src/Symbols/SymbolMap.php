<?php

namespace Lsr\Doc\Symbols;

/**
 * A structure containing all symbols
 *
 * Should be used to look up all symbols by their name.
 * This structure stores symbols in an associative array.
 * Symbols are searchable by their identifier.
 *  - File symbol's identifier is its filename
 *  - Function/Method symbol's identifier ends with "()"
 */
class SymbolMap
{

	protected static SymbolMap $instance;

	/** @var array<string, Symbol> */
	public array $symbols = [];

	/**
	 * @return SymbolMap
	 */
	public static function getInstance() : SymbolMap {
		if (!isset(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Add a symbol to look-up map
	 *
	 * @param Symbol ...$symbols
	 *
	 * @return $this
	 */
	public function add(Symbol ...$symbols) : static {
		foreach ($symbols as $symbol) {
			$identifier = (!empty($symbol->namespace) ? $symbol->namespace.'\\' : '').$symbol->name;
			if ($symbol instanceof FileSymbol) {
				$identifier = $symbol->name;
			}
			else if ($symbol instanceof FunctionSymbol) {
				$identifier .= '()';
			}

			$this->symbols[$identifier] = $symbol;

			// Recursively add child symbols
			if (!empty($symbol->symbols)) {
				$this->add(...$symbol->symbols);
			}
		}

		return $this;
	}

	/**
	 * Find a symbol object by its identifier
	 *
	 * @param string $identifier
	 *
	 * @return Symbol|null
	 */
	public function get(string $identifier) : ?Symbol {
		return $this->symbols[$identifier] ?? null;
	}

}