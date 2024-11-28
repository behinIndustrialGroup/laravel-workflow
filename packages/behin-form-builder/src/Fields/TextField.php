<?php

namespace MyFormBuilder\Fields;

class TextField extends AbstractField
{
    public function render(): string
    {
        $s= '<label>';
        $s .= trans('SimpleWorkflowLang::fields.' . $this->name);
        $s .= '</label>';
        $s .= '<input type="text" name="' . $this->name . '" ';

        foreach($this->attributes as $key => $value){
            $s .= $key . '="' . $value . '" ';
        }
        $s .= '>';
        return $s;
        if (!isset($this->attributes['type'])) {
            $this->attributes['type'] = 'text';
        }
        return sprintf('<input %s>', $this->buildAttributes());
    }
}
