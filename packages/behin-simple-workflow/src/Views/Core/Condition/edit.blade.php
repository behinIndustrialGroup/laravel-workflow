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
                <input type="text" name="name" id="name" class="form-control" value="{{ $condition->name }}"
                    required>
            </div>
            <div class="mb-3">
                <label for="next_if_true" class="form-label">{{ trans('Next If True') }}</label>
                <input type="text" name="next_if_true" id="next_if_true" class="form-control"
                    value="{{ $condition->next_if_true }}">
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
                                            << /option>
                                        <option value=">=" {{ $row->operation == '>=' ? 'selected' : '' }}>>=
                                        </option>
                                        <option value="<=" {{ $row->operation == '<=' ? 'selected' : '' }}>
                                            <=< /option>
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
                        <td><input type="text" name="fieldName[{{ $index }}]" class="form-control" value=""
                                list="fields">
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
                                <option value="=">=</option>
                                <option value=">">></option>
                                <option value="<">
                                    << /option>
                                <option value=">=">>=</option>
                                <option value="<=">
                                    <=< /option>
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
        <div class="card">
            <div class="card-body">
                <form action="javascript:void(0)" method="POST" id="test-form" class="form-inline">
                    @csrf
                    <div class="mb-3">
                        <label for="caseId" class="form-label">{{ trans('Case Id') }}</label>
                        <input type="text" name="caseId" id="caseId" class="form-control" list="cases">
                        <datalist id="cases">
                            <option value="">{{ trans('fields.Choose') }}</option>
                            @foreach (getCases() as $case)
                                <option value="{{ $case->id }}">{{ $case->number }} {{ $case->process->name }}
                                </option>
                            @endforeach
                        </datalist>
                    </div>
                    <button type="submit" class="btn btn-primary ml-2"
                        onclick="test()">{{ trans('fields.Test') }}</button>
                </form>
                <h5 class="mt-3" dir="ltr">
                    <pre style="text-align: left; white-space: pre;" dir="ltr">{{ trans('fields.Result') }}</pre>
                </h5>
                <div id="result" dir="ltr" style="text-align: left; white-space: pre;"></div>
            </div>
        </div>
    </div>
    <script>
        function removeTr(element) {
            element.parentNode.parentNode.remove();
        }

        function test() {
            var form = $('#test-form')[0];
            var fd = new FormData(form);
            send_ajax_formdata_request(
                "{{ route('simpleWorkflow.conditions.test', $condition->id) }}",
                fd,
                function(response) {
                    console.log(response);
                    $('#result').html('<pre style="text-align: left; white-space: pre;" dir="ltr">' + response +
                        '</pre>');
                },
                function(er) {
                    console.log(er);
                    result = er.responseJSON.message
                    if (result) {
                        $('#result').html('<pre style="text-align: left; white-space: pre;" dir="ltr">' + result +
                            '</pre>');
                    } else {
                        $('#result').html('{{ trans('fields.True') }}')
                    }
                    hide_loading();
                }
            )
        }
    </script>
@endsection
