@extends('behin-layouts.app')
@php
    $index = 0;
    $content = json_decode($condition->content);
@endphp

@section('content')
    <h1>{{ trans('fields.Edit Condition') }}</h1>
    <h2>{{ $condition->name }}</h2>
    <div class="container p-4 border rounded shadow-sm bg-light table-responsive">
        <form action="{{ route('simpleWorkflow.conditions.update', $condition->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="name" class="form-label">{{ trans('Name') }}</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ $condition->name }}" required>
            </div>
            <div class="mb-3">
                <label for="next_if_true" class="form-label">{{ trans('Next If True') }}</label>
                <input type="text" name="next_if_true" id="next_if_true" class="form-control"
                    value="{{ $condition->next_if_true }}" >
            </div>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>{{ trans('fields.Id') }}</th>
                        <th>{{ trans('fields.Field Name') }}</th>
                        <th>{{ trans('fields.Operation') }}</th>
                        <th>{{ trans('fields.Value') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @if (is_array($content))
                        @foreach ($content as $row)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><input type="text" name="fieldName[{{ $index }}]" class="form-control"
                                        value="{{ $row->fieldName }}" list="fields">
                                    <datalist id="fields">
                                        @foreach (getProcessFields() as $field)
                                            <option value="{{ $field->name }}"
                                                {{ $field->name == $row->fieldName ? 'selected' : '' }}>
                                                ({{ $field->type }})
                                                {{ $field->name }} {{ trans('fields.' . $field->name) }}</option>
                                        @endforeach
                                    </datalist>
                                </td>
                                <td>
                                    <select name="operation[{{ $index }}]" id="" class="form-control">
                                        <option value="=" {{ $row->operation == '=' ? 'selected' : '' }}>=</option>
                                        <option value=">" {{ $row->operation == '>' ? 'selected' : '' }}>></option>
                                        <option value="<" {{ $row->operation == '<' ? 'selected' : '' }}>
                                            <</option>
                                        <option value=">=" {{ $row->operation == '>=' ? 'selected' : '' }}>>=
                                        </option>
                                        <option value="<=" {{ $row->operation == '<=' ? 'selected' : '' }}>
                                            <=</option>
                                        <option value="!=" {{ $row->operation == '!=' ? 'selected' : '' }}>
                                            !=</option>
                                    </select>
                                </td>
                                <td><input type="text" name="value[{{ $index }}]" class="form-control"
                                        value="{{ $row->value }}"></td>
                                <td>
                                    <button class="btn btn-danger" type="button" onclick="removeTr(this)"><i
                                            class="fa fa-trash"></i></button>
                                </td>
                            </tr>
                            @php
                                $index++;
                            @endphp
                        @endforeach
                    @endif

                </tbody>
                <tfoot>
                    <tr>
                        <td></td>
                        <td><input type="text" name="fieldName[{{ $index }}]" class="form-control"
                                value="" list="fields">
                            <datalist id="fields">
                                @foreach (getProcessFields() as $field)
                                    <option value="{{ $field->name }}">
                                        ({{ $field->type }})
                                        {{ $field->name }} {{ trans('fields.' . $field->name) }}</option>
                                @endforeach
                            </datalist>
                        </td>
                        <td>
                            <select name="operation[{{ $index }}]" id="" class="form-control">
                                <option value="=" >=</option>
                                <option value=">" >></option>
                                <option value="<" ><</option>
                                <option value=">=" >>=</option>
                                <option value="<=" ><=</option>
                                <option value="!="> !=</option>
                            </select>
                        </td>
                        <td><input type="text" name="value[{{ $index }}]" class="form-control" id=""
                                value="">
                        </td>
                        <td><button class="btn btn-success" type="submit">{{ trans('Edit') }}</button></td>
                    </tr>
                </tfoot>
            </table>
        </form>

    </div>
    <script>
        function removeTr(element) {
            element.parentNode.parentNode.remove();
        }
    </script>
@endsection
