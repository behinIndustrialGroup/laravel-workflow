<?php

namespace MyFormBuilder\Fields;

class SubmitField extends AbstractField
{
    public function render(): string
    {
        $this->attributes['type'] = 'submit';
        return sprintf('<input %s>', $this->buildAttributes());
    }
}