@extends('behin-layouts.app')

@section('title')
    {{ trans('fields.Entity List') }}
@endsection

@section('content')
<div class="container card p-3">
    <form action="{{ route('simpleWorkflow.entities.store') }}" method="POST" class="row">
        @csrf
        <div class="col-sm-4">
            <input type="text" name="name" class="form-control text-center">
        </div>
        <div class="col-sm-4">
            <button class="btn btn-default">{{ trans('fields.Create') }}</button>
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
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        <form action="{{ route('simpleWorkflow.entities.export') }}" method="POST">
            @csrf
            <div class="mb-2">
                <button class="btn btn-default">{{ trans('fields.Export') }}</button>
            </div>
            <table class="table table-strpped" id="table">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="select-all"></th>
                        <th>{{ trans('ID') }}</th>
                        <th class="text-left">{{ trans('fields.Name') }}</th>
                        <th class="text-left">{{ trans('fields.Name') }}</th>
                        <th>{{ trans('fields.Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($entities as $entity)
                        <tr>
                            <td><input type="checkbox" name="ids[]" value="{{ $entity->id }}"></td>
                            <td>{{ $loop->iteration }}</td>
                            <td class="text-left">{{ trans("fields.".$entity->name) }}</td>
                            <td class="text-left">{{ $entity->name }}</td>
                            <td>
                                <a href="{{ route('simpleWorkflow.entities.edit', $entity->id) }}">{{ trans('fields.Edit') }}</a> |
                                <a href="{{ route('simpleWorkflow.entities.records', $entity->id) }}">{{ trans('fields.Edit Records') }}</a> |
                                <a href="{{ route('simpleWorkflow.entities.destroy', $entity->id) }}" onclick="return confirm('{{ trans('messages.confirmDelete') }}')">{{ trans('fields.Delete') }}</a>
                            </td>
                        </tr>
                    @endforeach

                </tbody>
                <tfoot id="createForm">

                </tfoot>
            </table>
        </form>
        <form action="{{ route('simpleWorkflow.entities.import') }}" method="POST" enctype="multipart/form-data" class="mt-3">
            @csrf
            <div class="row">
                <div class="col-sm-4">
                    <input type="file" name="file" class="form-control" required>
                </div>
                <div class="col-sm-4">
                    <button class="btn btn-default">{{ trans('fields.Import') }}</button>
                </div>
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
        document.getElementById('select-all').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('#table tbody input[type="checkbox"]');
            checkboxes.forEach(cb => cb.checked = this.checked);
        });
    </script>
@endsection
