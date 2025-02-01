@extends('behin-layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card table-responsive">
                    <div class="card-header">
                        <h3 class="card-title">{{ trans('Conditions') }}</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>{{ trans('fields.ID') }}</th>
                                    <th>{{ trans('fields.Name') }}</th>
                                    <th>{{ trans('fields.Content') }}</th>
                                    <th>{{ trans('fields.Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($conditions as $condition)
                                    <tr>
                                        <td>{{ $condition->id }} <input type="hidden" name="id"
                                                value="{{ $condition->id }}"></td>
                                        <td>{{ $condition->name }}</td>
                                        <td>
                                            @if ($condition->content)
                                                @foreach (json_decode($condition->content) as $c)
                                                    {{ $c->fieldName }} {{ $c->operation }} {{ $c->value }}
                                                @endforeach
                                            @endif

                                        </td>
                                        <td><a class="btn btn-primary"
                                                href="{{ route('simpleWorkflow.conditions.edit', $condition->id) }}">
                                                {{ trans('fields.Edit') }}</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4">
                                        <form action="{{ route('simpleWorkflow.conditions.store') }}" method="POST">
                                            @csrf
                                            <div class="form-group">
                                                <label for="name">{{ trans('fields.Name') }}</label>
                                                <input type="text" name="name" id="name" class="form-control"
                                                    required>
                                            </div>
                                            <button type="submit" class="btn btn-success">{{ trans('Create') }}</button>
                                        </form>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
