<?php

namespace MyFormBuilder\Fields;

class FileField extends AbstractField
{
    public function render(): string
    {
        $s = '<div class="form-group">';
        $s .= '<label>';
        $s .= trans('fields.' . $this->name);
        if($this->attributes['required'] == 'on' && $this->attributes['readonly'] != 'on'){
            $s .= ' <span class="text-danger">*</span>';
        }
        $s .= '</label><br>';
        foreach($this->attributes['value'] as $value){
            $s .= '<a href="' . url('public/' . $value) . '" target="_blank" download>' . trans('fields.Download') . '</a><br>';
        }
        if($this->attributes['readonly'] == 'on'){
            $s .= '<input type="file" multiple name="' . $this->name . '" disabled>';
        }else{
            $s .= '<input type="file" multiple name="' . $this->name . '" ';
            foreach($this->attributes as $key => $value){
                if($key != 'value'){
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
                        $s .= $key . '="' . $value . '" ';
                    }
                }
            }
            $s .= '>';
        }


        $s .= '</div>';
        return $s;
        if (!isset($this->attributes['type'])) {
            $this->attributes['type'] = 'text';
        }
        return sprintf('<input %s>', $this->buildAttributes());
    }
}
