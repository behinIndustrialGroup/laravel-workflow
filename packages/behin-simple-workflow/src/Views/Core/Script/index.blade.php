@extends('behin-layouts.app')

@section('title')
    {{ trans('fields.Script List') }}
@endsection

@section('content')
    <div class="container card p-3 mb-3">
        <form action="{{ route('simpleWorkflow.scripts.import') }}" method="POST" enctype="multipart/form-data" class="row">
            @csrf
            <div class="col-sm-8">
                <input type="file" name="scripts_file" class="form-control" accept="application/json">
            </div>
            <div class="col-sm-4">
                <button class="btn btn-primary">{{ trans('Import') }}</button>
            </div>
        </form>
    </div>
    <div class="card table-responsive">
        <div class="card-header">
            <h3 class="card-title">{{ trans('fields.Script List') }}</h3>
            <div class="card-tools">
                <a href="{{ route('simpleWorkflow.scripts.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> {{ trans('Create New Script') }}
                </a>
            </div>
        </div>
        <form action="{{ route('simpleWorkflow.scripts.export') }}" method="POST" id="export-form">
            @csrf
            <div class="card-body p-0">
                <table id="scripts-table" class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="select-all"></th>
                            <th>{{ trans('fields.Name') }}</th>
                            <th>{{ trans('Executive File') }}</th>
                            <th style="width: 20%" class="text-center">{{ trans('fields.Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($scripts as $script)
                            <tr>
                                <td><input type="checkbox" name="script_ids[]" value="{{ $script->id }}"></td>
                                <td class="text-left">{{ $script->name }}</td>
                                <td class="text-left">{{ $script->executive_file }}</td>
                                <td class="project-actions text-right">
                                    <a class="btn btn-primary btn-sm" href="{{ route('simpleWorkflow.scripts.edit', $script->id) }}">
                                        <i class="fas fa-pencil-alt"></i> {{ trans('Edit') }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">{{ trans('fields.No records found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <button class="btn btn-warning">{{ trans('Export Selected') }}</button>
            </div>
        </form>
    </div>
@endsection

@section('script')
    <script>
        initial_view();
        $('#scripts-table').DataTable({
            language: {
                url: 'https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Persian.json'
            }
        });
        $('#select-all').on('click', function(){
            $('input[name="script_ids[]"]').prop('checked', this.checked);
        });
    </script>
@endsection
