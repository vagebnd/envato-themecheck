<?php

namespace Vagebnd\EnvatoThemecheckCli\Support;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use themecheck;
use Vagebnd\EnvatoThemecheckCli\Enums\ErrorLevel;

class Check
{
    protected themecheck $check;

    public string|null $path = null;

    public function __construct(string $check)
    {
        $this->check = new $check;
    }

    public function run(string $source)
    {
        // Suppress warnings from the theme check.
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
        return collect(Arr::wrap($this->check->getError()))
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
        // We need to ignore the envato theme check itself.
        // and our own vendor.
        return Finder::create()
            ->files()
            ->notName(['*.zip', '*.svg'])
            ->exclude([
                'vendor', // For now,
                'envato',
                'node_modules',
            ])
            ->in($source);
    }
}
