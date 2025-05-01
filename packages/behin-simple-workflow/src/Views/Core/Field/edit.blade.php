@extends('behin-layouts.app')

@section('title')
    {{ trans('fields.Edit Field') . ' - ' . $field->name }}
@endsection

@section('content')
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.13.1/ace.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.23.0/mode-php.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.23.0/theme-monokai.js"></script>

    <h1>Edit Field</h1>
    <a href="{{ route('simpleWorkflow.fields.index') }}" class="btn btn-secondary mb-3">
        {{ trans('Back to list') }}
    </a>
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    @php
        $attributes = json_decode($field->attributes);
    @endphp
    <form action="{{ route('simpleWorkflow.fields.update', $field->id) }}" method="POST"
        class="p-4 border rounded shadow-sm bg-light">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">{{ trans('Name') }}</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ $field->name }}" required>
        </div>

        <div class="mb-3">
            <label for="type" class="form-label">{{ trans('Type') }}</label>
            <select name="type" id="type" class="form-select">
                <option value="string" @if ($field->type == 'string') selected @endif>{{ trans('String') }}</option>
                <option value="number" @if ($field->type == 'number') selected @endif>{{ trans('Number') }}</option>
                <option value="text" @if ($field->type == 'text') selected @endif>{{ trans('Text') }}</option>
                <option value="date" @if ($field->type == 'date') selected @endif>{{ trans('Date') }}</option>
                <option value="time" @if ($field->type == 'time') selected @endif>{{ trans('Time') }}</option>
                <option value="select" @if ($field->type == 'select') selected @endif>{{ trans('Select') }}</option>
                <option value="select-multiple" @if ($field->type == 'select-multiple') selected @endif>
                    {{ trans('Select Multiple') }}</option>
                <option value="file" @if ($field->type == 'file') selected @endif>{{ trans('File') }}</option>
                <option value="checkbox" @if ($field->type == 'checkbox') selected @endif>{{ trans('Checkbox') }}</option>
                <option value="radio" @if ($field->type == 'radio') selected @endif>{{ trans('Radio') }}</option>
                <option value="location" @if ($field->type == 'location') selected @endif>{{ trans('Location') }}
                </option>
                <option value="signature" @if ($field->type == 'signature') selected @endif>{{ trans('Signature') }}
                </option>
                <option value="entity" @if ($field->type == 'entity') selected @endif>{{ trans('Entity') }}</option>
                <option value="title" @if ($field->type == 'title') selected @endif>{{ trans('Title') }}</option>

                <option value="div" @if ($field->type == 'div') selected @endif>{{ trans('Div') }}</option>
                <option value="button" @if ($field->type == 'button') selected @endif>{{ trans('Button') }}</option>
                <option value="help" @if ($field->type == 'help') selected @endif>{{ trans('Help') }}</option>
            </select>
        </div>
        @if ($field->type == 'entity')
            @include('SimpleWorkflowView::Core.Field.entity', ['field' => $field])
        @elseif($field->type == 'help')
            @include('SimpleWorkflowView::Core.Field.help', ['field' => $field])
        @else
            @if ($field->type == 'select' || $field->type == 'select-multiple')
                <div class="mb-3">
                    <label for="options" class="form-label">{{ trans('Options') }}</label>
                    <span>هر گزینه در یک خط</span>
                    <textarea name="options" id="options" class="form-control" rows="4" dir="ltr">{{ isset($attributes?->options) ? $attributes?->options : '' }}</textarea>
                </div>
            @endif

            <div class="mb-3">
                <label for="query" class="form-label">{{ trans('Query') }}</label>
                <p>
                    کوئری باید شامل value و label باشد.
                </p>
                <textarea name="query" id="query" class="form-control" rows="4" dir="ltr">{{ is_string($attributes?->query) ? $attributes?->query : '' }}</textarea>
            </div>

            <div class="mb-3">
                <label for="placeholder" class="form-label">{{ trans('Placeholder') }}</label>
                <input type="text" name="placeholder" id="placeholder" class="form-control"
                    value="{{ $attributes?->placeholder }}">
            </div>

            <div class="mb-3">
                <label for="style" class="form-label">Style</label>
                <textarea name="style" id="style" class="form-control" rows="4" dir="ltr">{{ isset($attributes->style) && is_string($attributes?->style) ? $attributes?->style : '' }}</textarea>
            </div>

            <div class="mb-3">
                <label for="script" class="form-label">Script</label>
                <span>نیازی به تگ script نیست</span>
                <div id="script-editor" style="height: 500px; width: 100%;font-size: 16px;">{{ $attributes?->script ?? null }}</div>
                <textarea name="script" id="script" class="d-none" dir="ltr">{{ isset($attributes->script) && is_string($attributes?->script) ? $attributes?->script : '' }}</textarea>
                <script>
                    const editor = ace.edit("script-editor");
                    editor.setTheme("ace/theme/monokai"); // انتخاب تم
                    editor.session.setMode("ace/mode/javascript"); // تنظیم زبان 
                
                
                
                    // غیرفعال کردن تحلیلگر پیش‌فرض Ace
                    editor.getSession().setUseWorker(false);
                
                    // فعال‌سازی خط‌بندی خودکار
                    editor.setOption("wrap", true);
                
                    // ذخیره محتوا به textarea مخفی
                    editor.session.on('change', function() {
                        $('#script').val(editor.getValue());
                    });
                </script>
            </div>

            <div class="mb-3">
                <label for="datalist" class="form-label">Datalist From Database</label>
                باید شامل value و label باشد
                <textarea name="datalist_from_database" id="datalist" class="form-control" rows="4" dir="ltr">{{ isset($attributes->datalist_from_database) && is_string($attributes?->datalist_from_database) ? $attributes?->datalist_from_database : '' }}</textarea>
            </div>
        @endif

        <button class="btn btn-primary">{{ trans('Update') }}</button>
    </form>

@endsection
