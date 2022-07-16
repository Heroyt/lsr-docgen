<?php

namespace Lsr\Doc\Extensions;

use JetBrains\PhpStorm\ArrayShape;
use Lsr\Doc\Config\Config;
use Lsr\Doc\DocBlock\Tags\BriefTag;
use Lsr\Doc\DocBlock\Tags\DetailsTag;
use Lsr\Doc\DocBlock\Tags\Doxygen\AttentionTag;
use Lsr\Doc\DocBlock\Tags\Doxygen\PostTag;
use Lsr\Doc\DocBlock\Tags\Doxygen\PreTag;
use Lsr\Doc\DocBlock\Tags\Doxygen\WarningTag;
use Lsr\Doc\DocBlock\Tags\Psr19\ApiTag;
use Lsr\Doc\DocBlock\Tags\Psr19\AuthorTag;
use Lsr\Doc\DocBlock\Tags\Psr19\CopyrightTag;
use Lsr\Doc\DocBlock\Tags\Psr19\DeprecatedTag;
use Lsr\Doc\DocBlock\Tags\Psr19\GeneratedTag;
use Lsr\Doc\DocBlock\Tags\Psr19\InternalTag;
use Lsr\Doc\DocBlock\Tags\Psr19\LinkTag;
use Lsr\Doc\DocBlock\Tags\Psr19\MethodTag;
use Lsr\Doc\DocBlock\Tags\Psr19\PackageTag;
use Lsr\Doc\DocBlock\Tags\Psr19\ParamTag;
use Lsr\Doc\DocBlock\Tags\Psr19\PropertyTag;
use Lsr\Doc\DocBlock\Tags\Psr19\ReturnTag;
use Lsr\Doc\DocBlock\Tags\Psr19\SeeTag;
use Lsr\Doc\DocBlock\Tags\Psr19\SinceTag;
use Lsr\Doc\DocBlock\Tags\Psr19\ThrowsTag;
use Lsr\Doc\DocBlock\Tags\Psr19\TodoTag;
use Lsr\Doc\DocBlock\Tags\Psr19\UsesTag;
use Lsr\Doc\DocBlock\Tags\Psr19\VarTag;
use Lsr\Doc\DocBlock\Tags\Psr19\VersionTag;
use Lsr\Doc\DocBlock\Tags\Tag;
use Lsr\Doc\Symbols\ClassSymbol;
use Lsr\Doc\Symbols\FunctionSymbol;
use Lsr\Doc\Symbols\InterfaceSymbol;
use Lsr\Doc\Symbols\TraitSymbol;

class BaseExtension implements SymbolExtension, TagExtension
{

	public function __construct(
		protected readonly Config $config
	) {
	}

	/**
	 * @inheritDoc
	 */
	public function getSymbols() : array {
		return [
			ClassSymbol::class,
			FunctionSymbol::class,
			TraitSymbol::class,
			InterfaceSymbol::class,
		];
	}

	/**
	 * Get available tags
	 *
	 * Contains tags from a PSR-19 Specification.
	 *
	 * @link https://github.com/php-fig/fig-standards/blob/master/proposed/phpdoc-tags.md
	 *
	 * @return array<string, string|Tag> Associative array of tag names => tag classes
	 */
	public function getTags() : array {
		return [
			// Basic description tags
			'brief'      => BriefTag::class,
			'details'    => DetailsTag::class,

			// PSR-19 tags
			'api'        => ApiTag::class,
			'author'     => AuthorTag::class,
			'authors'    => AuthorTag::class,
			'copyright'  => CopyrightTag::class,
			'deprecated' => DeprecatedTag::class,
			'generated'  => GeneratedTag::class,
			'internal'   => InternalTag::class,
			'link'       => LinkTag::class,
			'method'     => MethodTag::class,
			'package'    => PackageTag::class,
			'param'      => ParamTag::class,
			'property'   => PropertyTag::class,
			'return'     => ReturnTag::class,
			'see'        => SeeTag::class,
			'since'      => SinceTag::class,
			'throws'     => ThrowsTag::class,
			'todo'       => TodoTag::class,
			'uses'       => UsesTag::class,
			'var'        => VarTag::class,
			'version'    => VersionTag::class,

			// Doxygen tags
			'attention'  => AttentionTag::class,
			'post'       => PostTag::class,
			'pre'        => PreTag::class,
			'warning'    => WarningTag::class,
			// TODO: Implement more doxygen tags
		];
	}
}