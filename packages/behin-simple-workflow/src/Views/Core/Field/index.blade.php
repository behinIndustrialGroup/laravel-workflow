@extends('behin-layouts.app')
@section('title')
    متغیر ها
@endsection

@section('content')
    <div class="container card p-3">
        <form action="{{ route('simpleWorkflow.fields.store') }}" method="POST" class="row">
            @csrf
            <div class="col-sm-4">
                <input type="text" name="name" class="form-control text-center">
            </div>
            <div class="col-sm-4">
                <select name="type" id="" class="form-control">
                    <option value="string">string</option>
                    <option value="number">number</option>
                    <option value="formmatted-digit">formatted-digit</option>
                    <option value="text">text</option>
                    <option value="date">date</option>
                    <option value="time">time</option>
                    <option value="datetime">datetime</option>
                    <option value="select">select</option>
                    <option value="select-multiple">select-multiple</option>
                    <option value="select-simple">select-simple</option>
                    <option value="searchable-input">searchable-input</option>
                    <option value="file">file</option>
                    <option value="checkbox">checkbox</option>
                    <option value="radio">radio</option>
                    <option value="location">location</option>
                    <option value="signature">signature</option>
                    <option value="entity">entity</option>
                    <option value="title">title</option>
                    <option value="div">div</option>
                    <option value="button">button</option>
                    <option value="help">help</option>
                    <option value="hidden">hidden</option>
                    <option value="view-model">view-model</option>
                </select>
            </div>
            <div class="col-sm-4">
                <button class="btn btn-default">{{ trans('fields.Create') }}</button>
            </div>

        </form>
    </div>
    <div class="container card p-3 mt-3">
        <form action="{{ route('simpleWorkflow.fields.import') }}" method="POST" enctype="multipart/form-data"
            class="row">
            @csrf
            <div class="col-sm-8">
                <input type="file" name="fields_file" class="form-control" accept="application/json">
            </div>
            <div class="col-sm-4">
                <button class="btn btn-default">Import</button>
            </div>
        </form>
    </div>
    <div class="container card p-3 table-responsive">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        <form action="javascript:void(0)" method="POST" id="export-form">
            @csrf
            <table class="table table-strpped" id="table">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="select-all"></th>
                        <th>{{ trans('ID') }}</th>
                        <th class="text-left">{{ trans('fields.Name') }}</th>
                        <th class="text-left">{{ trans('fields.Name') }}</th>
                        <th class="text-left">{{ trans('fields.Type') }}</th>
                        <th class="text-left">{{ trans('fields.Forms') }}</th>
                        <th>{{ trans('fields.Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($fields as $key => $field)
                        @php
                            $attributes = json_decode($field->attributes);
                        @endphp
                        <tr>
                            <td><input type="checkbox" name="field_ids[]" value="{{ $field->id }}"></td>
                            <td>{{ $key }}</td>
                            <td class="text-left">{{ trans('fields.' . $field->name) }}</td>
                            <td class="text-left">{{ $field->name }}</td>
                            <td class="text-left">{{ $field->type }}</td>
                            <td class="text-left">
                                @if (!empty($field->forms))
                                    {{ implode(', ', $field->forms) }}
                                @else
                                    -
                                @endif
                            </td>

                            <td>
                                <a href="{{ route('simpleWorkflow.fields.edit', $field->id) }}"
                                    class="btn btn-default">{{ trans('fields.Edit') }}</a>
                                <button class="btn btn-danger">{{ trans('fields.Delete') }}</button>
                                <a href="{{ route('simpleWorkflow.fields.copy', $field->id) }}"
                                    class="btn btn-success">{{ trans('fields.Copy') }}</a>

                            </td>
                        </tr>
                    @endforeach

                </tbody>
                <tfoot id="createForm">

                </tfoot>
            </table>
            <div class="mt-2">
                <button class="btn btn-warning" id="export-selected">Export Selected</button>
            </div>
        </form>
    </div>
@endsection

@section('script')
    <script>
        $('#table').DataTable({
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Persian.json"
            }
        });
        $('#select-all').on('click', function() {
            $('input[name="field_ids[]"]').prop('checked', this.checked);
        });
        $('#export-selected').on('click', function() {
            // ساخت FormData از فرم
            var fd = new FormData($('#export-form')[0]);

            // ارسال درخواست به سرور و گرفتن خروجی به صورت Blob
            fetch("{{ route('simpleWorkflow.fields.export') }}", {
                    method: 'POST',
                    body: fd
                })
                .then(res => res.blob()) // دریافت به صورت فایل
                .then(blob => {
                    // ساخت لینک دانلود
                    const url = URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = 'fields.json'; // اسم فایل دانلود
                    document.body.appendChild(a);
                    a.click();
                    a.remove();
                    URL.revokeObjectURL(url);
                })
                .catch(err => console.error(err));
        });
    </script>
@endsection
