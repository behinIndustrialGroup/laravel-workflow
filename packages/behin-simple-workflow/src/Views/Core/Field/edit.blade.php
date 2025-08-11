@extends('behin-layouts.app')

@section('title')
    {{ trans('fields.Edit Field') . ' - ' . $field->name }}
@endsection

@section('content')
    {{-- ACE Editor --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.13.1/ace.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.23.0/ext-language_tools.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.23.0/mode-javascript.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.23.0/mode-css.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.23.0/theme-monokai.js"></script>

    {{-- Back Button --}}
    <div class="card mb-3">
        <div class="card-header">
            <a href="{{ route('simpleWorkflow.fields.index') }}" class="btn btn-secondary">
                {{ trans('Back to list') }}
            </a>
        </div>
    </div>

    {{-- Success Message --}}
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- Edit Form --}}
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">{{ trans('Edit Field') }}</h5>
        </div>

        <div class="card-body">
            @php
                $attributes = json_decode($field->attributes);
                $isPrice = isset($attributes->isPrice) && $attributes->isPrice ? $attributes->isPrice : '';
            @endphp

            <form action="{{ route('simpleWorkflow.fields.update', $field->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    {{-- Name --}}
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">{{ trans('Name') }}</label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ $field->name }}" required>
                    </div>

                    {{-- Type --}}
                    <div class="col-md-6 mb-3">
                        <label for="type" class="form-label">{{ trans('Type') }}</label>
                        <select name="type" id="type" class="form-select">
                            @foreach ([
                                'string', 'number', 'formatted-digit', 'text', 'date', 'time',
                                'select', 'select-multiple', 'file', 'checkbox', 'radio',
                                'location', 'signature', 'entity', 'title', 'div', 'button',
                                'help', 'hidden', 'view-model'
                            ] as $typeOption)
                                <option value="{{ $typeOption }}" @if ($field->type == $typeOption) selected @endif>
                                    {{ trans(ucwords(str_replace('-', ' ', $typeOption))) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Dynamic Fields --}}
                @switch($field->type)
                    @case('entity')
                        @include('SimpleWorkflowView::Core.Field.entity', ['field' => $field])
                        @break

                    @case('help')
                        @include('SimpleWorkflowView::Core.Field.help', ['field' => $field])
                        @break

                    @case('view-model')
                        @include('SimpleWorkflowView::Core.Field.view-model', ['field' => $field])
                        @break

                    @default
                        <div class="row">
                            {{-- ID --}}
                            <div class="col-md-6 mb-3">
                                <label for="id" class="form-label">{{ trans('Id') }}</label>
                                <input type="text" name="id" id="id" class="form-control" dir="ltr" value="{{ is_string($attributes->id ?? null) ? $attributes->id : '' }}">
                            </div>
                        </div>

                        {{-- Options --}}
                        @if (in_array($field->type, ['select', 'select-multiple']))
                            <div class="mb-3">
                                <label for="options" class="form-label">{{ trans('Options') }}</label>
                                <small class="d-block text-muted">هر گزینه در یک خط</small>
                                <textarea name="options" id="options" class="form-control" rows="4" dir="ltr">{{ $attributes->options ?? '' }}</textarea>
                            </div>
                        @endif

                        {{-- Query --}}
                        <div class="mb-3">
                            <label for="query" class="form-label">{{ trans('Query') }}</label>
                            <small class="d-block text-muted">کوئری باید شامل value و label باشد.</small>
                            <textarea name="query" id="query" class="form-control" rows="4" dir="ltr">{{ is_string($attributes->query ?? null) ? $attributes->query : '' }}</textarea>
                        </div>

                        {{-- Placeholder --}}
                        <div class="mb-3">
                            <label for="placeholder" class="form-label">{{ trans('Placeholder') }}</label>
                            <input type="text" name="placeholder" id="placeholder" class="form-control" value="{{ $attributes->placeholder ?? '' }}">
                        </div>

                        {{-- Style --}}
                        <div class="mb-3">
                            <label for="style-editor" class="form-label">Style</label>
                            <div id="style-editor" style="height: 200px; width: 100%; font-size: 16px;">{{ $attributes->style ?? '' }}</div>
                            <textarea name="style" id="style" class="d-none" dir="ltr">{{ $attributes->style ?? '' }}</textarea>

                            <script>
                                const styleEditor = ace.edit("style-editor");
                                styleEditor.setTheme("ace/theme/monokai");
                                styleEditor.session.setMode("ace/mode/css");
                                ace.require("ace/ext/language_tools");
                                styleEditor.setOptions({
                                    enableBasicAutocompletion: true,
                                    enableLiveAutocompletion: true,
                                    wrap: true,
                                });
                                styleEditor.getSession().setUseWorker(false);
                                styleEditor.session.on('change', function () {
                                    document.getElementById('style').value = styleEditor.getValue();
                                });
                            </script>
                        </div>

                        {{-- Is Price --}}
                        <div class="mb-3">
                            <label for="isPrice" class="form-label">Is Price?</label>
                            <select name="isPrice" id="isPrice" class="form-select">
                                <option value=""></option>
                                <option value="1" @if ($isPrice) selected @endif>بله</option>
                                <option value="0" @if (!$isPrice) selected @endif>خیر</option>
                            </select>
                        </div>

                        {{-- Script Editor --}}
                        <div class="mb-3">
                            <label for="script-editor" class="form-label">Script</label>
                            <small class="d-block text-muted">نیازی به تگ <code>&lt;script&gt;</code> نیست</small>
                            <div id="script-editor" style="height: 500px; width: 100%; font-size: 16px;">{{ $attributes->script ?? '' }}</div>
                            <textarea name="script" id="script" class="d-none" dir="ltr">{{ $attributes->script ?? '' }}</textarea>

                            <script>
                                const editor = ace.edit("script-editor");
                                editor.setTheme("ace/theme/monokai");
                                editor.session.setMode("ace/mode/javascript");
                                ace.require("ace/ext/language_tools");
                                editor.setOptions({
                                    enableBasicAutocompletion: true,
                                    enableLiveAutocompletion: true,
                                    wrap: true,
                                });
                                editor.getSession().setUseWorker(false);
                                editor.session.on('change', function () {
                                    document.getElementById('script').value = editor.getValue();
                                });
                            </script>
                        </div>

                        {{-- Datalist --}}
                        <div class="mb-3">
                            <label for="datalist" class="form-label">Datalist From Database</label>
                            <small class="d-block text-muted">باید شامل value و label باشد</small>
                            <textarea name="datalist_from_database" id="datalist" class="form-control" rows="4" dir="ltr">{{ $attributes->datalist_from_database ?? '' }}</textarea>
                        </div>
                @endswitch

                {{-- Submit --}}
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        {{ trans('Update') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
