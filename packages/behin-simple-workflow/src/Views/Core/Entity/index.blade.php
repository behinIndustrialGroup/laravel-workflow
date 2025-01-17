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
        <table class="table table-strpped" id="table">
            <thead>
                <tr>
                    <th>{{ trans('ID') }}</th>
                    <th class="text-left">{{ trans('fields.Name') }}</th>
                    <th class="text-left">{{ trans('fields.Name') }}</th>
                    <th>{{ trans('fields.Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($entities as $entity)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td class="text-left">{{ trans("fields.".$entity->name) }}</td>
                        <td class="text-left">{{ $entity->name }}</td>
                        <td>
                            <a href="{{ route('simpleWorkflow.entities.edit', $entity->id) }}">{{ trans('fields.Edit') }}</a> |
                            <a href="{{ route('simpleWorkflow.entities.destroy', $entity->id) }}" onclick="return confirm('{{ trans('messages.confirmDelete') }}')">{{ trans('fields.Delete') }}</a>
                        </td>
                    </tr>
                @endforeach

            </tbody>
            <tfoot id="createForm">

            </tfoot>
        </table>

    </div>
@endsection

@section('script')
    <script>
        initial_view();
        $('#table').DataTable({
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Persian.json"
            }
        });
    </script>
@endsection
