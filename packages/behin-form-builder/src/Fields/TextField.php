<?php

namespace MyFormBuilder\Fields;

use Illuminate\Support\Facades\DB;

class TextField extends AbstractField
{
    public function render(): string
    {
        $readonly = $this->attributes['readonly'] ?? '';
        $s = '<div class="form-group">';
        $s .= '<label>';
        $s .= trans('fields.' . $this->name);
        if($this->attributes['required'] == 'on' && $readonly != 'on'){
            $s .= ' <span class="text-danger">*</span>';
        }
        $s .= '</label>';
        $s .= '<input type="text" name="' . $this->name . '" ';
        if(isset($this->attributes['datalist_from_database'])){
            $s .= 'list="' . $this->name . '_list" ';
        }
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
            }
            elseif($key == 'script' || $key == 'datalist_from_database'){

            }
            else{
                $s .= $key . '="' . $value . '" ';
            }
        }
        $s .= '>';
        if(isset($this->attributes['script'])){
            $s .= '<script>';
            $s .= $this->attributes['script'];
            $s .= '</script>';
        }
        if(isset($this->attributes['datalist_from_database'])){
            $s .= '<datalist id="' . $this->name . '_list">';
            $sqlOptions = DB::select($this->attributes['datalist_from_database']);
            foreach ($sqlOptions as $option) {
                $s .= "<option value='$option->value'>$option->label</option>";
            }
            $s .= '</datalist>';
        }
        $s .= '</div>';
        return $s;
        if (!isset($this->attributes['type'])) {
            $this->attributes['type'] = 'text';
        }
        return sprintf('<input %s>', $this->buildAttributes());
    }
}
