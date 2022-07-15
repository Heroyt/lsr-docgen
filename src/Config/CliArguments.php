<?php

namespace Lsr\Doc\Config;

/**
 * This class parses options from the command line.
 */
class CliArguments implements Configurator
{

	public const OPTIONS = [
		'h' => [
			'long'        => 'help',
			'description' => 'Print help information about this command.',
			'isOptional'  => true,
		],
		'c' => [
			'long'          => 'config',
			'description'   => 'Custom configuration file.',
			'isOptional'    => true,
			'value'         => '<file>',
			'valueOptional' => false,
		],
		'o' => [
			'long'          => 'output',
			'description'   => 'Output directory.',
			'isOptional'    => true,
			'value'         => '<dir>',
			'valueOptional' => false,
		],
	];

	public static function getShortOpts() : string {
		$opts = '';
		foreach (self::OPTIONS as $short => $info) {
			$opts .= $short;
			if (isset($info['value'])) {
				$opts .= ':';
				if (isset($info['valueOptional']) && $info['valueOptional']) {
					$opts .= ':';
				}
			}
		}
		return $opts;
	}

	public static function getLongOpts() : array {
		$opts = [];
		foreach (self::OPTIONS as $short => $info) {
			if (!isset($info['long'])) {
				continue;
			}
			$opt = $info['long'];
			if (isset($info['value'])) {
				$opt .= ':';
				if (isset($info['valueOptional']) && $info['valueOptional']) {
					$opt .= ':';
				}
			}
			$opts[] = $opt;
		}
		return $opts;
	}

	/**
	 * Load config from source into a Config object
	 *
	 * @param Config $config
	 *
	 * @return void
	 */
	public function loadConfig(Config $config) : void {
		$options = getopt(self::getShortOpts(), self::getLongOpts(), $optIndex);
		$sources = array_slice($_SERVER['argv'] ?? [], $optIndex);

		if (isset($options['h']) || isset($options['help'])) {
			$config->printHelp = true;
		}

		if (isset($options['c']) || isset($options['config'])) {
			$config->configFile = $options['config'] ?? $options['c'];
		}

		if (isset($options['o']) || isset($options['output'])) {
			$config->output = $options['output'] ?? $options['o'];
		}

		if (!empty($sources)) {
			$config->sources = $sources;
		}
	}
}