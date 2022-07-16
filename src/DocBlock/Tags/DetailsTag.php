<?php

namespace Lsr\Doc\DocBlock\Tags;

class DetailsTag extends Tag
{

	public function __construct(string $value) {
		parent::__construct('details', $value);
	}

}