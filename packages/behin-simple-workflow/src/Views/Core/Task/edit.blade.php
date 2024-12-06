@extends('behin-layouts.app')

@php
    $forms = getProcessForms();
    $scripts = getProcessScripts();
    $conditions = getProcessConditions();
@endphp

@section('content')
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    <div class="mb-3">
        <a href="{{ route('simpleWorkflow.task.index', $task->process_id) }}" class="btn btn-secondary">
            {{ trans('Back to list') }}
        </a>
    </div>
    <form action="{{ route('simpleWorkflow.task.update', $task->id) }}" method="POST" class="p-4 border rounded bg-light">
        @csrf
        @method('PUT')
        <div class="panel-heading p-2 bg-light">
            <a data-toggle="collapse" href="#{{ $task->id }}">{{ $task->name }}</a>
            <span
                class="badge bg-{{ $task->type == 'form' ? 'primary' : ($task->type == 'script' ? 'success' : 'warning') }}">
                {{ ucfirst($task->type) }}
            </span>
            <input type="hidden" name="id" value="{{ $task->id }}">
            <div class="row mb-3">
                <label for="parent_id" class="col-sm-2 col-form-label">{{ trans('Name') }}</label>
                <input type="text" name="name" id="" class="form-control" value="{{ $task->name }}">
            </div>
            <div class="row mb-3">
                <label for="parent_id" class="col-sm-2 col-form-label">{{ trans('Executive File') }}</label>
                <div class="col-sm-10">
                    <select name="executive_element_id" class="form-control select2">
                        <option value="">{{ trans('Select an option') }}</option>
                        @if ($task->type == 'form')
                                @foreach ($forms as $form)
                                    <option value="{{ $form->id }}"
                                        {{ $form->id == $task->executive_element_id ? 'selected' : '' }}>
                                        {{ $form->name }}
                                    </option>
                                @endforeach
                            @endif
                            @if ($task->type == 'script')
                                @foreach ($scripts as $script)
                                    <option value="{{ $script->id }}"
                                        {{ $script->id == $task->executive_element_id ? 'selected' : '' }}>
                                        {{ $script->name }}
                                    </option>
                                @endforeach
                            @endif
                            @if ($task->type == 'condition')
                                @foreach ($conditions as $condition)
                                    <option value="{{ $condition->id }}"
                                        {{ $condition->id == $task->executive_element_id ? 'selected' : '' }}>
                                        {{ $condition->name }}
                                    </option>
                                @endforeach
                            @endif
                    </select>
                </div>
            </div>
            <div class="row mb-3">
                <label for="parent_id" class="col-sm-2 col-form-label">{{ trans('Parent Task') }}</label>
                <div class="col-sm-10">
                    <select name="parent_id" id="parent_id" class="form-control select2">
                        <option value="">{{ trans('None') }}</option>
                        @foreach ($task->process->tasks() as $item)
                            <option value="{{ $item->id }}" {{ $item->id == $task->parent_id ? 'selected' : '' }}>{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row mb-3">
                <label for="next_element_id" class="col-sm-2 col-form-label">{{ trans('Next Element') }}</label>
                <div class="col-sm-10">
                    <select name="next_element_id" id="next_element_id" class="form-control select2">
                        <option value="">{{ trans('None') }}</option>
                    @foreach ($task->process->tasks() as $item)
                        <option value="{{ $item->id }}" {{ $item->id == $task->next_element_id ? 'selected' : '' }}>{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row mb-3">
                <label for="assignment_type" class="col-sm-2 col-form-label">{{ trans('Assignment') }}</label>
                <div class="col-sm-10">
                    <select name="assignment_type" id="assignment_type" class="form-control">
                        <option value="">{{ trans('None') }}</option>
                        <option value="normal" {{ $task->assignment_type == 'normal' ? 'selected' : '' }}>{{ trans('Normal') }}</option>
                        <option value="dynamic" {{ $task->assignment_type == 'dynamic' ? 'selected' : '' }}>{{ trans('Dynamic') }}</option>
                    </select>
                </div>
            </div>
            <div class="row mb-3">
                <label for="case_name" class="col-sm-2 col-form-label">{{ trans('Case Name') }}</label>
                <div class="col-sm-10">
                    <input type="text" name="case_name" class="form-control" dir="ltr" value="{{ $task->case_name }}">
                </div>
            </div>
            <button type="submit" class="btn btn-primary" style="float: left">{{ trans('Edit') }}</button>



        </div>
    </form>
@endsection

@section('script')
    <script>
        initial_view();
    </script>
@endsection
