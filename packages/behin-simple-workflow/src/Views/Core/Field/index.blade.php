@extends('behin-layouts.app')

@section('title')
    متغیر ها
@endsection

@section('content')
    <div class="container">
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
        <table class="table table-strpped">
            <thead>
                <tr>
                    <th>{{ trans('ID') }}</th>
                    <th>{{ trans('Name') }}</th>
                    <th>{{ trans('Type') }}</th>
                    <th>{{ trans('Query') }}</th>
                    <th>{{ trans('Placeholder') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($fields as $key => $field)
                    @php
                        $attributes = json_decode($field->attributes);
                    @endphp
                    <tr>
                        <td>{{ $key }}</td>
                        <td><input type="text" name="name" id="" value="{{ $field->name }}"></td>
                        <td><input type="text" name="type" id="" value="{{ $field->type }}"></td>
                        <td>
                            <textarea name="query" id="" cols="30" rows="10" dir="ltr">{{ is_string($attributes?->query) ? $attributes?->query : '' }}</textarea>
                        </td>
                        <td><input type="text" name="placeholder" id="" value="{{ $attributes?->placeholder }}">
                        </td>
                    </tr>
                @endforeach
                <form action="{{ route('simpleWorkflow.fields.store') }}" method="POST">
                    @csrf
                    <tr>
                        <td></td>
                        <td><input type="text" name="name" id=""></td>
                        <td><select name="type" id="">
                                <option value="string">string</option>
                                <option value="number">number</option>
                                <option value="text">text</option>
                                <option value="date">date</option>
                                <option value="select">select</option>
                                <option value="file">file</option>
                                <option value="checkbox">checkbox</option>
                                <option value="radio">radio</option>
                            </select></td>
                        <td>
                            <textarea name="query" id="" cols="30" rows="10"></textarea>
                        </td>
                        <td><input type="text" name="placeholder" id=""></td>
                    </tr>
                    <td>
                        <button class="btn btn-default">{{ trans('Create') }}</button>
                    </td>
                </form>
            </tbody>
        </table>
    </div>
@endsection
