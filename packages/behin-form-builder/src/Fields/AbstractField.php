<?php

namespace MyFormBuilder\Fields;

abstract class AbstractField
{
    protected array $attributes = [];
    protected string $name;
    public function __construct(string $name, array $attributes = [])
    {
        $this->name = $name;
        $this->attributes = $attributes;
    }

    abstract public function render(): string;

    protected function buildAttributes(): string
    {
        return collect($this->attributes)
            ->filter()
            ->map(function ($value, $key) {
                // Handle boolean attributes
                if (is_bool($value)) {
                    return $value ? $key : '';
                }
                // Handle array attributes (like data-* attributes)
                if (is_array($value)) {
                    $value = json_encode($value);
                }
                return "{$key}=\"" . htmlspecialchars($value, ENT_QUOTES) . "\"";
            })
            ->filter()
            ->implode(' ');
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }
}
