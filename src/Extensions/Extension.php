<?php

namespace Lsr\Doc\Extensions;

use Lsr\Doc\Config\Config;

interface Extension
{

	public function __construct(
		Config $config,
	);

}