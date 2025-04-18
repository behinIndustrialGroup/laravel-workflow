@php
    $attributes = json_decode($field->attributes);
@endphp
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.13.1/ace.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.23.0/mode-php.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.23.0/theme-monokai.js"></script>

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
]) !!}
<div id="script-editor" style="height: 500px; width: 100%;font-size: 16px;">{{ $attributes?->script ?? null }}</div>
<textarea name="script" id="script" dir="ltr" class="d-none">{{ $attributes?->script ?? null }}</textarea>
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
