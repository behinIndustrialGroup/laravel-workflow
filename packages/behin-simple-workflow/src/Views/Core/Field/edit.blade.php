@extends('behin-layouts.app')

@section('title')
    {{ trans('fields.Edit Field') . ' - ' . $field->name }}
@endsection

@section('content')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.13.1/ace.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.23.0/mode-php.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.23.0/theme-monokai.js"></script>
    <div class="card">
        <div class="card-header">
            <a href="{{ route('simpleWorkflow.fields.index') }}" class="btn btn-secondary mb-3">
                {{ trans('Back to list') }}
            </a>
        </div>
    </div>
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
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
                    <div class="col-sm-6 mb-3">
                        <label for="name" class="form-label">{{ trans('Name') }}</label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ $field->name }}"
                            required>
                    </div>

                    <div class="col-sm-6 mb-3">
                        <label for="type" class="form-label">{{ trans('Type') }}</label>
                        <select name="type" id="type" class="form-select form-control">
                            @foreach (['string', 'number', 'formatted-digit', 'text', 'date', 'time', 'select', 'select-multiple', 'file', 'checkbox', 'radio', 'location', 'signature', 'entity', 'title', 'div', 'button', 'help', 'hidden', 'view-model'] as $typeOption)
                                <option value="{{ $typeOption }}" @if ($field->type == $typeOption) selected @endif>
                                    {{ trans(ucwords(str_replace('-', ' ', $typeOption))) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Type Specific Inputs --}}
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
                        @if (in_array($field->type, ['select', 'select-multiple']))
                            <div class="mb-3">
                                <label for="options" class="form-label">{{ trans('Options') }}</label>
                                <small class="d-block">هر گزینه در یک خط</small>
                                <textarea name="options" id="options" class="form-control" rows="4" dir="ltr">{{ $attributes->options ?? '' }}</textarea>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label for="query" class="form-label">{{ trans('Query') }}</label>
                            <small class="d-block">کوئری باید شامل value و label باشد.</small>
                            <textarea name="query" id="query" class="form-control" rows="4" dir="ltr">{{ is_string($attributes->query ?? null) ? $attributes->query : '' }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="placeholder" class="form-label">{{ trans('Placeholder') }}</label>
                            <input type="text" name="placeholder" id="placeholder" class="form-control"
                                value="{{ $attributes->placeholder ?? '' }}">
                        </div>

                        <div class="mb-3">
                            <label for="style" class="form-label">Style</label>
                            <textarea name="style" id="style" class="form-control" rows="4" dir="ltr">{{ $attributes->style ?? '' }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="isPrice" class="form-label">Is price?</label>
                            <select name="isPrice" id="isPrice" class="form-select">
                                <option value=""></option>
                                <option value="1" @if ($isPrice) selected @endif>بله</option>
                                <option value="0" @if (!$isPrice) selected @endif>خیر</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="script-editor" class="form-label">Script</label>
                            <small class="d-block">نیازی به تگ <code>&lt;script&gt;</code> نیست</small>
                            <div id="script-editor" style="height: 500px; width: 100%; font-size: 16px;">
                                {{ $attributes->script ?? '' }}</div>
                            <textarea name="script" id="script" class="d-none" dir="ltr">{{ $attributes->script ?? '' }}</textarea>
                            <script>
                                const editor = ace.edit("script-editor");
                                editor.setTheme("ace/theme/monokai");
                                editor.session.setMode("ace/mode/javascript");
                                editor.getSession().setUseWorker(false);
                                editor.setOption("wrap", true);
                                editor.session.on('change', function() {
                                    document.getElementById('script').value = editor.getValue();
                                });
                            </script>
                        </div>

                        <div class="mb-3">
                            <label for="datalist" class="form-label">Datalist From Database</label>
                            <small class="d-block">باید شامل value و label باشد</small>
                            <textarea name="datalist_from_database" id="datalist" class="form-control" rows="4" dir="ltr">{{ $attributes->datalist_from_database ?? '' }}</textarea>
                        </div>
                @endswitch

                <button type="submit" class="btn btn-primary">{{ trans('Update') }}</button>
            </form>
        </div>
    </div>


@endsection
