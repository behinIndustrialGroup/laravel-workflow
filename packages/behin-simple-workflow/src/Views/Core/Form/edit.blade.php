@extends('behin-layouts.app')

@php
    $index = 0;
    $content = json_decode($form->content);
@endphp

@section('content')
    <div class="container">
        <form action="{{ route('simpleWorkflow.form.update') }}" method="POST">
            @csrf
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ trans('Id') }}</th>
                        <th>{{ trans('Field Name') }}</th>
                        <th>{{ trans('Required') }}</th>
                        <th>{{ trans('Class') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @if (is_array($content))
                        @foreach ($content as $field)
                            <tr>
                                <td></td>
                                <td><input type="text" name="fieldName[{{ $index }}]"
                                        value="{{ $field->fieldName }}"></td>
                                <td><input type="checkbox" name="required[{{ $index }}]"
                                        {{ isset($field->required) ? 'checked' : '' }}></td>
                                <td><input type="text" name="class[{{ $index }}]" value="{{ $field->class }}"></td>
                                <td></td>
                            </tr>
                            @php
                                $index++;
                            @endphp
                        @endforeach
                    @endif

                </tbody>
                <tfoot>
                    <input type="hidden" name="formId" value="{{ $form->id }}">
                    <tr>
                        <td></td>
                        <td>
                            <select name="fieldName[{{ $index }}]" id="">
                                @foreach (getProcessFields() as $field)
                                    <option value="{{ $field->name }}">{{ $field->name }} ({{ $field->type }})</option>
                                @endforeach
                            </select>
                        </td>
                        <td><input type="checkbox" name="required[{{ $index }}]" id=""></td>
                        <td><input type="text" name="class[{{ $index }}]" id=""></td>
                        <td><button class="btn btn-success">{{ trans('Create') }}</button></td>
                    </tr>
                </tfoot>
            </table>
        </form>

    </div>
@endsection

