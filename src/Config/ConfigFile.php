<?php

namespace Lsr\Doc\Config;

use Lsr\Doc\Exceptions\ConfigurationException;
use Lsr\Doc\Exceptions\InvalidConfigurationTypeException;
use Nette\Neon\Exception;
use Nette\Neon\Neon;
use Nette\Utils\Arrays;

/**
 * This class is responsible from loading configuration from given configuration file.
 */
class ConfigFile implements Configurator
{

	/** @var string Default configuration file that will be extended by user-defined ones */
	public const DEFAULT_CONFIG = __DIR__.'/../../docgen.neon';

	public function __construct(
		public string $file = self::DEFAULT_CONFIG,
	) {
	}

	/**
	 * Load config from source into a Config object
	 *
	 * @param Config $config
	 *
	 * @return void
	 * @throws ConfigurationException
	 */
	public function loadConfig(Config $config) : void {
		if (!file_exists($this->file)) {
			throw new ConfigurationException('Invalid configuration file - '.$this->file);
		}
		if (!is_readable($this->file)) {
			throw new ConfigurationException('Configuration file is not readable - '.$this->file);
		}

		try {
			$info = $this->parseConfigFile();
		} catch (Exception $e) {
			throw new ConfigurationException('Cannot read configuration file ('.$this->file.') - may not be a valid NEON file.'.PHP_EOL.'Message: '.$e->getMessage());
		}
		$config->configFile = $this->file;

		// Save individual configuration options.
		// Do not overwrite previously saved options.
		// This should also validate the option types.

		if (empty($config->output) && isset($info['output'])) {
			// Validate
			if (!is_string($info['output'])) {
				throw new InvalidConfigurationTypeException('Output settings must be a string.');
			}

			$config->output = $info['output'];
		}

		// Use "source" as an alias for "sources"
		if (isset($info['source'])) {
			if (isset($info['sources'])) {
				$info['sources'] = array_merge($info['sources'], $info['source']);
			}
			else {
				$info['sources'] = $info['source'];
			}
		}

		if (empty($config->sources) && isset($info['sources'])) {
			// Handle conversion from string to array of strings
			if (is_string($info['sources'])) {
				$info['sources'] = [$info['sources']];
			}

			// Validate
			if (!is_array($info['sources']) || !Arrays::every($info['sources'], static function(mixed $value) : bool {
					return is_string($value);
				})) {
				throw new InvalidConfigurationTypeException('Sources must either be a string or an array of strings.');
			}

			$config->sources = $info['sources'];
		}
	}

	/**
	 * Parse information from config files and return it as an array
	 *
	 * @return array{output:string,sources:string|string[]}
	 * @throws Exception
	 */
	protected function parseConfigFile() : array {
		$config = Neon::decodeFile(self::DEFAULT_CONFIG);
		if ($this->file !== self::DEFAULT_CONFIG) {
			$config = array_merge($config, Neon::decodeFile(self::DEFAULT_CONFIG));
		}
		return $config;
	}

}