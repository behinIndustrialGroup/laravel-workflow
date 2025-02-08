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
                    <i class="fa fa-edit mr-2"></i> {{ trans('Edit ') }}
                </a>
                <a href="{{ route('simpleWorkflow.form.editContent', ['id' => $form->id]) }}" class="btn btn-primary">
                    <i class="fa fa-edit mr-2"></i> {{ trans('Edit Content') }}
                </a>
            </div>
        </div>
        <div class="card">
            <div class="row">
                <div class="col-sm-12">
                    <form action="{{ route('simpleWorkflow.form.updateScript') }}" method="POST" class="mb-3">
                        @csrf
                        <input type="hidden" name="formId" value="{{ $form->id }}">
                        <label for="">{{ trans('Form Name') }}:</label>
                        <input type="text" name="name" value="{{ $form->name }}" class="form-control" id="">
                        <label for="">{{ trans('Form Scripts') }}:</label>
                        <div id="editor" style="height: 600px; width: 100%;">{{ $form->scripts }}</div>
                        <textarea name="scripts" id="scripts" style="display: none" class="form-control" dir="ltr" id="" cols="30" rows="30">{{ $form->scripts }}</textarea>
                        <button type="submit" class="btn btn-primary">{{ trans('Update') }}</button>
                    </form>
                </div>
                
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
