<?php

namespace Lsr\Doc\DocBlock\Tags;

/**
 * Base class for different doc-block tags
 *
 * Each specific class can have different availability.
 * Each specific tag can further parse its own value.
 * Each specific tag must have its own latte template to generate an output.
 */
class Tag
{

	public const AVAILABILITY_ANY      = 63;
	public const AVAILABILITY_FUNCTION = 1;
	public const AVAILABILITY_METHOD   = 2;
	public const AVAILABILITY_PROPERTY = 4;
	public const AVAILABILITY_CLASS    = 8;
	public const AVAILABILITY_FILE     = 16;
	public const AVAILABILITY_VARIABLE = 32;

	public const AVAILABILITY = self::AVAILABILITY_ANY;

	public function __construct(
		public string $name,
		public string $value,
	) {
	}

}