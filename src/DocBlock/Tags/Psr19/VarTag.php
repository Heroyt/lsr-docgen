<?php

namespace Lsr\Doc\DocBlock\Tags\Psr19;

use Lsr\Doc\DocBlock\Tags\Tag;

class VarTag extends Tag
{

	public function __construct(string $value) {
		parent::__construct('var', $value);
	}

}