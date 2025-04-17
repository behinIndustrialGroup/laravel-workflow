@extends('behin-layouts.app')

@section('title', $form->name)

@php
    $content = json_decode($form->content);
@endphp

@section('content')
    @include('SimpleWorkflowView::Core.Partial.back-btn')
    <div class="card shadow-sm mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">{{ $task->name }} - {{ $inbox->case_name }}</h6>
        </div>
        <div class="card-body">
            <p class="mb-0">
                {{ trans('fields.Case Number') }}: <span class="badge badge-secondary">{{ $case->number }}</span> <br>
                {{ trans('fields.Creator') }}: <span class="badge badge-light">{{ getUserInfo($case->creator)->name }}</span>
                <br>
                {{ trans('fields.Created At') }}: <span class="badge badge-light"
                    dir="ltr">{{ toJalali($case->created_at)->format('Y-m-d H:i') }}</span>
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
            <form action="javascript:void(0)" method="POST" id="form" enctype="multipart/form-data"
                class="needs-validation" novalidate>
                <input type="hidden" name="inboxId" id="inboxId" value="{{ $inbox->id }}">
                <input type="hidden" name="caseId" id="caseId" value="{{ $case->id }}">
                <input type="hidden" name="taskId" id="taskId" value="{{ $task->id }}">
                <input type="hidden" name="processId" id="processId" value="{{ $process->id }}">
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

    @if (in_array($inbox->status, ['done', 'doneByOther']))
        <div class="card shadow-sm mb-2">
            <div class="card-body">
                <p class="m-0">
                    <i class="fa fa-check-circle text-success mr-2"></i>
                    {{ trans('fields.Done At') }}:
                    <span class="badge badge-light" dir="ltr">
                        {{ toJalali($inbox->updated_at)->format('Y-m-d H:i') }}
                    </span>
                    <br>
                    {{ trans('fields.Done By') }}:
                    <span class="badge badge-light">
                        {{ getUserInfo($inbox->actor)->name }}
                    </span>
                </p>
            </div>
        </div>
    @else
        <div class="d-flex justify-content-end bg-white p-2 mt-2">
            @if ($inbox->status == 'draft')
                <button class="btn btn-sm btn-outline-info m-1"
                    onclick="createCaseNumberAndSave()">{{ trans('fields.Create Case Number and Save') }}</button>
            @else
                @if ($task->jumps->count() > 0)
                    <button class="btn btn-sm btn-outline-warning m-1" onclick="showJumpModal()">
                        <i class="fa fa-send"></i> {{ trans('fields.Send Manully') }}
                    </button>
                @endif
                <button class="btn btn-sm btn-outline-primary m-1" onclick="saveForm()">
                    <i class="fa fa-save"></i> {{ trans('fields.Save') }}
                </button>
                <button class="btn btn-sm btn-outline-danger m-1" onclick="saveAndNextForm()">
                    <i class="fa fa-save"></i> <i class="fa fa-arrow-left"></i>{{ trans('fields.Save and next') }}
                </button>
            @endif
        </div>
    @endif
@endsection

@section('script')
    <script>
        initial_view()

        function createCaseNumberAndSave() {
            var form = $('#form')[0];
            var fd = new FormData(form);
            send_ajax_formdata_request(
                '{{ route('simpleWorkflow.routing.createCaseNumberAndSave') }}',
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
                        if(response.url){
                            window.location.href = response.url;
                        }else{
                            window.location.href = '{{ route('simpleWorkflow.inbox.index') }}';
                        }
                    } else {
                        show_error(response.msg);
                    }
                }
            )
        }

        function showJumpModal(task_id) {
            send_ajax_get_request(
                '{{ route('simpleWorkflow.task-jump.show', [$task->id , $inbox->id , $case->id , $process->id] ) }}',
                function(response) {
                    open_admin_modal_with_data(response, '', function() {
                        initial_view()
                    })
                }
            )
        }
    </script>
@endsection
