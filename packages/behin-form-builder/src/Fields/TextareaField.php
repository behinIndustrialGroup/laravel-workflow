<?php

namespace MyFormBuilder\Fields;

class TextareaField extends AbstractField
{
    public function render(): string
    {
        $attributes = $this->attributes;
        $value = $attributes['value'] ?? '';
        unset($attributes['value'], $attributes['type']);

        return sprintf(
            '<textarea %s>%s</textarea>',
            $this->buildAttributes(),
            htmlspecialchars($value, ENT_QUOTES)
        );
    }
}