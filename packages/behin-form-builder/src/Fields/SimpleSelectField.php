<?php

namespace MyFormBuilder\Fields;

use Illuminate\Support\Facades\DB;

class SimpleSelectField extends AbstractField
{
    public function render(): string
    {
        $s = '<div class="form-group">';
        $s .= '<label>';
        $s .= trans('fields.' . $this->name);
        if ($this->attributes['required'] == 'on' && $this->attributes['readonly'] != 'on') {
            $s .= ' <span class="text-danger">*</span>';
        }
        $s .= '</label>';
        $s .= '<select name="' . $this->name . '" class="form-control" ';
        foreach ($this->attributes as $key => $value) {
            if ($key == 'required') {
                if ($value == 'on') {
                    $s .= 'required ';
                }
            } elseif ($key == 'readonly') {
                if ($value == 'on') {
                    $s .= 'disabled ';
                }
            } elseif ($key == 'script') {
                
            } else {
                $s .= $key . '="' . $value . '" ';
            }
        }
        $s .= '>';
        $s .= '<option value="">' . trans('fields.Select') . '</option>';

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
        if (isset($this->attributes['script'])) {
            $s .= '<script>';
            $s .= $this->attributes['script'];
            $s .= '</script>';
        }
        $s .= '</div>';
        return $s;
    }
}

