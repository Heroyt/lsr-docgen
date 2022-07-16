<?php

namespace Lsr\Doc\DocBlock\Tags;

class BriefTag extends Tag
{

	public function __construct(string $value) {
		parent::__construct('brief', $value);
	}

}