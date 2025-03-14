@extends('behin-layouts.app')

@section('title')
    {{ trans('Process List') }}
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ trans('Process List') }}</h3>
                        <div class="card-tools">
                            <a href="{{ route('simpleWorkflow.process.create') }}" class="btn btn-success">{{ trans('Create') }}</a>
                        </div>
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-striped projects">
                            <thead>
                                <tr>
                                    <th style="width: 1%">#</th>
                                    <th>{{ trans('Name') }}</th>
                                    <th>{{ trans('Created at') }}</th>
                                    <th>{{ trans('Edit') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($processes as $key => $value)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $value->name }}</td>
                                        <td>{{ $value->created_at }}</td>
                                        <td class="project-actions text-right">
                                            <a class="btn btn-primary btn-sm" href="{{ route('simpleWorkflow.task.index', $value->id) }}">
                                                <i class="fas fa-pencil-alt"></i>
                                                {{ trans('Edit') }}
                                            </a>
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
