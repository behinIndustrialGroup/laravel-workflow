@extends('behin-layouts.app')

@section('content')
    <div class="container">
        <table class="table">
            <thead>
                <tr>
                    <th>{{ trans('Id') }}</th>
                    <th>{{ trans('Name') }}</th>
                    <th>{{ trans('Executive file') }}</th>
                    <th>{{ trans('Action') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($conditions as $condition)
                        <tr>
                            <td>{{ $condition->id }} <input type="hidden" name="id" value="{{ $condition->id }}"></td>
                            <td><input type="text" name="name" id="" value="{{ $condition->name }}"></td>
                            <td><input type="text" name="executive_file" id=""
                                    value="{{ $condition->executive_file }}">
                            </td>
                            <td><a class="btn btn-success"
                                    href="{{ route('simpleWorkflow.conditions.edit', $condition->id) }}">{{ trans('Edit') }}</a>
                            </td>
                        </tr>
                @endforeach
            </tbody>
            <tfoot>
                <form action="{{ route('simpleWorkflow.conditions.store') }}" method="POST">
                    @csrf
                    <tr>
                        <td></td>
                        <td><input type="text" name="name" id="" value=""></td>
                        <td><input type="text" name="executive_file" id="" value="">
                        </td>
                        <td><button class="btn btn-success">{{ trans('Create') }}</button></td>
                    </tr>
                </form>
            </tfoot>
        </table>
    </div>
@endsection

