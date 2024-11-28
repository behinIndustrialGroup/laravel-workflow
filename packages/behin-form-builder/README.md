# Laravel Form Builder

A simple and elegant form builder package for Laravel applications.

## Installation

```bash
composer require my/form-builder
```

## Usage

```php
// In your blade template
{!! Form::open(['action' => route('users.store')]) !!}
    {!! Form::text('name', ['placeholder' => 'Enter your name']) !!}
    {!! Form::email('email', ['placeholder' => 'Enter your email']) !!}
    {!! Form::password('password') !!}
    {!! Form::textarea('bio', ['rows' => 3]) !!}
    {!! Form::select('country', [
        'us' => 'United States',
        'uk' => 'United Kingdom',
        'ca' => 'Canada'
    ]) !!}
    {!! Form::submit('Save User') !!}
{!! Form::render() !!}
```

## Features

- Fluent interface for form building
- Support for common form elements
- Custom attributes and classes
- Error handling integration
- Bootstrap-compatible markup
- Extensible and customizable

## Available Methods

- `open(array $attributes = [])`
- `text(string $name, array $attributes = [])`
- `email(string $name, array $attributes = [])`
- `password(string $name, array $attributes = [])`
- `textarea(string $name, array $attributes = [])`
- `select(string $name, array $options, array $attributes = [])`
- `submit(string $text = 'Submit', array $attributes = [])`
- `render()`

## Customization

You can publish the views to customize the HTML markup:

```bash
php artisan vendor:publish --tag=form-builder-views
```