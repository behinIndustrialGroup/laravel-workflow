<?php

namespace MyFormBuilder\Services;

use MyFormBuilder\Contracts\FormBuilderInterface;
use MyFormBuilder\Fields\ButtonField;
use MyFormBuilder\Fields\DateField;
use MyFormBuilder\Fields\TextField;
use MyFormBuilder\Fields\EmailField;
use MyFormBuilder\Fields\SelectField;
use MyFormBuilder\Fields\TextareaField;
use MyFormBuilder\Fields\SubmitField;
use MyFormBuilder\Fields\FieldFactory;
use MyFormBuilder\Fields\FileField;
use MyFormBuilder\Fields\CheckboxField;
use MyFormBuilder\Fields\DivField;
use MyFormBuilder\Fields\EntityField;
use MyFormBuilder\Fields\HelpField;
use MyFormBuilder\Fields\LocationField;
use MyFormBuilder\Fields\TitleField;
use MyFormBuilder\Renderers\FormRenderer;
use MyFormBuilder\Fields\SelectMultipleField;
use MyFormBuilder\Fields\SignatureField;
use MyFormBuilder\Fields\TimeField;

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

    public function title(string $name, array $attributes = null)
    {

        $attributes = $attributes ?? [];
        // $field = $this->fieldFactory->create('text', $name, $attributes);
        return (new TitleField($name, $attributes))->render();
        return $this;
    }

    public function help(string $name, array $attributes = null)
    {

        $attributes = $attributes ?? [];
        // $field = $this->fieldFactory->create('text', $name, $attributes);
        return (new HelpField($name, $attributes))->render();
        return $this;
    }

    public function button(string $name, array $attributes = null)
    {

        $attributes = $attributes ?? [];
        // $field = $this->fieldFactory->create('text', $name, $attributes);
        return (new ButtonField($name, $attributes))->render();
        return $this;
    }

    public function div(string $name, array $attributes = null)
    {

        $attributes = $attributes ?? [];
        // $field = $this->fieldFactory->create('text', $name, $attributes);
        return (new DivField($name, $attributes))->render();
        return $this;
    }

    public function checkbox(string $name, array $attributes = null)
    {

        $attributes = $attributes ?? [];
        // $field = $this->fieldFactory->create('text', $name, $attributes);
        return (new CheckboxField($name, $attributes))->render();
        return $this;
    }

    public function text(string $name, array $attributes = null)
    {

        $attributes = $attributes ?? [];
        // $field = $this->fieldFactory->create('text', $name, $attributes);
        return (new TextField($name, $attributes))->render();
        return $this;
    }

    public function signature(string $name, array $attributes = null)
    {

        $attributes = $attributes ?? [];
        // $field = $this->fieldFactory->create('text', $name, $attributes);
        return (new SignatureField($name, $attributes))->render();
        return $this;
    }

    public function location(string $name, array $attributes = null)
    {
        return (new LocationField($name, $attributes))->render();
    }

    public function file(string $name, array $attributes = null)
    {

        $attributes = $attributes ?? [];
        // $field = $this->fieldFactory->create('text', $name, $attributes);
        return (new FileField($name, $attributes))->render();
        return $this;
    }

    public function date(string $name, array $attributes = null)
    {

        $attributes = $attributes ?? [];
        // $field = $this->fieldFactory->create('text', $name, $attributes);
        return (new DateField($name, $attributes))->render();
        return $this;
    }

    public function time(string $name, array $attributes = null)
    {

        $attributes = $attributes ?? [];
        // $field = $this->fieldFactory->create('text', $name, $attributes);
        return (new TimeField($name, $attributes))->render();
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

    public function textarea($name, $attributes = null)
    {

        $attributes = $attributes ?? [];
        // $field = $this->fieldFactory->create('textarea', $name, $attributes);
        return (new TextareaField($name, $attributes))->render();

        $this->fields[] = new TextareaField($name, $field);
        return $this;
    }

    public function entity($name, $attributes = [])
    {
        $attributes = $attributes ?? [];


        return (new EntityField($name, $attributes))->render();
    }

    public function select($name, $options, $attributes = [])
    {
        $attributes = $attributes ?? [];
        $attributes['options'] = $options;


        return (new SelectField($name, $attributes))->render();
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

    public function selectMultiple($name, $options, $attributes = [])
    {
        $attributes = $attributes ?? [];
        $attributes['options'] = $options;


        return (new SelectMultipleField($name, $attributes))->render();
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
