<?php

namespace MyFormBuilder\Fields;

class TextareaField extends AbstractField
{
    public function render(): string
    {
        $s = '<div class="form-group">';
        $s .= '<label>';
        $s .= trans('fields.' . $this->name);
        if($this->attributes['required'] == 'on'  && $this->attributes['readonly'] != 'on'){
            $s .= ' <span class="text-danger">*</span>';
        }
        $s .= '</label>';
        $s .= '<textarea name="' . $this->name . '" class="form-control" rows="5" ';

        foreach($this->attributes as $key => $value){

            if($key == 'required'){
                if($value == 'on'){
                    $s .= 'required ';
                }
            }
            elseif($key == 'readonly'){
                if($value == 'on'){
                    $s .= 'readonly ';
                }
            }else{
                if($key != 'value'){
                    $s .= $key . '="' . $value . '" ';
                }
            }
        }
        $s .= '>' . $this->attributes['value'] . '</textarea>';
        $s .= '</div>';
        return $s;
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
