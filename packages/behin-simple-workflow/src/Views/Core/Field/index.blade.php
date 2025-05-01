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
            <select name="type" id="" class="form-control">
                <option value="string">string</option>
                <option value="number">number</option>
                <option value="text">text</option>
                <option value="date">date</option>
                <option value="time">time</option>
                <option value="select">select</option>
                <option value="select-multiple">select-multiple</option>
                <option value="file">file</option>
                <option value="checkbox">checkbox</option>
                <option value="radio">radio</option>
                <option value="location">location</option>
                <option value="signature">signature</option>
                <option value="entity">entity</option>
                <option value="title">title</option>
                <option value="div">div</option>
                <option value="button">button</option>
                <option value="help">help</option>
            </select>
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
                    <th class="text-left">{{ trans('fields.Type') }}</th>
                    <th>{{ trans('fields.Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($fields as $key => $field)
                    @php
                        $attributes = json_decode($field->attributes);
                    @endphp
                    <tr>
                        <td>{{ $key }}</td>
                        <td class="text-left">{{ trans("fields.".$field->name) }}</td>
                        <td class="text-left">{{ $field->name }}</td>
                        <td class="text-left">{{ $field->type }}</td>

                        <td>
                            <a href="{{ route('simpleWorkflow.fields.edit', $field->id) }}" class="btn btn-default">{{ trans('fields.Edit') }}</a>
                            <button class="btn btn-danger">{{ trans('fields.Delete') }}</button>
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
