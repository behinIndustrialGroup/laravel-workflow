<?php

namespace MyFormBuilder\Fields;

class DateTimeField extends AbstractField
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
        $s .= '<input type="text" name="' . $this->name . '" ';

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
        $s .= "<input type='hidden' name='". $this->name ."_alt' id='". $this->name ."_alt' value='". $this->attributes['altValue'] ."'>";
        $s .= "<script>$('#$this->name').persianDatepicker({
                viewMode: 'day',
                initialValue: false,
                format: 'YYYY-MM-DD HH:mm',
                timePicker: { enabled: true, second: { enabled: false } },
                initialValueType: 'persian',
                altField: '#". $this->name ."_alt',
                calendar: {
                    persian: {
                        leapYearMode: 'astronomical',
                        locale: 'fa'
                    }
                }
            });</script>";
        $s .= '</div>';
        return $s;
    }
}
