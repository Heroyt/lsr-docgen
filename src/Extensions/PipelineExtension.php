<?php

namespace Lsr\Doc\Extensions;

use Lsr\Doc\Pipeline\PipelineBase;

interface PipelineExtension extends Extension
{

	/**
	 * @return PipelineBase[]
	 */
	public function getPipeline() : array;

}