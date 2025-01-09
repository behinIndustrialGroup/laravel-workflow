@extends('behin-layouts.app')

@section('title', $form->name)

@php
    $content = json_decode($form->content);
@endphp

@section('content')
    <div class="card shadow-sm mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">{{ $task->name }} - {{ $inbox->case_name }}</h6>
        </div>
        <div class="card-body">
            <p class="mb-0">
                {{ trans('fields.Case Number') }}: <span class="badge badge-secondary">{{ $case->number }}</span> <br>
                {{ trans('fields.Creator') }}: <span class="badge badge-light">{{ getUserInfo($case->creator)->name }}</span> <br>
                {{ trans('fields.Created At') }}: <span class="badge badge-light" dir="ltr">{{ $case->created_at->format('Y-m-d H:i') }}</span>
                <br>
                <span class="badge badge-light" style="color: dark">{{ $case->id }}</span>
            </p>
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
    <div class="card shadow-sm mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ $form->name }}</h6>
        </div>
        <div class="card-body">
            <form action="javascript:void(0)" method="POST" id="form" enctype="multipart/form-data" class="needs-validation" novalidate>
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
        </div>
    </div>

    <div class="d-flex justify-content-end bg-white p-2 mt-2">
        <button class="btn btn-sm btn-outline-primary m-1" onclick="saveForm()">
            <i class="fa fa-save"></i> {{ trans('fields.Save') }}
        </button>
        <button class="btn btn-sm btn-outline-danger m-1" onclick="saveAndNextForm()">
            <i class="fa fa-save"></i>  <i class="fa fa-arrow-left"></i>{{ trans('fields.Save and next') }}
        </button>
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
                        show_message('{{ trans('fields.Saved') }}')
                        // window.close();                        
                        window.location.href = '{{ route('simpleWorkflow.inbox.index') }}';
                    } else {
                        show_error(response.msg);
                    }
                }
            )
        }
    </script>
@endsection
