@extends('behin-layouts.app')

@section('title', $form->name)

@php
    $content = json_decode($form->content);
@endphp

@section('content')
    <div class="row bg-dark p-2">
        <div class="col-md-12">
            <h2>{{ $task->name }} - {{ $inbox->case_name }}</h2>
        {{ trans('fields.Case Number') }}: {{ $case->number }} <br>
        {{ trans('fields.Creator') }}: {{ getUserInfo($case->creator)->name }} <br>
        {{ trans('fields.Created At') }}: <span dir="ltr">{{ $case->created_at->format('Y-m-d H:i') }}</span>
            <span class="badge color-dark" style="float: left; color: dark">{{ $case->id }}</span>
        </div>
    </div>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="javascript:void(0)" method="POST" id="form" enctype="multipart/form-data" class="border p-2 bg-white">

        <input type="hidden" name="inboxId" id="" value="{{ $inbox->id }}">
        <input type="hidden" name="caseId" id="" value="{{ $case->id }}">
        <input type="hidden" name="taskId" id="" value="{{ $task->id }}">
        <input type="hidden" name="processId" id="" value="{{ $process->id }}">
        @include('SimpleWorkflowView::Core.Form.preview', [
            'form' => $form,
            'task' => $task,
            'case' => $case,
            'inbox' => $inbox,
            'variables' => $variables,
            'process' => $process,
        ])
    </form>

    <div class="row bg-white p-2 mt-2">
        <button class="btn btn-primary btn-sm m-1"
            onclick="saveForm()">{{ trans('fields.Save') }}</button>
        <button class="btn btn-danger btn-sm m-1"
            onclick="saveAndNextForm()">{{ trans('fields.Save and next') }}</button>
    </div>
@endsection

@section('script')
    <script>
        initial_view()

        function saveForm() {
            var form = $('#form')[0];
            var fd = new FormData(form);
            send_ajax_formdata_request(
                '{{ route('simpleWorkflow.routing.save') }}',
                fd,
                function(response) {
                    console.log(response);
                    if (response.status == 200) {
                        show_message(response.msg)
                        window.location.reload();
                    } else {
                        show_error(response.msg);
                    }
                }
            )
        }

        function saveAndNextForm() {
            var form = $('#form')[0];
            var fd = new FormData(form);
            send_ajax_formdata_request(
                '{{ route('simpleWorkflow.routing.saveAndNext') }}',
                fd,
                function(response) {
                    console.log(response);
                    if (response.status == 200) {
                        window.location.href = '{{ route('simpleWorkflow.inbox.index') }}';
                    } else {
                        show_error(response.msg);
                    }
                }
            )
        }
    </script>
@endsection
