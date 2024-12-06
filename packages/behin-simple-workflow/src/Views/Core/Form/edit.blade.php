@extends('behin-layouts.app')

@section('title', trans('Edit Form') . ' - ' . $form->name)

@php
    $index = 0;
    $content = json_decode($form->content);
    $content = collect($content)->sortBy('order')->toArray();
@endphp

@section('content')
    <div class="container">
        <div class="card row col-sm-12">

            <div class="col-md-12">
                <form action="{{ route('simpleWorkflow.form.update') }}" method="POST" class="mb-3">
                    @csrf
                    <input type="hidden" name="formId" value="{{ $form->id }}">

                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>{{ trans('Id') }}</th>
                                <th>{{ trans('Order') }}</th>
                                <th>{{ trans('Field Name') }}</th>
                                <th>{{ trans('Required') }}</th>
                                <th>{{ trans('Read Only') }}</th>
                                <th>{{ trans('Class') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (is_array($content))
                                @foreach ($content as $field)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td><input type="text" name="order[{{ $index }}]" id="" class="form-control text-center"
                                                value="{{ isset($field->order) ? $field->order : '' }}"></td>
                                        <td><input type="text" name="fieldName[{{ $index }}]" class="form-control text-center"
                                                value="{{ $field->fieldName }}"></td>
                                        <td><input type="checkbox" name="required[{{ $index }}]"
                                                {{ $field->required == 'on' ? 'checked' : '' }}></td>
                                        <td>
                                            <input type="checkbox" name="readOnly[{{ $index }}]"
                                                {{ $field->readOnly == 'on' ? 'checked' : '' }}>
                                        </td>
                                        <td><input type="text" name="class[{{ $index }}]" class="form-control text-center"
                                                value="{{ $field->class }}" dir="ltr"></td>
                                        <td></td>
                                    </tr>
                                    @php
                                        $index++;
                                    @endphp
                                @endforeach
                            @endif

                        </tbody>
                    </table>
                    <button type="submit" class="btn btn-primary">{{ trans('Update') }}</button>
                </form>
                <form action="{{ route('simpleWorkflow.form.store') }}" method="POST" class="mb-3" id="createForm">
                    @csrf
                    <input type="hidden" name="formId" value="{{ $form->id }}">
                    <table class="table table-striped bg-info">
                        <thead>
                            <tr>
                                <th>{{ trans('Id') }}</th>
                                <th>{{ trans('Order') }}</th>
                                <th>{{ trans('Field Name') }}</th>
                                <th>{{ trans('Required') }}</th>
                                <th>{{ trans('Read Only') }}</th>
                                <th>{{ trans('Class') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <td></td>
                                <td><input type="text" name="order" id="" class="form-control text-center"></td>
                                <td>
                                    <select name="fieldName" id="" class="form-control select2">
                                        @foreach (getProcessFields() as $field)
                                            <option value="{{ $field->name }}">{{ $field->name }} ({{ $field->type }})
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td><input type="checkbox" name="required" id=""></td>
                                <td><input type="checkbox" name="readOnly" id=""></td>
                                <td><input type="text" name="class" id="" class="form-control text-center"></td>
                                <td><button class="btn btn-success">{{ trans('Create') }}</button></td>
                            </tr>
                        </tfoot>
                    </table>
                </form>
            </div>
            <div class="col-md-12">
                @include('SimpleWorkflowView::Core.Form.preview', ['form' => $form])
            </div>
        </div>

    </div>
@endsection

@section('script')
    <script>
        initial_view();
    </script>
@endsection
