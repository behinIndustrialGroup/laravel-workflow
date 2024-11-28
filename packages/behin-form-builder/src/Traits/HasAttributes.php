<?php

namespace MyFormBuilder\Traits;

trait HasAttributes
{
    protected function buildAttributes(array $attributes): string
    {
        return collect($attributes)
            ->map(function ($value, $key) {
                return "{$key}=\"{$value}\"";
            })
            ->implode(' ');
    }

    protected function mergeAttributes(array $default, array $custom): array
    {
        return array_merge($default, $custom);
    }
}