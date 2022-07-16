<?php

namespace Lsr\Doc\DocBlock\Tags\Doxygen;

class PostTag extends \Lsr\Doc\DocBlock\Tags\Tag
{

	public function __construct(string $value) {
		parent::__construct('post', $value);
	}

}