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
            }else{
                $s .= $key . '="' . $value . '" ';
            }
        }
        $s .= '>';
        $s .= trans($this->name);
        $s .= '<p>';
        $s .= $this->attributes['options'] ?? '';
        $s .= '</p>';

        $s .= '</div>';
        return $s;
        if (!isset($this->attributes['type'])) {
            $this->attributes['type'] = 'text';
        }
        return sprintf('<input %s>', $this->buildAttributes());
    }
}
