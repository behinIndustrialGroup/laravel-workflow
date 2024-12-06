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
        <table class="table table-strpped" dir="ltr">
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
                <form action="{{ route('simpleWorkflow.fields.store') }}" method="POST" >
                    @csrf
                    <tr>
                        <td></td>
                        <td><input type="text" name="name" class="form-control text-center"></td>
                        <td class="text-center form-select"><select name="type" id="">
                                <option value="string">string</option>
                                <option value="number">number</option>
                                <option value="text">text</option>
                                <option value="date">date</option>
                                <option value="select">select</option>
                                <option value="file">file</option>
                                <option value="checkbox">checkbox</option>
                                <option value="radio">radio</option>
                                <option value="title">title</option>
                                <option value="location">location</option>
                                <option value="div">div</option>
                            </select></td>
                            <td>
                                <button class="btn btn-default">{{ trans('Create') }}</button>
                            </td>
                    </tr>

                </form>
            </tfoot>
        </table>
    </div>
@endsection
