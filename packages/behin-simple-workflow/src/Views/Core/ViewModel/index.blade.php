@extends('behin-layouts.app')

@section('content')
    <div class="container card p-3 mt-3">
        <form action="{{ route('simpleWorkflow.view-model.import') }}" method="POST" enctype="multipart/form-data" class="row">
            @csrf
            <div class="col-sm-8">
                <input type="file" name="view_models_file" class="form-control" accept="application/json">
            </div>
            <div class="col-sm-4">
                <button class="btn btn-default">Import</button>
            </div>
        </form>
    </div>
    <div class="container mt-3">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <a href="{{ route('simpleWorkflow.view-model.create') }}" class="btn btn-sm btn-info">{{ trans('fields.Create') }}</a>
                    </div>
                </div>

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

                <div class="card table-responsive">
                    <div class="card-header">
                        <h3 class="card-title">{{ trans('View Models') }}</h3>
                    </div>
                    <div class="card-body">
                        <form action="javascript:void(0)" method="POST" id="export-form">
                            @csrf
                            <table class="table table-bordered" id="table">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" id="select-all"></th>
                                        <th>{{ trans('fields.ID') }}</th>
                                        <th>{{ trans('fields.Name') }}</th>
                                        <th>{{ trans('fields.Entity Name') }}</th>
                                        <th>{{ trans('fields.Created At') }}</th>
                                        <th>{{ trans('fields.Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($viewModels as $viewModel)
                                        <tr>
                                            <td><input type="checkbox" name="view_model_ids[]" value="{{ $viewModel->id }}"></td>
                                            <td>{{ $viewModel->id }}</td>
                                            <td>{{ $viewModel->name }}</td>
                                            <td>{{ $viewModel->entity->name }}</td>
                                            <td dir="ltr">{{ toJalali($viewModel->created_at) }}</td>
                                            <td>
                                                <a class="btn btn-sm btn-primary" href="{{ route('simpleWorkflow.view-model.edit', $viewModel->id) }}">
                                                    <i class="fa fa-edit"></i>{{ trans('fields.Edit') }}
                                                </a>
                                                <a href="{{ route('simpleWorkflow.view-model.copy', $viewModel->id) }}" class="btn btn-sm btn-info">
                                                    <i class="fa fa-copy"></i>Copy
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="mt-2">
                                <button class="btn btn-warning" id="export-selected">Export Selected</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $('#table').DataTable({
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Persian.json"
            },
            "order": [4,'desc']
        });
        $('#select-all').on('click', function() {
            $('input[name="view_model_ids[]"]').prop('checked', this.checked);
        });
        $('#export-selected').on('click', function() {
            var fd = new FormData($('#export-form')[0]);

            fetch("{{ route('simpleWorkflow.view-model.export') }}", {
                    method: 'POST',
                    body: fd
                })
                .then(res => res.blob())
                .then(blob => {
                    const url = URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = 'view-models.json';
                    document.body.appendChild(a);
                    a.click();
                    a.remove();
                    URL.revokeObjectURL(url);
                })
                .catch(err => console.error(err));
        });
    </script>
@endsection

