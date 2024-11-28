<?php

namespace MyFormBuilder\Fields;

class SelectField extends AbstractField
{
    public function render(): string
    {
        $s= '<label>';
        $s .= trans('SimpleWorkflowLang::fields.' . $this->name);
        $s .= '</label>';
        $s .= '<select name="' . $this->name . '" ';
        foreach($this->attributes['options'] as $key => $value){
            $s .= $key . '="' . $value . '" ';
        }
        $s .= '>';
        return $s;
        $attributes = $this->attributes;
        $options = $attributes['options'] ?? [];
        unset($attributes['options'], $attributes['type']);

        $html = sprintf('<select %s>', $this->buildAttributes());

        foreach ($options as $value => $label) {
            $selected = isset($attributes['value']) && $attributes['value'] == $value ? ' selected' : '';
            $html .= sprintf(
                '<option value="%s"%s>%s</option>',
                htmlspecialchars($value, ENT_QUOTES),
                $selected,
                htmlspecialchars($label, ENT_QUOTES)
            );
        }

        return $html . '</select>';
    }
}
