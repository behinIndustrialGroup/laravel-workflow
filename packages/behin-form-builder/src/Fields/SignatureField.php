<?php

namespace MyFormBuilder\Fields;

use Illuminate\Support\Facades\DB;

class SignatureField extends AbstractField
{
    public function render(): string
    {
        $s = '<div class="form-group">';
        $s .= '<label>';
        $s .= trans('fields.' . $this->name);
        if ($this->attributes['required'] == 'on' && $this->attributes['readonly'] != 'on') {
            $s .= ' <span class="text-danger">*</span>';
        }
        $s .= '</label><br>';
        if (isset($this->attributes['value'])) {
            $s .= '<br>';
            $s .= '<a href="' . $this->attributes['value'] . '" target="_blank" download>' . trans('fields.Download') . ' ' . trans('fields.' . $this->name) . '</a><br>';
        }
        if($this->attributes['readonly'] == 'on'){
            $s .= '<img src="' . $this->attributes['value'] . '" alt="">';
        }
        if ($this->attributes['readonly'] != 'on') {
            $s .= '<canvas id="' . $this->name . '_canvas"';
            foreach ($this->attributes as $key => $value) {
                if ($key == 'style') {
                    $s .= $key . '="' . $value . '" ';
                }
            }
            $s .= '>';
            $s .= '</canvas>';
            $s .= '<br>';
            $s .= '<button type="button" class="btn btn-success btn-sm" id="' . $this->name . '_save" onclick="setSignature' . $this->name . '()">'. trans('fields.Set') .'</button>';
            $s .= '<button type="button" class="btn btn-danger btn-sm" id="' . $this->name . '_clear" onclick="signaturePad' . $this->name . '.clear()">'. trans('fields.Clear') .'</button>';
            
            $s .= '<input type="hidden" name="' . $this->name . '" id="' . $this->name . '" ';
            if(isset($this->attributes['value'])){
                $s .= 'value="' . $this->attributes['value'] . '"';
            }
            $s .= '>';
            $s .= '<script>';
            $s .= 'var signaturePad' . $this->name . ' = new SignaturePad(document.getElementById("' . $this->name . '_canvas"));';
            $s .= 'function setSignature' . $this->name . '() {';
            $s .= "document.getElementById('" . $this->name . "').value = signaturePad" . $this->name . ".toDataURL();";
            $s .= '}';
            $s .= '</script>';
            if (isset($this->attributes['script'])) {
                $s .= '<script>';
                $s .= $this->attributes['script'];
                $s .= '</script>';
            }
        }

        $s .= '</div>';
        return $s;
    }
}
