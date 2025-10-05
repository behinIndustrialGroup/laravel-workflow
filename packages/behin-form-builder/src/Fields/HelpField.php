<?php

namespace MyFormBuilder\Fields;

class HelpField extends AbstractField
{
    public function render(): string
    {
        $s = '<div class="" ';
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
            elseif($key == 'script'){

            }
            else{
                $s .= $key . '="' . $value . '" ';
            }
        }
        $s .= '>';
        $s .= '<p style="margin: 0">';
        $s .= $this->attributes['options'] ?? '';
        $s .= '</p>';
        if(isset($this->attributes['script'])){
            $s .= '<script>';
            $s .= $this->attributes['script'];
            $s .= '</script>';
        }
        $s .= '</div>';
        return $s;
    }
}
