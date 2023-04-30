<?php

namespace Vagebond\EnvatoThemecheck\Support;

use Composer\Autoload\ClassLoader;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class CheckFactory
{
    public static function getAllChecks(ClassLoader $classLoader)
    {
        $checks = Collection::make($classLoader->getClassMap())
            ->filter(fn ($path) => Str::contains($path, 'envato-theme-check'))
            ->keys()
            ->map(fn ($class) => new Check($class));

        return $checks;
    }
}
