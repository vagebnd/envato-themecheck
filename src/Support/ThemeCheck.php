<?php

namespace Vagebnd\EnvatoThemecheckCli\Support;

use Composer\Autoload\ClassLoader;
use Symfony\Component\Finder\Finder;

class ThemeCheck
{
    protected static self $instance;

    protected string $source;

    protected Finder $phpFiles;

    protected Finder $cssFiles;

    protected Finder $otherFiles;

    public static function make(string $source)
    {
        return static::$instance = new static($source);
    }

    public function __construct(string $source)
    {
        $this->source = $source;
    }

    public function run(ClassLoader $classLoader)
    {
        $checks = CheckFactory::getAllChecks($classLoader);

        return $checks->map(fn ($check) => $check->run($this->source));
    }
}
