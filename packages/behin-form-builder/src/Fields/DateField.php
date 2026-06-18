<?php

namespace MyFormBuilder\Fields;

class DateField extends AbstractField
{
    public function render(): string
    {
        $name = $this->name;
        $attributes = $this->attributes;
        $id = isset($attributes['id']) ? $attributes['id'] : $name;
        $showLabel = isset($attributes['showLabel']) ? $attributes['showLabel'] : true;
        $s = '<div class="form-group">';
        if ($showLabel) {
            $s .= '<label>';
            $s .= trans('fields.' . $this->name);
            if ($this->attributes['required'] == 'on' && $this->attributes['readonly'] != 'on') {
                $s .= ' <span class="text-danger">*</span>';
            }
            $s .= '</label>';
        }

        $s .= "<input type='text' name='$name' id='$id' dir='ltr'";


        foreach ($this->attributes as $key => $value) {
            if ($key == 'required') {
                if ($value == 'on') {
                    $s .= 'required ';
                }
            } elseif ($key == 'readonly') {
                if ($value == 'on') {
                    $s .= 'readonly ';
                }
            } else {
                $s .= $key . '="' . $value . '" ';
            }
        }
        $s .= '>';
        $s .= "<input type='hidden' name='" . $this->name . "_alt' id='" . $id . "_alt'>";
        $s .= "<script>
                    $('#$id').persianDatepicker({
                        viewMode: 'day',
                        initialValue: false,
                        format: 'YYYY-MM-DD',
                        initialValueType: 'persian',
                        altField: '#" . $id . "_alt',
                        calendar: {
                            persian: {
                                leapYearMode: 'astronomical',
                                locale: 'fa'
                            }
                        }
                    });
                    var dateInput = document.getElementById('$id');
                    // جلوگیری از تایپ کردن مستقیم در اینپوت
                    dateInput.addEventListener('keydown', function(event) {
                        event.preventDefault(); // جلوگیری از وقوع پیش‌فرض رویداد (یعنی تایپ کردن)
                    });
                </script>";
        $s .= '</div>';
        return $s;
        if (!isset($this->attributes['type'])) {
            $this->attributes['type'] = 'text';
        }
        return sprintf('<input %s>', $this->buildAttributes());
    }
}
