@php
    $attributes = json_decode($field->attributes);
@endphp

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="id" class="form-label">{{ trans('Id') }}</label>
        <input type="text" name="id" id="id" class="form-control" dir="ltr" value="{{ is_string($attributes->id ?? null) ? $attributes->id : '' }}">
    </div>
    <div class="col-md-6 mb-3">
        <label for="endpoint" class="form-label">{{ trans('Endpoint') }}</label>
        <input type="text" name="endpoint" id="endpoint" class="form-control" dir="ltr" value="{{ $attributes->endpoint ?? '' }}" placeholder="https://example.com/api/search">
    </div>
</div>
<div class="row">
    <div class="col-md-4 mb-3">
        <label for="minChars" class="form-label">{{ trans('Minimum Characters') }}</label>
        <input type="number" name="minChars" id="minChars" class="form-control" dir="ltr" value="{{ $attributes->minChars ?? 3 }}" min="1">
    </div>
    <div class="col-md-4 mb-3">
        <label for="limit" class="form-label">{{ trans('Limit') }}</label>
        <input type="number" name="limit" id="limit" class="form-control" dir="ltr" value="{{ $attributes->limit ?? '' }}" min="1" placeholder="{{ trans('Optional') }}">
    </div>
    <div class="col-md-4 mb-3">
        <label for="placeholder" class="form-label">{{ trans('Placeholder') }}</label>
        <input type="text" name="placeholder" id="placeholder" class="form-control" value="{{ $attributes->placeholder ?? '' }}">
    </div>
</div>
<div class="row">
    <div class="col-md-6 mb-3">
        <label for="initial_label" class="form-label">{{ trans('Initial Label') }}</label>
        <input type="text" name="initial_label" id="initial_label" class="form-control" value="{{ $attributes->initial_label ?? ($attributes->initialLabel ?? '') }}" placeholder="{{ trans('Optional label for preset value') }}">
    </div>
</div>

<div class="mb-3">
    <label for="style-editor" class="form-label">Style</label>
    <div id="style-editor" style="height: 200px; width: 100%; font-size: 16px;">{{ $attributes->style ?? '' }}</div>
    <textarea name="style" id="style" class="d-none" dir="ltr">{{ $attributes->style ?? '' }}</textarea>
</div>

<div class="mb-3">
    <label for="script-editor" class="form-label">Script</label>
    <small class="d-block text-muted">{{ trans('No need to wrap with <script> tag') }}</small>
    <div id="script-editor" style="height: 500px; width: 100%; font-size: 16px;">{{ $attributes->script ?? '' }}</div>
    <textarea name="script" id="script" class="d-none" dir="ltr">{{ $attributes->script ?? '' }}</textarea>
</div>

<script>
    ace.require("ace/ext/language_tools");

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
    styleEditor.session.on('change', function() {
        document.getElementById('style').value = styleEditor.getValue();
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
    editor.session.on('change', function() {
        document.getElementById('script').value = editor.getValue();
    });
</script>
