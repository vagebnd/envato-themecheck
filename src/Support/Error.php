<?php

namespace Vagebnd\EnvatoThemecheckCli\Support;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Vagebnd\EnvatoThemecheckCli\Enums\ErrorLevel;
use voku\helper\HtmlDomParser;

class Error
{
    protected HtmlDomParser $dom;

    protected ErrorLevel $level;

    protected string $message;

    protected string|null $file = null;

    protected int|null $line = null;

    public static function parse(string $error)
    {
        return (new static($error))->parseError(
            HtmlDomParser::str_get_html("<p>$error</p>"),
        );
    }

    private function parseError(HtmlDomParser $dom)
    {
        $level = $dom->find('.tc-required,.tc-warning,.tc-info', 0)->text;

        $this->level = ErrorLevel::fromString($level);
        $this->message = html_entity_decode(trim(Str::after($dom->text(), "$level:")));

        // Check if the error has a file
        if (preg_match('/\bfile\s+(\S+)/i', $this->message, $matches)) {
            $this->file = Arr::get($matches, 1);
        }

        // Check if the error has a line number
        if (preg_match('/\bline\s+(\d+)/i', $this->message, $matches)) {
            $this->line = Arr::get($matches, 1);
        }

        return $this;
    }

    public function getLevel(): ErrorLevel
    {
        return $this->level;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getFile(): string|null
    {
        return $this->file;
    }

    public function getLine(): int|null
    {
        return $this->line;
    }
}
