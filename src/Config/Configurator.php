<?php

namespace Lsr\Doc\Config;

interface Configurator
{

	/**
	 * Load config from source into a Config object
	 *
	 * @param Config $config
	 *
	 * @return void
	 */
	public function loadConfig(Config $config) : void;

}