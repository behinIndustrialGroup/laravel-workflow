<?php

namespace MyFormBuilder\Fields;

class CheckboxField extends AbstractField
{
    public function render(): string
    {
        $s = '<div class="form-group">';

        $s .= '<label style="cursor: pointer;">';
        $s .= "<input type=\"checkbox\" name=\"" . $this->name . "_alt\" ";
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
            }elseif($key == 'value'){
                if($value == 'on'){
                    $s .= 'checked ';
                }
            }
            else{
                $s .= $key . '="' . $value . '" ';
            }
        }
        $s .= '>&nbsp;';
        $s .= trans('fields.' . $this->name);
        if($this->attributes['required'] == 'on' && $this->attributes['readonly'] != 'on'){
            $s .= ' <span class="text-danger">*</span>';
        }
        $s .= '</label>';
        $s .= '<input type="hidden" name="' . $this->name . '" value="' . $this->attributes['value'] . '">';



        $s .= '<script>
            $("input[type=checkbox][name=' . $this->name . '_alt]").on("change", function() {
                if(this.checked) {
                    $("input[type=hidden][name=' . $this->name . ']").val("on");
                } else {
                    $("input[type=hidden][name=' . $this->name . ']").val("off");
                }
            });
        </script>';
        $s .= '</div>';
        return $s;
        if (!isset($this->attributes['type'])) {
            $this->attributes['type'] = 'text';
        }
        return sprintf('<input %s>', $this->buildAttributes());
    }
}
