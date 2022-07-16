<?php

namespace Lsr\Doc\Config;

use Lsr\Doc\Extensions\BaseExtension;
use Lsr\Doc\Extensions\Extension;

/**
 * A structure containing the configuration options of the run.
 *
 * The configuration is parsed from CLI arguments and from a NEON config file.
 *
 * @see CliArguments
 * @see ConfigFile
 */
class Config
{

	/** @var bool If true, do not execute the script, but print help information instead. */
	public bool $printHelp = false;

	/** @var string[] Source files or directories */
	public array $sources = [];

	/** @var string[] Filetypes to scan in source directories */
	public array $fileExtensions = [];

	/** @var string Output directory */
	public string $output = '';

	public string $configFile = 'docgen.neon';

	/** @var string Caching directory, where all cache files will be stored */
	public string $cacheDir = '';

	/** @var bool If true, prior to running the script, all cache files will be invalidated */
	public bool $clearCache = false;

	/** @var Extension[] Loaded extensions */
	public array $extensions = [BaseExtension::class];

	/**
	 * Get loaded extensions
	 *
	 * @param string|null $interface Get only extensions that implement this interface
	 *
	 * @return Extension[]
	 */
	public function getExtensions(?string $interface = null) : array {
		// Filter only extensions that implement a certain interface
		$extensions = [];
		foreach ($this->extensions as $test) {
			if (!isset($interface) || \Lsr\Doc\class_implements($test, $interface)) {
				$extensions[] = new $test($this);
			}
		}
		return $extensions;
	}

}