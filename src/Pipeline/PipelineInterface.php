<?php

namespace Lsr\Doc\Pipeline;

use Lsr\Doc\Symbols\Symbol;

interface PipelineInterface
{

	/**
	 * Process symbols - add metadata to symbols
	 *
	 * @param Symbol[] $symbols
	 *
	 * @return void
	 */
	public function process(array $symbols) : void;

}