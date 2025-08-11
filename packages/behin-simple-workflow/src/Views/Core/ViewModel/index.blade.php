@extends('behin-layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="casrd">
                    <div class="card-body">
                        <a href="{{ route('simpleWorkflow.view-model.create') }}" class="btn btn-sm btn-info">{{ trans('fields.Create') }}</a>
                    </div>
                </div>
                <div class="card table-responsive">
                    <div class="card-header">
                        <h3 class="card-title">{{ trans('View Models') }}</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>{{ trans('fields.ID') }}</th>
                                    <th>{{ trans('fields.Name') }}</th>
                                    <th>{{ trans('fields.Entity Name') }}</th>
                                    <th>{{ trans('fields.Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($viewModels as $viewModel)
                                    <tr>
                                        <td>{{ $viewModel->id }} <input type="hidden" name="id"
                                                value="{{ $viewModel->id }}"></td>
                                        <td>{{ $viewModel->name }}</td>
                                        <td>
                                            {{ $viewModel->entity->name }}
                                        </td>
                                        <td><a class="btn btn-primary"
                                                href="{{ route('simpleWorkflow.view-model.edit', $viewModel->id) }}">
                                                {{ trans('fields.Edit') }}</a>

                                            <a href="{{ route('simpleWorkflow.view-model.copy', $viewModel->id) }}" class="btn btn-info">Copy</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
