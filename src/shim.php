<?php

use Illuminate\Support\Str;

/**
 * Shim for WordPress functions
 */
if (! function_exists('do_action')) {
    function do_action($action)
    {
        // do nothing
    }
}

if (! function_exists('__')) {
    function __($text, $domain)
    {
        return $text;
    }
}

if (! function_exists('sanitize_title_with_dashes')) {
    function sanitize_title_with_dashes($title = '')
    {
        if (empty($title)) {
            return '';
        }

        return Str::replace(' ', '-', $title);
    }
}

if (! function_exists('esc_html')) {
    function esc_html($text)
    {
        return $text;
    }
}

if (! function_exists('esc_html__')) {
    function esc_html__($text, $domain)
    {
        return $text;
    }
}
