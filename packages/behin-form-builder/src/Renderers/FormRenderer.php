<?php

namespace MyFormBuilder\Renderers;

use MyFormBuilder\Fields\AbstractField;
use MyFormBuilder\Traits\HasAttributes;

class FormRenderer
{
    use HasAttributes;

    public function render(array $formAttributes, array $fields): string
    {
        // $formOpen = '<form ' . $this->buildAttributes($formAttributes) . '>';
        $formContent = '';

        foreach ($fields as $field) {
            $formContent .= $this->renderField($field);
        }

        return $formContent;
    }

    protected function renderField(AbstractField $field): string
    {
        $attributes = $field->getAttributes();
        $html = '<div class="form-group">';

        // Add label if it exists and it's not a submit button
        if (($attributes['type'] ?? '') !== 'submit' && isset($attributes['label'])) {
            $html .= sprintf(
                '<label for="%s">%s</label>',
                $attributes['id'] ?? $attributes['name'],
                $attributes['label']
            );
        }

        $html .= $field->render();
        $html .= '</div>';

        return $html;
    }
}
