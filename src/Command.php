<?php

namespace Lsr\Doc;

use Lsr\Doc\Config\Config;

class Command
{

	/** @var array<string, mixed> Parsed command line arguments */
	protected array $arguments = [];

	protected Config $config;

	/**
	 * Input point for the command.
	 *
	 * @return never
	 */
	public static function start() : never {
		$command = new self();
		$command->run();
		exit(0);
	}

	public function run() : void {
		$this->prepareConfig();
		// TODO: Start the command
	}

	/**
	 * Prepares run configuration to a config file
	 *
	 * @return void
	 *
	 * @see Config
	 */
	public function prepareConfig() : void {
		// TODO: Implement
	}

}