@extends('behin-layouts.app')

@section('title')
    {{ trans('fields.Edit Records') }}
@endsection

@section('content')
<div class="container card p-3 table-responsive">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="mb-3 d-flex flex-wrap align-items-center">
        <a href="{{ route('simpleWorkflow.entities.createRecord', $entity->id) }}" class="btn btn-primary me-2">{{ trans('fields.Add Record') }}</a>
        <form action="{{ route('simpleWorkflow.entities.records.export', $entity->id) }}" method="POST" class="me-2">
            @csrf
            <button class="btn btn-default">{{ trans('fields.Export') }}</button>
        </form>
        <form action="{{ route('simpleWorkflow.entities.records.import', $entity->id) }}" method="POST" enctype="multipart/form-data" class="d-flex">
            @csrf
            <input type="file" name="file" class="form-control form-control-sm me-2" required>
            <button class="btn btn-default">{{ trans('fields.Import') }}</button>
        </form>
    </div>
    <table class="table table-strpped" id="recordsTable">
        <thead>
            <tr>
                @foreach($columns as $column)
                    <th class="text-left">{{ trans('fields.' . $column) }}</th>
                @endforeach
                <th>{{ trans('fields.Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $record)
            <tr>
                @foreach($columns as $column)
                    <td class="text-left">{{ $record->$column }}</td>
                @endforeach
                <td>
                    <a href="{{ route('simpleWorkflow.entities.editRecord', [$entity->id, $record->id]) }}">{{ trans('fields.Edit') }}</a>
                    <form action="{{ route('simpleWorkflow.entities.deleteRecord', [$entity->id, $record->id]) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-link p-0 ms-2">{{ trans('fields.Delete') }}</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

@section('script')
<script>
    $('#recordsTable').DataTable({
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Persian.json"
        }
    });
</script>
@endsection
