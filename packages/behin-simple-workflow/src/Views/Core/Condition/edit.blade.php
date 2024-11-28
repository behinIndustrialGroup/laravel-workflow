@extends('behin-layouts.app')
@php
    $index = 0;
    $content = json_decode($condition->content);
@endphp

@section('content')
    <h1>Edit Script</h1>
    <div class="container">
        <form action="{{ route('simpleWorkflow.conditions.update', $condition->id) }}" method="POST">
            @csrf
            @method('PUT')
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ trans('Id') }}</th>
                        <th>{{ trans('Field Name') }}</th>
                        <th>{{ trans('Operation') }}</th>
                        <th>{{ trans('Value') }}</th>
                        <th>{{ trans('Task') }}</th>
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
                                <td><input type="text" name="operation[{{ $index }}]" value="{{ $field->operation }}"></td>
                                <td><input type="text" name="value[{{ $index }}]" value="{{ $field->value }}"></td>
                                <td><input type="text" name="task[{{ $index }}]" value="{{ $field->task }}"></td>
                                <td></td>
                            </tr>
                            @php
                                $index++;
                            @endphp
                        @endforeach
                    @endif

                </tbody>
                <tfoot>
                    {{-- <input type="hidden" name="formId" value="{{ $condition->id }}"> --}}
                    <tr>
                        <td></td>
                        <td><input type="text" name="fieldName[{{ $index }}]" id="" value=""></td>
                        <td><input type="text" name="operation[{{ $index }}]" id="" value="">
                        <td><input type="text" name="value[{{ $index }}]" id="" value="">
                        <td><input type="text" name="task[{{ $index }}]" id="" value="">
                        </td>
                        <td><button class="btn btn-success">{{ trans('Edit') }}</button></td>
                    </tr>
                </tfoot>
            </table>
        </form>

    </div>
@endsection
