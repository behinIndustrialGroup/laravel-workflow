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
                            <span class="text-muted">Uses:</span>
                            <span class="ms-2">use {{ $entity->namespace }}\{{ $entity->model_name }};</span>
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
            <label>{{ trans('fields.Columns') }}</label>
            <table class="table" id="columns_table">
                <thead>
                    <tr>
                        <th>Field</th>
                        <th>Type</th>
                        <th>Nullable</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
            <button class="btn btn-secondary" type="button" id="add_field">Add Field</button>
            <textarea name="columns" id="columns" class="form-control" style="display:none">{{ $entity->columns }}</textarea>
            
            <label for="class_uses">{{ trans('fields.Class Uses') }}</label>
            <div id="use_editor" style="height: 200px; width: 100%;">{{ $entity->uses }}</div>
            <textarea name="uses" id="class_uses" class="form-control"
                style="display: none;text-align: left; white-space: pre; font-family: Monospace " dir="ltr">{{ $entity->uses }}</textarea>

            <label for="class_contents">{{ trans('fields.Class Content') }}</label>
            <div id="editor" style="height: 500px; width: 100%;">{{ $entity->class_contents }}</div>
            <textarea name="class_contents" id="class_contents" class="form-control"
                style="display: none;text-align: left; white-space: pre; font-family: Monospace " dir="ltr">{{ $entity->class_contents }}</textarea>
            <button class="btn btn-primary">{{ trans('fields.Submit') }}</button>
            <a class="btn btn-danger" style="float: left" href="{{ route('simpleWorkflow.entities.createTable', $entity->id) }}"
                >{{ trans('fields.Create Table') }}</a>
        </form>
        
    </div>
@endsection

@section('script')
    <script>
        let tables = @json($tables);
        function addRow(name = '', type = 'string', nullable = 'no', table = '') {
            let options = tables.map(t => `<option value="${t}" ${table === t ? 'selected' : ''}>${t}</option>`).join('');
            let row = `<tr>
                <td><input type="text" class="form-control field-name" value="${name}"></td>
                <td>
                    <select class="form-control field-type">
                        <option value="string" ${type === 'string' ? 'selected' : ''}>string</option>
                        <option value="integer" ${type === 'integer' ? 'selected' : ''}>integer</option>
                        <option value="json" ${type === 'json' ? 'selected' : ''}>json</option>
                        <option value="entity" ${type === 'entity' ? 'selected' : ''}>entity</option>
                    </select>
                    <select class="form-control entity-table ${type === 'entity' ? '' : 'd-none'}">${options}</select>
                </td>
                <td>
                    <select class="form-control field-nullable">
                        <option value="no" ${nullable === 'no' ? 'selected' : ''}>no</option>
                        <option value="yes" ${nullable === 'yes' ? 'selected' : ''}>yes</option>
                    </select>
                </td>
                <td><button type="button" class="btn btn-danger btn-sm remove-row">X</button></td>
            </tr>`;
            $('#columns_table tbody').append(row);
        }
        $('#add_field').on('click', function() { addRow(); });
        $(document).on('change', '.field-type', function() {
            if ($(this).val() === 'entity') {
                $(this).siblings('.entity-table').removeClass('d-none');
            } else {
                $(this).siblings('.entity-table').addClass('d-none');
            }
        });
        $(document).on('click', '.remove-row', function() {
            $(this).closest('tr').remove();
        });
        function loadExisting() {
            let lines = $('#columns').val().trim().split(/\r?\n/);
            lines.forEach(function(line) {
                if (!line) return;
                let parts = line.split(',');
                let name = parts[0];
                let type = parts[1] || 'string';
                let nullable = parts[2] || 'no';
                let table = '';
                if (type.startsWith('entity:')) {
                    table = type.split(':')[1];
                    type = 'entity';
                }
                addRow(name, type, nullable, table);
            });
        }
        loadExisting();
        $('form').on('submit', function() {
            let lines = [];
            $('#columns_table tbody tr').each(function() {
                let name = $(this).find('.field-name').val();
                let type = $(this).find('.field-type').val();
                let nullable = $(this).find('.field-nullable').val();
                if (type === 'entity') {
                    let table = $(this).find('.entity-table').val();
                    type = 'entity:' + table;
                }
                lines.push(name + ',' + type + ',' + nullable);
            });
            $('#columns').val(lines.join('\n'));
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
