<?php

namespace Lsr\Doc\Pipeline;

use Lsr\Doc\Config\Config;

/**
 * Pipeline object is an object that somehow modifies symbol metadata.
 *
 * Should parse additional information and store them in symbol's metadata.
 */
abstract class PipelineBase implements PipelineInterface
{

	public const ORDER = 0;

	public function __construct(
		protected readonly Config $config
	) {
	}

}