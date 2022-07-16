<?php

namespace Lsr\Doc\DocBlock;

use Lsr\Doc\Config\Config;
use Lsr\Doc\DocBlock\Tags\BriefTag;
use Lsr\Doc\DocBlock\Tags\DetailsTag;
use Lsr\Doc\DocBlock\Tags\Tag;
use Lsr\Doc\Extensions\TagExtension;

class DocBlockParser
{

	public const TAG_PATTERN  = '/^\s*\*?\s*@([a-zA-Z]+)\s*(.*)$/m';
	public const TEXT_PATTERN = '/^([^@*\s].+)$/m';

	public function __construct(
		protected string $docBlock,
		protected Config $config,
	) {
	}

	/**
	 * Parse a docblock
	 *
	 * Extracts its tags and information that are stored in it.
	 *
	 * @return Tag[]
	 */
	public function parse() : array {
		$start = strpos($this->docBlock, '/**');
		$end = strpos($this->docBlock, '*/');
		if ($start === false || $end === false) {
			return [];
		}
		// Remove the doc block start and end
		$doc = trim(substr($this->docBlock, $start + 3, $end - strlen($this->docBlock)));
		$tags = [];

		// Extract individual lines and clean them
		$lines = array_map([$this, 'trimLine'], explode(PHP_EOL, $doc));

		// Remove first empty lines
		foreach ($lines as $key => $line) {
			if (!empty($line)) {
				break;
			}
			unset($lines[$key]);
		}
		$lines = array_values($lines);

		if (count($lines) === 0) {
			// Empty docblock
			return [];
		}

		// Test for auto-brief at the start. Can be multiline.
		if (preg_match(self::TEXT_PATTERN, $lines[0], $matches)) {
			// Docblock contains auto-brief on the first line
			$tags[] = new BriefTag($this->extractMultilineContent($lines));
		}

		// Reset line keys
		$lines = array_values($lines);

		// Test for auto-description at the start. Can be multiline.
		if (!empty($lines) && preg_match(self::TEXT_PATTERN, $lines[0], $matches)) {
			// Docblock contains description on the next line
			$tags[] = new DetailsTag($this->extractMultilineContent($lines));
		}


		/** @var TagExtension[] $extensions */
		$extensions = $this->config->getExtensions(TagExtension::class);
		/** @var array<string, Tag> $availableTags */
		$availableTags = [];
		foreach ($extensions as $extension) {
			$availableTags[] = $extension->getTags();
		}
		// Flatten the array
		$availableTags = array_merge(...$availableTags);

		// Extract all remaining tags
		foreach ($lines as $key => $line) {
			unset($lines[$key]); // Remove from the array

			// Skip empty lines
			if (empty($line)) {
				continue;
			}

			if (preg_match(self::TAG_PATTERN, $line, $matches)) {
				$tagName = strtolower($matches[1]);
				$value = trim($matches[2]);
				$multilineValue = $this->extractMultilineContent($lines);
				if (!empty($multilineValue)) {
					$value .= PHP_EOL.$multilineValue;
				}
				if (isset($availableTags[$tagName])) {
					$tagClass = new ($availableTags[$tagName])($value);
				}
				else {
					$tagClass = new Tag($tagName, $value);
				}
				$tags[] = $tagClass;
			}
		}

		return $tags;
	}

	/**
	 * Extract lines which contain a value
	 *
	 * Ends with the first empty line or the first tag.
	 *
	 * @param string[] $lines
	 *
	 * @return string
	 */
	protected function extractMultilineContent(array &$lines) : string {
		$content = [];
		foreach ($lines as $key => $line) {
			// Check if the next line is empty
			if (empty($line)) {
				unset($lines[$key]); // Remove empty line
				break;               // End
			}
			// Check if the next line contains a tag
			if (preg_match(self::TAG_PATTERN, $line)) {
				break; // End
			}
			$content[] = $line;
			unset($lines[$key]); // Remove line from the array
		}
		return implode(PHP_EOL, $content);
	}

	public function trimLine(string $line) : string {
		return preg_replace('/^\s*\*\s*/', '', trim($line));
	}

}