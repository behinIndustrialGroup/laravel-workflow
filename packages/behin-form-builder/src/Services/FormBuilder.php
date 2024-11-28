<?php

namespace MyFormBuilder\Services;

use MyFormBuilder\Contracts\FormBuilderInterface;
use MyFormBuilder\Fields\DateField;
use MyFormBuilder\Fields\TextField;
use MyFormBuilder\Fields\EmailField;
use MyFormBuilder\Fields\SelectField;
use MyFormBuilder\Fields\TextareaField;
use MyFormBuilder\Fields\SubmitField;
use MyFormBuilder\Fields\FieldFactory;
use MyFormBuilder\Renderers\FormRenderer;

class FormBuilder
{
    protected array $attributes = [];
    protected array $fields = [];
    protected FieldFactory $fieldFactory;
    protected FormRenderer $renderer;

    public function __construct()
    {
        $this->fieldFactory = new FieldFactory();
        $this->renderer = new FormRenderer();
    }

    public function open(array $attributes = []): self
    {
        $this->attributes = array_merge([
            'method' => 'POST',
            'class' => 'form-builder',
        ], $attributes);

        return $this;
    }

    public function text(string $name, array $attributes = null)
    {

        $attributes = $attributes ?? [];
        // $field = $this->fieldFactory->create('text', $name, $attributes);
        return (new TextField($name, $attributes))->render();
        return $this;
    }

    public function date(string $name, array $attributes = null)
    {

        $attributes = $attributes ?? [];
        // $field = $this->fieldFactory->create('text', $name, $attributes);
        return (new DateField($name, $attributes))->render();
        return $this;
    }

    public function email($name, $attributes = null): self
    {
        if (is_array($name)) {
            $attributes = $name;
            $name = $attributes['name'] ?? '';
            unset($attributes['name']);
        }

        $attributes = $attributes ?? [];
        $field = $this->fieldFactory->create('email', $name, $attributes);
        $this->fields[] = new EmailField($name, $field);
        return $this;
    }

    public function textarea($name, $attributes = null): self
    {
        if (is_array($name)) {
            $attributes = $name;
            $name = $attributes['name'] ?? '';
            unset($attributes['name']);
        }

        $attributes = $attributes ?? [];
        $field = $this->fieldFactory->create('textarea', $name, $attributes);
        $this->fields[] = new TextareaField($name, $field);
        return $this;
    }

    public function select($name, $options = [], $attributes = []): self
    {
        if (is_array($name)) {
            $attributes = $name;
            $name = $attributes['name'] ?? '';
            $options = $attributes['options'] ?? [];
            unset($attributes['name'], $attributes['options']);
        }

        $attributes['options'] = $options;
        $field = $this->fieldFactory->create('select', $name, $attributes);
        $this->fields[] = new SelectField($name, $field);
        return $this;
    }

    public function submit($text = 'Submit', $attributes = []): self
    {
        if (is_array($text)) {
            $attributes = $text;
            $text = $attributes['value'] ?? 'Submit';
            unset($attributes['value']);
        }

        $attributes['type'] = 'submit';
        $attributes['value'] = $text;
        $field = $this->fieldFactory->create('submit', 'submit', $attributes);
        $this->fields[] = new SubmitField('submit', $field);
        return $this;
    }

    public function render(): string
    {
        return $this->renderer->render($this->attributes, $this->fields);
    }

    public function __toString(): string
    {
        return $this->render();
    }
}
