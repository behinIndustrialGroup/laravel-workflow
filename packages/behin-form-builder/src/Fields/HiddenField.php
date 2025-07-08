<?php

namespace MyFormBuilder\Fields;

use Illuminate\Support\Facades\DB;

class HiddenField extends AbstractField
{
    public function render(): string
    {
        $s = '<div class="form-group">';
        $s .= '<input type="hidden" name="' . $this->name . '" ';
        if(isset($this->attributes['value'])){
            $s .= 'value="' . $this->attributes['value'] . '" ';
        }
        $s .= '</div>';
        return $s;
    }
}
