@extends('behin-layouts.app')

@section('title')
    متغیر ها
@endsection

@section('content')
<div class="container card p-3">
    <form action="{{ route('simpleWorkflow.fields.store') }}" method="POST" class="row">
        @csrf
        <div class="col-sm-4">
            <input type="text" name="name" class="form-control text-center">
        </div>
        <div class="col-sm-4">
            <select name="type" id="" class="form-control select2">
                <option value="string">string</option>
                <option value="number">number</option>
                <option value="text">text</option>
                <option value="date">date</option>
                <option value="select">select</option>
                <option value="select-multiple">select-multiple</option>
                <option value="file">file</option>
                <option value="checkbox">checkbox</option>
                <option value="radio">radio</option>
                <option value="title">title</option>
                <option value="location">location</option>
                <option value="div">div</option>
            </select>
        </div>
        <div class="col-sm-4">
            <button class="btn btn-default">{{ trans('Create') }}</button>
        </div>

    </form>
</div>
    <div class="container card">
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
        <table class="table table-strpped" dir="ltr" id="table">
            <thead>
                <tr>
                    <th>{{ trans('ID') }}</th>
                    <th>{{ trans('Name') }}</th>
                    <th>{{ trans('Type') }}</th>
                    <th>{{ trans('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($fields as $key => $field)
                    @php
                        $attributes = json_decode($field->attributes);
                    @endphp
                    <tr>
                        <td>{{ $key }}</td>
                        <td class="text-center">{{ $field->name }}</td>
                        <td class="text-center">{{ $field->type }}</td>

                        <td>
                            <a href="{{ route('simpleWorkflow.fields.edit', $field->id) }}" class="btn btn-default">{{ trans('Edit') }}</a>
                            <button class="btn btn-danger">{{ trans('Delete') }}</button>
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
        $('#table').DataTable({});
    </script>
@endsection
