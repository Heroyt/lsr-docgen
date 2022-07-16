<?php

namespace Lsr\Doc\Services;

use InvalidArgumentException;
use Lsr\Doc\Config\Config;
use Lsr\Doc\Exceptions\FileException;
use function Lsr\Doc\trailingSlashIt;

class Cache
{

	private static Cache $instance;

	/**
	 * @param Config $config
	 *
	 * @throws FileException
	 */
	public function __construct(
		protected Config $config
	) {
		$this->config->cacheDir = trailingSlashIt($this->config->cacheDir);
		if (!file_exists($this->config->cacheDir) && !mkdir($this->config->cacheDir) && !is_dir($this->config->cacheDir)) {
			throw new FileException('Cannot create caching directory - '.$this->config->cacheDir);
		}
	}

	/**
	 * @param Config|null $config
	 *
	 * @return Cache
	 * @throws FileException
	 */
	public static function getInstance(?Config $config) : Cache {
		if (!isset(self::$instance)) {
			if (!isset($config)) {
				throw new InvalidArgumentException('Missing required constructor argument (0) Config $config');
			}
			self::$instance = new self($config);
		}
		return self::$instance;
	}

	/**
	 * Remove all files and directories in the cache directory
	 *
	 * @post The cache directory will be empty, but not removed itself
	 *
	 * @return void
	 */
	public function clear() : void {
		$objects = scandir($this->config->cacheDir);
		foreach ($objects as $object) {
			if ($object !== "." && $object !== "..") {
				if (is_dir($this->config->cacheDir.DIRECTORY_SEPARATOR.$object) && !is_link($this->config->cacheDir."/".$object)) {
					rmdir($this->config->cacheDir.DIRECTORY_SEPARATOR.$object);
				}
				else {
					unlink($this->config->cacheDir.DIRECTORY_SEPARATOR.$object);
				}
			}
		}
	}

	/**
	 * Get cached value for a file in a given category
	 *
	 * @param string $category
	 * @param string $file
	 *
	 * @return mixed Null if no cache file exists
	 */
	public function getCache(string $category, string $file) : mixed {
		$hash = md5_file($file);

		$fileName = $this->config->cacheDir.$category.'-'.basename($file).'-'.$hash.'.txt';
		if (!file_exists($fileName)) {
			return null;
		}
		/** @noinspection UnserializeExploitsInspection */
		return unserialize(file_get_contents($fileName));
	}

	/**
	 * Save cached value into a cache file
	 *
	 * @param string $category
	 * @param string $file
	 * @param mixed  $value
	 *
	 * @return bool Success flag
	 */
	public function saveCache(string $category, string $file, mixed $value) : bool {
		$hash = md5_file($file);
		$fileName = $this->config->cacheDir.$category.'-'.basename($file).'-'.$hash.'.txt';
		return file_put_contents($fileName, serialize($value));
	}

}