<?php

namespace MyFormBuilder\Fields;

class FieldFactory
{
    public function create(string $type, string $name, array $attributes = []): array
    {
        $defaults = [
            'type' => $type,
            'name' => $name,
            'id' => $name,
            'class' => 'form-control',
        ];

        // Handle special attributes
        if (isset($attributes['value'])) {
            $defaults['value'] = $attributes['value'];
        }

        if (isset($attributes['placeholder'])) {
            $defaults['placeholder'] = $attributes['placeholder'];
        }

        if (isset($attributes['class'])) {
            $defaults['class'] = trim($defaults['class'] . ' ' . $attributes['class']);
            unset($attributes['class']);
        }

        // Remove null or empty string values from attributes
        $attributes = array_filter($attributes, function ($value) {
            return $value !== null && $value !== '';
        });

        return array_merge($defaults, $attributes);
    }
}