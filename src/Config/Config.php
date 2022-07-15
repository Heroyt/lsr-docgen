<?php

namespace Lsr\Doc\Config;

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

	/** @var string Output directory */
	public string $output = '';

	public string $configFile = 'docgen.neon';

}