<?php

namespace Lsr\Doc\Render;

use Latte\Engine;
use Lsr\Doc\Config\Config;
use function Lsr\Doc\trailingSlashIt;

class HtmlRenderer
{

    public const DEFAULT_THEME = 'basic';
    public const DEFAULT_TEMPLATE_DIR = __DIR__ . '../../templates/';

    protected Engine $latte;

    public function __construct(
        protected readonly Config $config
    )
    {
        $this->latte = new Engine();
        $this->latte->setTempDirectory($this->config->cacheDir);
    }

    public function locateTemplate(string $name): string
    {
        if (!str_ends_with($name, '.latte')) {
            $name .= '.latte';
        }
        if (!empty($this->config->customTemplateDir)) {
            $path = trailingSlashIt($this->config->customTemplateDir) . $name;
            if (file_exists($path) && is_readable($path)) {
                return $path;
            }
        }

        $path = self::DEFAULT_TEMPLATE_DIR . $this->config->theme . '/' . $name;
        if (!file_exists($path) || !is_readable($path)) {
            throw new \RuntimeException('Cannot find template file - ' . $name);
        }
        return $path;
    }

}