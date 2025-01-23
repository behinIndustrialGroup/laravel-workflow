@extends('behin-layouts.app')

@section('title', trans('Edit Form') . ' - ' . $form->name)

@php
    $index = 0;
    $content = json_decode($form->content);
    $content = collect($content)->sortBy('order')->toArray();
@endphp

@section('content')
    <div class="container">
        <div class="card row col-sm-12 p-2">
            <a href="{{ route('simpleWorkflow.form.index') }}" class="btn btn-primary col-sm-2">{{ trans('Back To Forms') }}</a>
        </div>
        <div class="card row col-sm-12">
            <div class="col-md-12">
                <form action="{{ route('simpleWorkflow.form.updateContent') }}" method="POST" class="mb-3">
                    @csrf
                    <input type="hidden" name="formId" value="{{ $form->id }}">
                    <label for="">{{ trans('Form Name') }}:</label>
                    <input type="text" name="name" value="{{ $form->name }}" class="form-control" id="">
                    <label for="">{{ trans('Form Content') }}:</label>
                    <textarea name="content" class="form-control" dir="ltr" id="" cols="30" rows="50">{{ $form->content }}</textarea>
                    <button type="submit" class="btn btn-primary">{{ trans('Update') }}</button>
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

        function deleteField(index) {
            $('#tr_' + index).remove();
        }

    </script>
@endsection
