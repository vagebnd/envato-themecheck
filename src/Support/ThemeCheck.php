<?php

namespace Vagebond\EnvatoThemecheck\Support;

use Composer\Autoload\ClassLoader;
use Symfony\Component\Finder\Finder;

class ThemeCheck
{
    protected static self $instance;

    protected string $source;

    public static function make(string $source)
    {
        return static::$instance = new static($source);
    }

    public function __construct(string $source)
    {
        $this->source = $source;
    }

    public function run(ClassLoader $classLoader, bool $isDev = false)
    {
        $checks = CheckFactory::getAllChecks($classLoader);

        return $checks->map(fn ($check) => $check->run($this->source, $isDev));
    }
}
