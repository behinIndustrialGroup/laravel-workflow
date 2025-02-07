<?php

namespace MyFormBuilder\Fields;

use Illuminate\Support\Facades\DB;

class SelectMultipleField extends AbstractField
{
    public function render(): string
    {
        $s = '<div class="form-group">';
        $s .= '<label>';
        $s .= trans('fields.' . $this->name);
        if($this->attributes['required'] == 'on' && $this->attributes['readonly'] != 'on'){
            $s .= ' <span class="text-danger">*</span>';
        }
        $s .= '</label>';
        $s .= '<select name="' . $this->name . '[]" class="form-control select2" multiple="multiple" ';
        foreach ($this->attributes as $key => $value) {
            if ($key == 'required') {
                if ($value == 'on') {
                    $s .= 'required ';
                }
            } elseif ($key == 'readonly') {
                if ($value == 'on') {
                    $s .= 'disabled ';
                }
            }
            elseif ($key == 'value') {

            } 
            else {
                $s .= $key . '="' . $value . '" ';
            }
        }
        $s .= '>';
        if (($this->attributes['query'])) {
            $sqlOptions = DB::select($this->attributes['query']);
            foreach ($sqlOptions as $option) {
                $values = is_array($this->attributes['value']) ? $this->attributes['value'] : [];
                $selected = in_array($option->value, $values) ? 'selected' : '';
                $s .= "<option value='$option->value' $selected >$option->label</option>";
            }
        }
        if (is_string($this->attributes['options'])) {
            $options = explode("\r\n", $this->attributes['options']);
            foreach ($options as $option) {
                $values = is_array($this->attributes['value']) ? $this->attributes['value'] : [];
                $selected = in_array($option, $values) ? 'selected' : '';
                $s .= "<option value='$option' $selected >$option</option>";
            }
        }

        $s .= '</select>';
        $s .= '</div>';
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
