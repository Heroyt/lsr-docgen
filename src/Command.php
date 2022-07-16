<?php

namespace Lsr\Doc;

use Lsr\Doc\CliTools\Colors;
use Lsr\Doc\CliTools\Enums\ForegroundColors;
use Lsr\Doc\CliTools\Enums\TextAttributes;
use Lsr\Doc\Config\CliArguments;
use Lsr\Doc\Config\Config;
use Lsr\Doc\Config\ConfigFile;
use Lsr\Doc\Config\Configurator;
use Lsr\Doc\Exceptions\ConfigurationException;
use Lsr\Doc\Extensions\PipelineExtension;
use Lsr\Doc\Pipeline\PipelineBase;
use Lsr\Doc\Scan\FileScanner;
use Lsr\Doc\Scan\SymbolExtractor;
use Lsr\Doc\Services\Cache;
use Lsr\Doc\Symbols\FileSymbol;
use Lsr\Doc\Symbols\SymbolMap;
use Nette\Loaders\RobotLoader;

class Command
{

	/** @var array<string, mixed> Parsed command line arguments */
	protected array $arguments = [];

	/** @var Config Parsed config from all config sources */
	protected Config $config;

	/** @var string[] All files to extract */
	protected array $files = [];
	/** @var FileSymbol[] */
	protected array $symbols = [];

	/**
	 * Input point for the command.
	 *
	 * @return never
	 * @throws Exceptions\FileException
	 */
	public static function start() : never {
		$command = new self();
		$command->run();
		exit(0);
	}

	/**
	 * Run the script
	 *
	 * @return void
	 * @throws Exceptions\FileException
	 */
	public function run() : void {
		$this->prepareConfig();
		$this->prepareFiles();

		// TODO: Start the command
		// Scan and autoload all files
		$autoloader = new RobotLoader();
		$autoloader->addDirectory(...$this->files);
		$autoloader->setTempDirectory($this->config->cacheDir);
		$autoloader->register();
		foreach ($this->files as $file) {
			$extractor = new SymbolExtractor($file, $this->config);
			$this->symbols[] = $extractor->extract();
		}
		SymbolMap::getInstance()->add(...$this->symbols);

		/** @var PipelineExtension[] $extensions */
		$extensions = $this->config->getExtensions(PipelineExtension::class);
		/** @var PipelineBase[] $pipeline */
		$pipeline = [];
		foreach ($extensions as $extension) {
			$pipeline[] = $extension->getPipeline();
		}
		// Flatten the array
		$pipeline = array_merge(...$pipeline);
		// Sort
		usort($pipeline, static function($pipelineA, $pipelineB) {
			/** @var PipelineBase $pipelineA */
			/** @var PipelineBase $pipelineB */
			return $pipelineA::ORDER - $pipelineB::ORDER;
		});
		// Walk through the pipeline
		foreach ($pipeline as $step) {
			/** @var PipelineBase $step */
			$step = new $step($this->config);
			$step->process($this->symbols);
		}
		file_put_contents('symbols.json', json_encode($this->symbols, JSON_PRETTY_PRINT));
	}

	/**
	 * Prepares run configuration to a config file
	 *
	 * @return void
	 * @throws Exceptions\FileException
	 * @see CliArguments
	 * @see ConfigFile
	 *
	 * @see Configurator
	 * @see Config
	 */
	public function prepareConfig() : void {
		$this->config = new Config();
		try {
			// Load configuration from different sources in hierarchical order
			(new CliArguments())->loadConfig($this->config);
			(new ConfigFile($this->config->configFile))->loadConfig($this->config);
		} catch (ConfigurationException $e) {
			fprintf(
				STDERR,
				Colors::color(ForegroundColors::RED, attribute: TextAttributes::BOLD).
				'Failed to load configuration.'.PHP_EOL.
				'Error: '.Colors::color(attribute: TextAttributes::UN_BOLD).$e->getMessage().PHP_EOL.PHP_EOL.
				'------------------------------------------------------------------------'.PHP_EOL.
				$e->getTraceAsString().PHP_EOL.
				Colors::reset()
			);
			exit(1);
		}
		if ($this->config->clearCache) {
			Cache::getInstance($this->config)->clear();
		}
		if ($this->config->printHelp) {
			$this->printHelp();
		}
	}

	/**
	 * Print help information about the command and its arguments
	 *
	 * @return never
	 */
	public function printHelp() : never {
		$caller = $_SERVER['argv'][0];
		if (str_contains($caller, 'php')) {
			$caller .= ' '.$_SERVER['argv'][1];
		}

		echo PHP_EOL.Colors::color(ForegroundColors::GREEN, attribute: TextAttributes::BOLD).'Usage:'.Colors::reset().PHP_EOL;
		echo TextAttributes::BOLD->value.$caller.' [-h | --help] [-c <file> | --config=<file>] [-o <dir> | --output <dir>] [sources...]'.TextAttributes::UN_BOLD->value.PHP_EOL;
		echo PHP_EOL.Colors::color(ForegroundColors::YELLOW).'Arguments:'.Colors::reset().PHP_EOL;
		echo "\t".Colors::color(ForegroundColors::BLUE).'[sources...]'.Colors::reset().PHP_EOL."\t\tSources to scan for source files. Can be a list of directories or concrete files.".PHP_EOL;
		foreach (CliArguments::OPTIONS as $short => $info) {
			$valueOpt = $info['value'] ?? '';
			$shortLong = '-'.$short.(empty($valueOpt) ? '' : ' '.$valueOpt);
			if (isset($info['long'])) {
				$shortLong .= ' | --'.$info['long'].(empty($valueOpt) ? '' : '='.$valueOpt);
			}
			$name = ($info['isOptional'] ?? false) ? '['.$shortLong.']' : '<'.$shortLong.'>';
			echo "\t".Colors::color(ForegroundColors::BLUE).$name.Colors::reset().PHP_EOL."\t\t".($info['description'] ?? '').PHP_EOL;
		}
		exit(0);
	}

	/**
	 * Load all files to be extracted
	 *
	 * @return void
	 * @see FileScanner
	 *
	 */
	protected function prepareFiles() : void {
		try {
			$this->files = (new FileScanner($this->config))->getFiles();
		} catch (Exceptions\FileException $e) {
			fprintf(
				STDERR,
				Colors::color(ForegroundColors::RED, attribute: TextAttributes::BOLD).
				'Error while scanning for files.'.PHP_EOL.
				'Error: '.Colors::color(attribute: TextAttributes::UN_BOLD).$e->getMessage().PHP_EOL.PHP_EOL.
				'------------------------------------------------------------------------'.PHP_EOL.
				$e->getTraceAsString().PHP_EOL.
				Colors::reset()
			);
			exit(2);
		}
	}

}