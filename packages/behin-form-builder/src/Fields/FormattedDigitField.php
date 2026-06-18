<?php

namespace MyFormBuilder\Fields;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FormattedDigitField extends AbstractField
{
    public function render(): string
    {
        $attributes = $this->attributes;
        $readonly = $this->attributes['readonly'] ?? '';
        $isPrice = $this->attributes['isPrice'] ?? false;
        $showLabel = isset($attributes['showLabel']) ? $attributes['showLabel'] : true;
        $s = '<div class="form-group">';
        if ($showLabel) {
            $s .= '<label>';
            $s .= trans('fields.' . $this->name);
            $s .= $showLabel;
            if ($this->attributes['required'] == 'on' && $this->attributes['readonly'] != 'on') {
                $s .= ' <span class="text-danger">*</span>';
            }
            $s .= '</label>';
        }

        // شروع ساخت input
        $s .= '<input type="text" name="' . $this->name . '" ';
        $s .= 'list="' . $this->name . '_list" ';

        // اگر کلاس خاصی برای AutoNumeric نیاز داریم اضافه کنیم
        $s .= 'class="form-control formatted-digit" inputmode="numeric" pattern="[0-9]*"';

        // ویژگی‌های دیگر input
        foreach ($this->attributes as $key => $value) {
            switch ($key) {
                case 'required':
                    if ($value === 'on') {
                        $s .= 'required ';
                    }
                    break;

                case 'readonly':
                    if ($value === 'on') {
                        $s .= 'readonly ';
                    }
                    break;

                case 'script':
                case 'datalist_from_database':
                case 'isPrice':
                    // این موارد مستقیماً به input اعمال نمی‌شوند
                    break;

                default:
                    $s .= $key . '="' . htmlspecialchars($value, ENT_QUOTES) . '" ';
                    break;
            }
        }

        $s .= '>';

        // اسکریپت دلخواه برای این فیلد
        if (isset($this->attributes['script'])) {
            $s .= '<script>' . $this->attributes['script'] . '</script>';
        }

        // datalist از پایگاه داده
        if (isset($this->attributes['datalist_from_database'])) {
            $s .= '<datalist id="' . $this->name . '_list">';
            $sqlOptions = DB::select($this->attributes['datalist_from_database']);
            foreach ($sqlOptions as $option) {
                $value = htmlspecialchars($option->value ?? '', ENT_QUOTES);
                $label = htmlspecialchars($option->label ?? '', ENT_QUOTES);
                $s .= "<option value=\"$value\">$label</option>";
            }
            $s .= '</datalist>';
        }

        $s .= '</div>';

        return $s;
    }
}
