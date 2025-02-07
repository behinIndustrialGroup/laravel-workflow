<?php

namespace MyFormBuilder\Fields;

use Illuminate\Support\Facades\DB;

class SelectField extends AbstractField
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
        $s .= '<select name="' . $this->name . '" class="form-control select2" ';
        foreach ($this->attributes as $key => $value) {
            if ($key == 'required') {
                if ($value == 'on') {
                    $s .= 'required ';
                }
            } elseif ($key == 'readonly') {
                if ($value == 'on') {
                    $s .= 'disabled ';
                }
            } else {
                $s .= $key . '="' . $value . '" ';
            }
        }
        $s .= '>';
        $s .= '<option value="">'. trans('fields.Select') .'</option>';

        if (($this->attributes['query'])) {
            $sqlOptions = DB::select($this->attributes['query']);
            foreach ($sqlOptions as $option) {
                $selected = $this->attributes['value'] == $option->value ? 'selected' : '';
                $s .= "<option value='$option->value' $selected >$option->label</option>";
            }
        }
        if (is_string($this->attributes['options'])) {
            $options = explode("\r\n", $this->attributes['options']);
            foreach ($options as $value) {
                $selected = $this->attributes['value'] == $value ? 'selected' : '';
                $s .= "<option value='$value' $selected >$value</option>";
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
