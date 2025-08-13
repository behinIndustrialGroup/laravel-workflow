@php
    $attributes = json_decode($field->attributes);
@endphp
{!! Form::text('id', [
    'value' => $attributes?->id ?? null,
    'required' => false,
    'dir' => 'ltr'
]) !!}
{!! Form::textarea('options', [
    'value' => $attributes?->options ?? null,
    'required' => false,
    'dir' => 'ltr'
]) !!}
{!! Form::textarea('style', [
    'value' => $attributes?->style ?? null,
    'required' => false,
    'dir' => 'ltr',
    'id' => 'style',
    'class' => 'd-none'
]) !!}
<div id="style-editor" style="height: 200px; width: 100%;font-size: 16px;">{{ $attributes?->style ?? null }}</div>

{!! Form::textarea('script', [
    'value' => $attributes?->script ?? null,
    'required' => false,
    'dir' => 'ltr',
    'id' => 'script',
    'class' => 'd-none'
]) !!}
<div id="script-editor" style="height: 500px; width: 100%;font-size: 16px;">{{ $attributes?->script ?? null }}</div>

<script>
    ace.require('ace/ext/language_tools');

    const styleEditor = ace.edit('style-editor');
    styleEditor.setTheme('ace/theme/monokai');
    styleEditor.session.setMode('ace/mode/css');
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

    const scriptEditor = ace.edit('script-editor');
    scriptEditor.setTheme('ace/theme/monokai');
    scriptEditor.session.setMode('ace/mode/javascript');
    scriptEditor.setOptions({
        enableBasicAutocompletion: true,
        enableLiveAutocompletion: true,
        enableSnippets: true,
        wrap: true,
    });
    scriptEditor.getSession().setUseWorker(false);
    scriptEditor.session.on('change', function () {
        $('#script').val(scriptEditor.getValue());
    });
</script>
