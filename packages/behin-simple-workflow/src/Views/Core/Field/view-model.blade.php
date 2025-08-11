@php
    $attributes = json_decode($field->attributes);
@endphp
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.13.1/ace.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.23.0/ext-language_tools.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.23.0/mode-javascript.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.23.0/mode-css.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.23.0/theme-monokai.js"></script>

<div class="row">
    <div class="col-sm-6">
        <label for="style" class="form-label">{{ trans('View Model') }}</label>
        <select name="view_model_id" id="view_model_id" class="form-select form-control">
            <option value="">{{ trans('Select View Model') }}</option>
            @foreach ($viewModels as $viewModel)
                <option value="{{ $viewModel->id }}" @if ($attributes?->view_model_id == $viewModel->id) selected @endif>{{ $viewModel->name }}</option>
            @endforeach
        </select>
    </div>
</div>

{!! Form::textarea('style', [
    'value' => $attributes?->style ?? null,
    'required' => false,
    'dir' => 'ltr',
    'id' => 'style',
    'class' => 'd-none',
]) !!}
<div id="style-editor" style="height: 200px; width: 100%;font-size: 16px;">{{ $attributes?->style ?? null }}</div>

Script
<div id="script-editor" style="height: 500px; width: 100%;font-size: 16px;">{{ $attributes?->script ?? null }}</div>
<textarea name="script" id="script" dir="ltr" class="d-none">{{ $attributes?->script ?? null }}</textarea>
<script>
    ace.require('ace/ext/language_tools');

    const styleEditor = ace.edit("style-editor");
    styleEditor.setTheme("ace/theme/monokai");
    styleEditor.session.setMode("ace/mode/css");
    styleEditor.setOptions({
        enableBasicAutocompletion: true,
        enableLiveAutocompletion: true,
        wrap: true,
    });
    styleEditor.getSession().setUseWorker(false);
    styleEditor.session.on('change', function () {
        $('#style').val(styleEditor.getValue());
    });

    const editor = ace.edit("script-editor");
    editor.setTheme("ace/theme/monokai");
    editor.session.setMode("ace/mode/javascript");
    editor.setOptions({
        enableBasicAutocompletion: true,
        enableLiveAutocompletion: true,
        wrap: true,
    });
    editor.getSession().setUseWorker(false);
    editor.session.on('change', function () {
        $('#script').val(editor.getValue());
    });
</script>
