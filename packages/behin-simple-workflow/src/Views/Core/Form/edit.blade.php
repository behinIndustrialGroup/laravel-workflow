@extends('behin-layouts.app')

@section('title', $form->name)

@php
    $index = 0;
    $content = json_decode($form->content);
    $content = collect($content)->sortBy('order')->toArray();
@endphp

@section('content')
    <div class="container">
        <div class="card shadow-sm p-3 mb-3">
            <div class="d-flex justify-content-between">
                <a href="{{ route('simpleWorkflow.form.index') }}" class="btn btn-outline-primary">
                    <i class="fa fa-arrow-left mr-2"></i> {{ trans('Back To Forms') }}
                </a>
                <a href="{{ route('simpleWorkflow.form.editScript', ['id' => $form->id]) }}" class="btn btn-primary">
                    <i class="fa fa-edit mr-2"></i> {{ trans('Edit Script') }}
                </a>
                <a href="{{ route('simpleWorkflow.form.editContent', ['id' => $form->id]) }}" class="btn btn-primary">
                    <i class="fa fa-edit mr-2"></i> {{ trans('Edit Content') }}
                </a>
            </div>
        </div>
        <div class="card row col-sm-12 p-2" style="border: 1px solid #0da95b !important;">
            <form action="{{ route('simpleWorkflow.form.store') }}" method="POST" class="mb-3 table-responsive" id="createForm">
                @csrf
                <input type="hidden" name="formId" value="{{ $form->id }}">
                <table class="table">
                    <thead>
                        <tr>
                            <th width="10">{{ trans('Order') }}</th>
                            <th>{{ trans('Field Name') }}</th>
                            <th>{{ trans('Required') }}</th>
                            <th>{{ trans('Read Only') }}</th>
                            <th>{{ trans('Class') }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <td style="width:20px"><input type="text" name="order" id="" class="form-control text-center">
                            </td>
                            <td>
                                <select name="fieldName" id="" class="form-control select2" dir="ltr">
                                    @include('SimpleWorkflowView::Core.Form.all-fields-options', [
                                        'form' => $form,
                                    ])
                                </select>
                            </td>
                            <td><input type="checkbox" name="required" id=""></td>
                            <td><input type="checkbox" name="readOnly" id=""></td>
                            <td><input type="text" name="class" id="" dir="ltr" class="form-control text-center">
                            </td>
                            <td><button class="btn btn-success">{{ trans('Create') }}</button></td>
                        </tr>
                    </tfoot>
                </table>
            </form>
        </div>
        <div class="card row col-sm-12" style="border: 1px solid #dc3545 !important;">
            <div class="col-md-12">
                <form action="{{ route('simpleWorkflow.form.update') }}" method="POST" class="mb-3">
                    @csrf
                    <input type="hidden" name="formId" value="{{ $form->id }}">
                    <div class="form-group bg-danger row">
                        <label for="name" class="col-sm-3 col-form-label">{{ trans('Form Name') }}:</label>
                        <input type="text" class="col-sm-9 form-control" name="name" value="{{ $form->name }}" class="form-control"
                            id="name" placeholder="{{ trans('Enter form name') }}">
                    </div>
                    <div class="accordion row" id="accordionExample">
                        @if (is_array($content))
                            @foreach ($content as $field)
                                <div class="card {{ $field->class ?? '' }}">
                                    <div class="card-header btn btn-light text-right" id="heading_{{ $index }}" type="button" data-toggle="collapse"
                                    data-target="#collapse_{{ $index }}" aria-expanded="true"
                                    aria-controls="collapse_{{ $index }}">
                                        {{ trans('fields.' . $field->fieldName) }}
                                    </div>

                                    <div id="collapse_{{ $index }}" class="collapse"
                                        aria-labelledby="heading_{{ $index }}" data-parent="#accordionExample">
                                        <div class="card-body table-responsive">
                                            <table class="table">
                                                <tr>
                                                    <td>{{ trans('Id') }}</td>
                                                    <td>{{ $index + 1 }}</td>
                                                </tr>
                                                <tr>
                                                    <td>{{ trans('Order') }}</td>
                                                    <td>
                                                        <input type="text" name="order[{{ $index }}]"
                                                            id="" class="form-control text-center"
                                                            value="{{ isset($field->order) ? $field->order : '' }}">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>{{ trans('Field Name') }}</td>
                                                    <td class="" style="width: 80px">
                                                        <select name="fieldName[{{ $index }}]" id=""
                                                            class="form-control select2" dir="ltr">
                                                            @include(
                                                                'SimpleWorkflowView::Core.Form.all-fields-options',
                                                                [
                                                                    'form' => $form,
                                                                    'selectedField' => $field->fieldName,
                                                                ]
                                                            )
                                                        </select>
                                                        @if ($editId = getFieldDetailsByName($field->fieldName)?->id)
                                                            <a class=""
                                                                href="{{ route('simpleWorkflow.fields.edit', ['field' => $editId]) }}"><i class="fa fa-edit"></i></a>
                                                        @else
                                                            <a class=""
                                                                href="{{ route('simpleWorkflow.form.edit', ['id' => $field->fieldName]) }}"><i class="fa fa-edit"></i></a>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>{{ trans('Required') }}</td>
                                                    <td>
                                                        <input type="checkbox" name="required[{{ $index }}]"
                                                            {{ $field->required == 'on' ? 'checked' : '' }}>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>{{ trans('Read Only') }}</td>
                                                    <td>
                                                        <input type="checkbox" name="readOnly[{{ $index }}]"
                                                            {{ $field->readOnly == 'on' ? 'checked' : '' }}>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>{{ trans('Class') }}</td>
                                                    <td>
                                                        <input type="text" name="class[{{ $index }}]"
                                                            class="form-control text-center" value="{{ $field->class }}"
                                                            dir="ltr">
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                @php
                                    $index++;
                                @endphp
                            @endforeach
                        @endif

                    </div>
                    <button type="submit" class="btn btn-danger">{{ trans('Update') }}</button>
                </form>

            </div>

        </div>
        <div class="card row col-sm-12 mb-4" style="border: 1px solid #1f9bda !important;">
            {{-- <div class="card-header bg-primary">{{ trans('Preview') }}</div> --}}
            <div class="row col-sm-12 p-0 m-0">
                @include('SimpleWorkflowView::Core.Form.preview', ['form' => $form])
            </div>
        </div>

    </div>
@endsection

@section('script')
    <script>
        initial_view();

        function deleteField(index) {
            $('#tr_' + index).remove();
        }
    </script>
@endsection
