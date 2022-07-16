<?php

namespace Lsr\Doc\Extensions;

use Lsr\Doc\DocBlock\Tags\Tag;

interface TagExtension extends Extension
{

	/**
	 * Get available tags
	 *
	 * @return array<string, string|Tag> Associative array of tag names => tag classes
	 */
	public function getTags() : array;

}