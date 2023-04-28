<?php

namespace Vagebnd\EnvatoThemecheckCli\Support;

use Illuminate\Support\Str;
use Vagebnd\EnvatoThemecheckCli\Enums\ErrorLevel;
use voku\helper\HtmlDomParser;

class Error
{
    protected HtmlDomParser $dom;

    protected ErrorLevel $level;

    protected string $message;

    public static function parse(string $error)
    {
        return (new static($error))->parseError(
            HtmlDomParser::str_get_html("<p>$error</p>"),
        );
    }

    private function parseError(HtmlDomParser $dom, ?string $filename = null)
    {
        $level = $dom->find('.tc-required,.tc-warning,.tc-info', 0)->text;

        $this->level = ErrorLevel::fromString($level);
        $this->message = trim(Str::after($dom->text(), "$level:"));

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
}
