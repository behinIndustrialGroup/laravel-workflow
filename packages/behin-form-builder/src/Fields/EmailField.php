<?php

namespace MyFormBuilder\Fields;

class EmailField extends AbstractField
{
    public function render(): string
    {
        $this->attributes['type'] = 'email';
        return sprintf('<input %s>', $this->buildAttributes());
    }
}