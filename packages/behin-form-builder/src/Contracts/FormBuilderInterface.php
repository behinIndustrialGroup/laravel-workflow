<?php

namespace MyFormBuilder\Contracts;

interface FormBuilderInterface
{
    public function open(array $attributes = []): self;
    public function text(string $name, array $attributes = []): self;
    public function email(string $name, array $attributes = []): self;
    public function password(string $name, array $attributes = []): self;
    public function textarea(string $name, array $attributes = []): self;
    public function select(string $name, string $options, array $attributes = []): self;
    public function submit(string $text = 'Submit', array $attributes = []): self;
    public function render(): string;
}
