<?php

namespace Vagebond\EnvatoThemecheck\Enums;

enum ErrorLevel
{
    case REQUIRED;
    case WARNING;
    case INFO;

    public static function fromString(string $level): self
    {
        return match (strtolower($level)) {
            'required' => self::REQUIRED,
            'warning' => self::WARNING,
            'info' => self::INFO,
            default => throw new \InvalidArgumentException('Invalid error level'),
        };
    }
}
