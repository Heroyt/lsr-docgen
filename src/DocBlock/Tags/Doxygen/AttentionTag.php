<?php

namespace Lsr\Doc\DocBlock\Tags\Doxygen;

class AttentionTag extends \Lsr\Doc\DocBlock\Tags\Tag
{

	public function __construct(string $value) {
		parent::__construct('attention', $value);
	}

}