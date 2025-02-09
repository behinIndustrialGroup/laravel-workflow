<?php

namespace MyFormBuilder\Fields;

class ButtonField extends AbstractField
{
    public function render(): string
    {
        $s = '<button class="btn '. $this->attributes['class'] .'" id="'. $this->attributes['id'] .'">';
        $s .= trans('fields.' . $this->name);
        $s .= '</button>';
        return $s;
    }
}
