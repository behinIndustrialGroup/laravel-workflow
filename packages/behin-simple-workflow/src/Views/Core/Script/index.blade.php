@extends('behin-layouts.app')

@section('title')
    {{ trans('fields.Script List') }}
@endsection

@section('content')
    <div class="card table-responsive">
        <div class="card-header">
            <h3 class="card-title">{{ trans('fields.Script List') }}</h3>
            <div class="card-tools">
                <a href="{{ route('simpleWorkflow.scripts.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> {{ trans('Create New Script') }}
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            <table id="scripts-table" class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th style="width: 1%">#</th>
                        <th>{{ trans('fields.Name') }}</th>
                        <th>{{ trans('Executive File') }}</th>
                        <th style="width: 20%" class="text-center">{{ trans('fields.Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($scripts as $script)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
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
    </script>
@endsection
