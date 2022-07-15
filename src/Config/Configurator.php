<?php

namespace Lsr\Doc\Config;

/**
 * Interface for all possible configuration sources
 */
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