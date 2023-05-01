<?php

namespace Vagebond\EnvatoThemecheck\Support;

use themecheck;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Vagebond\EnvatoThemecheck\Enums\ErrorLevel;

class Check
{
    protected themecheck $check;

    protected bool $isDev = false;

    public string|null $path = null;

    public function __construct(string $check)
    {
        $this->check = new $check;
    }

    public function run(string $source, bool $isDev = false)
    {
        $this->isDev = $isDev;

        // use @ to Suppress warnings from the theme check.
        @$this->check->check($this->getPhpFiles($source), $this->getCssFiles($source), $this->getOtherFiles($source));

        return $this;
    }

    public function getErrors()
    {
        return $this->getOutput()->filter(fn ($error) => $error->getLevel() === ErrorLevel::REQUIRED);
    }

    public function getWarnings()
    {
        return $this->getOutput()->filter(fn ($error) => $error->getLevel() === ErrorLevel::WARNING);
    }

    public function getInfos()
    {
        return $this->getOutput()->filter(fn ($error) => $error->getLevel() === ErrorLevel::INFO);
    }

    public function getOutput()
    {
        return Collection::make(Arr::wrap($this->check->getError()))
            ->filter()
            ->map(fn ($error) => Error::parse($error))
            ->unique(fn ($error) => $error->getMessage());
    }

    public function hasErrors()
    {
        return count($this->check->getError()) > 0;
    }

    private function getPhpFiles(string $source)
    {
        return Collection::make(iterator_to_array($this->makeFinder($source)->name('*.php')->notContains('tgm-plugin-activation')))
            ->map(fn (SplFileInfo $file) => php_strip_whitespace($file->getRealPath()))
            ->toArray();
    }

    private function getCssFiles(string $source)
    {
        return Collection::make(iterator_to_array($this->makeFinder($source)->name('*.css')))
            ->map(fn (SplFileInfo $file) => $file->getContents())
            ->toArray();
    }

    private function getOtherFiles(string $source)
    {
        return Collection::make(iterator_to_array($this->makeFinder($source)->notName('*.php')->notName('*.css')))
            ->map(fn (SplFileInfo $file) => $file->getContents())
            ->toArray();
    }

    private function makeFinder(string $source)
    {
        $excludes = Collection::make([
            'node_modules',
            'wordpress-stubs'
        ]);

        // Exclude dev dependencies from the theme check when in development mode.
        if ($this->isDev) {
            $excludes = $excludes->merge($this->getDevDependencies($source));
        }

        $finder = Finder::create()
            ->in($source)
            ->exclude($excludes->toArray())
            ->notName(['*.zip', '*.svg', 'XdebugHandler.php'])
            ->files();

        if ($this->isDev) {
            $finder
                ->notName(['wordpress-stubs'])
                ->ignoreDotFiles(true);
        }

        return $finder;
    }

    private function getDevDependencies(string $source)
    {
        // Get all the dev dependencies from all composer.json files
        $finder = Finder::create()
            ->in($source)
            ->followLinks()
            ->name('composer.json')
            ->files();

        return Collection::make(iterator_to_array($finder))
            ->map(fn (SplFileInfo $file) => json_decode($file->getContents(), true)['require-dev'])
            ->filter()
            ->map(fn (array $composerJson) => array_keys($composerJson))
            ->values()
            ->flatten()
            ->unique()
            ->filter(fn ($value) => Str::contains($value, '/'));
    }
}
