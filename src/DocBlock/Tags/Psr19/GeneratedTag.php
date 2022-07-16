<?php

namespace Lsr\Doc\DocBlock\Tags\Psr19;

use Lsr\Doc\DocBlock\Tags\Tag;

class GeneratedTag extends Tag
{

	public function __construct(string $value) {
		parent::__construct('generated', $value);
	}

}