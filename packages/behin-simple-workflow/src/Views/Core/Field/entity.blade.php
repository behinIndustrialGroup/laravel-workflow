@php
    $attributes = json_decode($field->attributes);
@endphp

{!! Form::text('id', [
    'value' => $attributes?->id ?? null,
    'required' => false,
    'dir' => 'ltr',
]) !!}
{!! Form::textarea('columns', [
    'value' => $attributes?->columns ?? null,
    'required' => false,
    'dir' => 'ltr',
]) !!}
{!! Form::textarea('query', [
    'value' => $attributes?->query ?? null,
    'required' => false,
    'dir' => 'ltr',
]) !!}

{!! Form::textarea('style', [
    'value' => $attributes?->style ?? null,
    'required' => false,
    'dir' => 'ltr',
    'id' => 'style',
    'class' => 'd-none',
]) !!}
<div id="style-editor" style="height: 200px; width: 100%;font-size: 16px;">{{ $attributes?->style ?? null }}</div>
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
        enableSnippets: true,
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
        enableSnippets: true,
        wrap: true,
    });
    editor.getSession().setUseWorker(false);
    editor.session.on('change', function () {
        $('#script').val(editor.getValue());
    });
</script>
