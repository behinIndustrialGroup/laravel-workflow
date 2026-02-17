@extends('behin-layouts.app')

@section('title', $form->name)

@php
    $index = 0;
    $content = json_decode($form->content);
    $content = collect($content)->sortBy('order')->toArray();
@endphp

@section('content')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.13.1/ace.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.23.0/mode-php.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.23.0/theme-monokai.js"></script>
    <div class="container">
        <div class="card shadow-sm p-3 mb-3">
            <div class="d-flex justify-content-between">
                <a href="{{ route('simpleWorkflow.form.index') }}" class="btn btn-outline-primary">
                    <i class="fa fa-arrow-left mr-2"></i> {{ trans('Back To Forms') }}
                </a>
                <a href="{{ route('simpleWorkflow.form.edit', ['id' => $form->id]) }}" class="btn btn-primary">
                    <i class="fa fa-edit mr-2"></i> {{ trans('Edit Content') }}
                </a>
                <a href="{{ route('simpleWorkflow.form.editScript', ['id' => $form->id]) }}" class="btn btn-primary">
                    <i class="fa fa-edit mr-2"></i> {{ trans('Edit Script') }}
                </a>
            </div>
        </div>
        <div class="card">
                <div class="col-sm-12 p-2">
                    <form action="{{ route('simpleWorkflow.form.updateContent') }}" method="POST" class="m-1">
                        @csrf
                        <input type="hidden" name="formId" value="{{ $form->id }}">
                        <label for="">{{ trans('Form Name') }}:</label>
                        <input type="text" name="name" value="{{ $form->name }}" class="form-control"
                            id="">
                        <label for="">{{ trans('Form Content') }}:</label>
                        <div dir="ltr" id="form-content" style="height: 600px; width: 100%;">{{ $form->content }}</div>
                        <textarea name="content" class="form-control d-none" dir="ltr" id="content" cols="30" rows="30">{{ $form->content }}</textarea>
                        <button type="submit" class="btn btn-primary">{{ trans('Update') }}</button>
                    </form>
                </div>
        </div>
        <div class="col-sm-12">
            @include('SimpleWorkflowView::Core.Form.preview', ['form' => $form])
        </div>

    </div>
@endsection

@section('script')
    <script>
        initial_view();

        function deleteField(index) {
            $('#tr_' + index).remove();
        }

        const formContentEditor = ace.edit("form-content");
        formContentEditor.setTheme("ace/theme/monokai"); // انتخاب تم
        formContentEditor.session.setMode("ace/mode/javascript");
        formContentEditor.setOption("wrap", true);
        formContentEditor.setOption("font-size", '16px');
        formContentEditor.session.on('change', function() {
            $('#content').val(formContentEditor.getValue());
        });
    </script>
    <script>
        const editor = ace.edit("editor");
        editor.setTheme("ace/theme/monokai"); // انتخاب تم
        editor.session.setMode("ace/mode/javascript");
        // غیرفعال کردن تحلیلگر پیش‌فرض Ace
        editor.getSession().setUseWorker(false);

        // فعال‌سازی خط‌بندی خودکار
        editor.setOption("wrap", true);

        // ذخیره محتوا به textarea مخفی
        editor.session.on('change', function() {
            $('#scripts').val(editor.getValue());
        });
    </script>
@endsection
