@extends('behin-layouts.app')

@section('title')
    {{ trans('fields.Edit Entity') }}
@endsection

@section('content')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.13.1/ace.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.23.0/mode-php.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.23.0/theme-monokai.js"></script>
    <div class="container table-responsive card p-2">
        <div class="row mb-3 text-left" dir="ltr">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Model Information</h5>
                        <div class="mb-2">
                            <span class="text-muted">Model Name:</span>
                            <span class="ms-2">{{ $entity->model_name }}</span>
                        </div>
                        <div class="mb-2">
                            <span class="text-muted">Namespace:</span>
                            <span class="ms-2">{{ $entity->namespace }}</span>
                        </div>
                        <div class="mb-2">
                            <span class="text-muted">DB Table Name:</span>
                            <span class="ms-2">{{ $entity->db_table_name }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <form action="{{ route('simpleWorkflow.entities.update', $entity->id) }}" method="POST">
            @csrf
            @method('PUT')
            {!! Form::text('name', [
                'name' => 'name',
                'value' => $entity->name,
                'class' => 'form-control',
                'required' => true,
                'dir' => 'ltr',
            ]) !!}
            {!! Form::textarea('description', [
                'value' => $entity->description,
                'class' => 'form-control',
                'required' => false,
                'placeholder' => trans('fields.Entity Description'),
                'rows' => 5,
            ]) !!}
            <span dir="ltr" style="display:block; float:left">name,type,nullable(yes,no)</span>
            <label for="class_uses">{{ trans('fields.Columns') }}</label>
            <div id="columns_editor" style="height: 200px; width: 100%;">{{ $entity->columns }}</div>
            <textarea name="columns" id="class_columns" class="form-control"
                style="display: none;text-align: left; white-space: pre; font-family: Monospace " dir="ltr">{{ $entity->columns }}</textarea>
            
            <label for="class_uses">{{ trans('fields.Class Uses') }}</label>
            <div id="use_editor" style="height: 200px; width: 100%;">{{ $entity->uses }}</div>
            <textarea name="uses" id="class_uses" class="form-control"
                style="display: none;text-align: left; white-space: pre; font-family: Monospace " dir="ltr">{{ $entity->uses }}</textarea>

            <label for="class_contents">{{ trans('fields.Class Content') }}</label>
            <div id="editor" style="height: 500px; width: 100%;">{{ $entity->class_contents }}</div>
            <textarea name="class_contents" id="class_contents" class="form-control"
                style="display: none;text-align: left; white-space: pre; font-family: Monospace " dir="ltr">{{ $entity->class_contents }}</textarea>
            <button class="btn btn-primary">{{ trans('fields.Submit') }}</button>
        </form>
        <a class="btn btn-danger" href="{{ route('simpleWorkflow.entities.createTable', $entity->id) }}"
            target="_blank">{{ trans('fields.Create Table') }}</a>
    </div>
@endsection

@section('script')
    <script>
        const columns_editor = ace.edit("columns_editor");
        columns_editor.setTheme("ace/theme/monokai"); // انتخاب تم
        columns_editor.session.setMode("ace/mode/php"); // تنظیم زبان PHP



        // غیرفعال کردن تحلیلگر پیش‌فرض Ace
        columns_editor.getSession().setUseWorker(false);

        // فعال‌سازی خط‌بندی خودکار
        columns_editor.setOption("wrap", true);

        // ذخیره محتوا به textarea مخفی
        columns_editor.session.on('change', function() {
            $('#class_columns').val(columns_editor.getValue());
        });



        const use_editor = ace.edit("use_editor");
        use_editor.setTheme("ace/theme/monokai"); // انتخاب تم
        use_editor.session.setMode("ace/mode/php"); // تنظیم زبان PHP



        // غیرفعال کردن تحلیلگر پیش‌فرض Ace
        use_editor.getSession().setUseWorker(false);

        // فعال‌سازی خط‌بندی خودکار
        use_editor.setOption("wrap", true);

        // ذخیره محتوا به textarea مخفی
        use_editor.session.on('change', function() {
            $('#class_uses').val(use_editor.getValue());
        });

        const editor = ace.edit("editor");
        editor.setTheme("ace/theme/monokai"); // انتخاب تم
        editor.session.setMode("ace/mode/php"); // تنظیم زبان PHP



        // غیرفعال کردن تحلیلگر پیش‌فرض Ace
        editor.getSession().setUseWorker(false);

        // فعال‌سازی خط‌بندی خودکار
        editor.setOption("wrap", true);

        // ذخیره محتوا به textarea مخفی
        editor.session.on('change', function() {
            $('#class_contents').val(editor.getValue());
        });
    </script>
@endsection
