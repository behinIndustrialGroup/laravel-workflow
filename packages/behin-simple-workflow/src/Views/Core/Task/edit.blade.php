@extends('behin-layouts.app')

@php
    $forms = getProcessForms();
    $scripts = getProcessScripts();
    $conditions = getProcessConditions();
    $bgColor = '';
    if ($task->type == 'form') {
        $bgColor = 'primary';
    }
    if ($task->type == 'script') {
        $bgColor = 'success';
    }
    if ($task->type == 'condition') {
        $bgColor = 'warning';
    }
    if ($task->type == 'end') {
        $bgColor = 'danger';
    }
    if ($task->type == 'timed_condition') {
        $bgColor = 'info';
    }
@endphp

@section('content')
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    <div class="mb-3">
        <a href="{{ route('simpleWorkflow.task.index', $task->process_id) }}" class="btn btn-outline-secondary">
            <i class="fa fa-arrow-left"></i> {{ trans('Back to list') }}
        </a>
    </div>
    <form action="{{ route('simpleWorkflow.task.update', $task->id) }}" method="POST" class="card shadow-sm mb-4">
        @csrf
        @method('PUT')
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">{{ $task->name }}</h5>
            <span class="badge bg-{{ $bgColor }}">{{ ucfirst($task->type) }}</span>
        </div>
        <div class="card-body row g-3">
            <div class="col-md-6">
                <label for="id" class="form-label">{{ trans('ID') }}</label>
                <input type="text" name="id" id="id" class="form-control" value="{{ $task->id }}" readonly>
            </div>
            <div class="col-md-6">
                <label for="name" class="form-label">{{ trans('Name') }}</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ $task->name }}">
            </div>
            <div class="col-md-6">
                <label for="executive_element_id" class="form-label">{{ trans('Executive File') }}</label>
                <select name="executive_element_id" id="executive_element_id" class="form-control select2">
                    <option value="">{{ trans('Select an option') }}</option>
                    @if ($task->type == 'form')
                        @foreach ($forms as $form)
                            <option value="{{ $form->id }}" {{ $form->id == $task->executive_element_id ? 'selected' : '' }}>
                                {{ $form->name }}
                            </option>
                        @endforeach
                    @endif
                    @if ($task->type == 'script')
                        @foreach ($scripts as $script)
                            <option value="{{ $script->id }}" {{ $script->id == $task->executive_element_id ? 'selected' : '' }}>
                                {{ $script->name }}
                            </option>
                        @endforeach
                    @endif
                    @if ($task->type == 'condition')
                        @foreach ($conditions as $condition)
                            <option value="{{ $condition->id }}" {{ $condition->id == $task->executive_element_id ? 'selected' : '' }}>
                                {{ $condition->name }}
                            </option>
                        @endforeach
                    @endif
                </select>
                @if ($task->type == 'form' and $task->executive_element_id)
                    <a href="{{ route('simpleWorkflow.form.edit', ['id' => $task->executive_element_id]) }}">
                        {{ trans('Edit') }}
                    </a>
                @endif
                @if ($task->type == 'script' and $task->executive_element_id)
                    <a href="{{ route('simpleWorkflow.scripts.edit', ['script' => $task->executive_element_id]) }}">
                        {{ trans('Edit') }}
                    </a>
                @endif
                @if ($task->type == 'condition' and $task->executive_element_id)
                    <a href="{{ route('simpleWorkflow.conditions.edit', ['condition' => $task->executive_element_id]) }}">
                        {{ trans('Edit') }}
                    </a>
                @endif
                @if ($task->type == 'timed_condition' and $task->executive_element_id)
                    <a href="{{ route('simpleWorkflow.conditions.edit', ['condition' => $task->executive_element_id]) }}">
                        {{ trans('Edit') }}
                    </a>
                @endif
            </div>
            <div class="col-md-6">
                <label for="parent_id" class="form-label">{{ trans('Parent Task') }}</label>
                <select name="parent_id" id="parent_id" class="form-control select2">
                    <option value="">{{ trans('None') }}</option>
                    @foreach ($task->process->tasks() as $item)
                        <option dir="ltr" value="{{ $item->id }}" {{ $item->id == $task->parent_id ? 'selected' : '' }}>
                            {{ $item->name }} ({{ $item->id }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label for="next_element_id" class="form-label">{{ trans('Next Element') }}</label>
                <select name="next_element_id" id="next_element_id" class="form-control select2">
                    <option value="">{{ trans('None') }}</option>
                    @foreach ($task->process->tasks() as $item)
                        <option value="{{ $item->id }}" {{ $item->id == $task->next_element_id ? 'selected' : '' }}>
                            {{ $item->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label for="assignment_type" class="form-label">{{ trans('Assignment') }}</label>
                <select name="assignment_type" id="assignment_type" class="form-control">
                    <option value="">{{ trans('None') }}</option>
                    <option value="normal" {{ $task->assignment_type == 'normal' ? 'selected' : '' }}>{{ trans('Normal') }}</option>
                    <option value="dynamic" {{ $task->assignment_type == 'dynamic' ? 'selected' : '' }}>{{ trans('Dynamic') }}</option>
                    <option value="public" {{ $task->assignment_type == 'public' ? 'selected' : '' }}>{{ trans('Public') }}</option>
                </select>
            </div>
            <div class="col-md-6">
                <label for="case_name" class="form-label">{{ trans('Case Name') }}</label>
                <input type="text" name="case_name" id="case_name" class="form-control" dir="ltr" value="{{ $task->case_name }}">
            </div>
            <div class="col-md-6">
                <label for="duration" class="form-label">{{ trans('Duration') }}</label>
                <div class="input-group">
                    <input type="text" name="duration" id="duration" class="form-control" dir="ltr" value="{{ $task->duration }}">
                    <span class="input-group-text">{{ trans('fields.Minutes') }}</span>
                </div>
            </div>
            <div class="col-md-6">
                <label for="color" class="form-label">{{ trans('Color') }}</label>
                <div class="input-group">
                    <input type="text" name="color" class="form-control" dir="ltr" value="{{ $task->color }}">
                    <input type="color" id="color" class="form-control form-control-color" value="{{ $task->color }}">
                </div>
                <script>
                    document.getElementById('color').addEventListener('change', function () {
                        $('input[name=color]').val(this.value);
                    });
                </script>
            </div>
            <div class="col-md-6">
                <label for="background" class="form-label">{{ trans('Background') }}</label>
                <div class="input-group">
                    <input type="text" name="background" class="form-control" dir="ltr" value="{{ $task->background }}">
                    <input type="color" id="background" class="form-control form-control-color" value="{{ $task->background }}">
                </div>
                <script>
                    document.getElementById('background').addEventListener('change', function () {
                        $('input[name=background]').val(this.value);
                    });
                </script>
            </div>
            <div class="col-md-6">
                <label for="order" class="form-label">{{ trans('Order') }}</label>
                <input type="text" name="order" id="order" class="form-control" dir="ltr" value="{{ $task->order }}">
            </div>
            @if ($task->type == 'timed_condition')
                <div class="col-md-6">
                    <label for="timing_type" class="form-label">{{ trans('Timing Type') }}</label>
                    <select name="timing_type" id="timing_type" class="form-control">
                        <option value="static" {{ $task->timing_type == 'static' ? 'selected' : '' }}>{{ trans('fields.static') }}</option>
                        <option value="dynamic" {{ $task->timing_type == 'dynamic' ? 'selected' : '' }}>{{ trans('fields.dynamic') }}</option>
                    </select>
                </div>
            @endif
            @if ($task->timing_type == 'static')
                <div class="col-md-6">
                    <label for="timing_value" class="form-label">{{ trans('Timing Value') }}</label>
                    <input type="text" name="timing_value" id="timing_value" class="form-control" dir="ltr" value="{{ $task->timing_value }}">
                </div>
            @endif
            @if ($task->timing_type == 'dynamic')
                <div class="col-md-6">
                    <label for="timing_key_name" class="form-label">{{ trans('Timing Key') }}</label>
                    <input type="text" name="timing_key_name" id="timing_key_name" class="form-control" dir="ltr" value="{{ $task->timing_key_name }}">
                </div>
            @endif
        </div>
        <div class="card-footer text-start">
            <button type="submit" class="btn btn-primary">{{ trans('Edit') }}</button>
        </div>
    </form>
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">{{ trans('Task Actors') }}</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <td>{{ trans('Row') }}</td>
                            <td>{{ trans('ID') }}</td>
                            <td>{{ trans('Task') }}</td>
                            <td>{{ trans('Task Assignment Type') }}</td>
                            <td>{{ trans('Actor') }}</td>
                            <td>{{ trans('Role') }}</td>
                            <td>{{ trans('Created at') }}</td>
                            <td>{{ trans('Action') }}</td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($task->actors as $key => $value)
                            <tr>
                                <td>{{ $key + 1 }} </td>
                                <td>{{ $value->id }}</td>
                                <td>{{ $value->task->name }}</td>
                                <td>{{ $value->task->assignment_type }}</td>
                                <td>{{ is_numeric($value->actor) ? getUserInfo($value->actor)?->name : $value->actor }}</td>
                                <td>{{ $value->role?->name }}</td>
                                <td>{{ $value->created_at }}</td>
                                <td>
                                    <form action="{{ route('simpleWorkflow.task-actors.destroy', $value->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger">{{ trans('Delete') }}</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <form action="{{ route('simpleWorkflow.task-actors.store') }}" method="POST">
                        @csrf
                        <tfoot>
                            <tr>
                                <td></td>
                                <td></td>
                                <td>
                                    <input type="text" name="task_id" value="{{ $task->id }}" class="d-none">
                                    <input type="text" name="task_name" value="{{ $task->name }}" class="form-control">
                                </td>
                                <td></td>
                                <td>
                                    <input type="text" name="actor" list="actors">
                                    <datalist id="actors">
                                        @foreach (App\Models\User::all() as $actor)
                                            <option value="{{ $actor->id }}">{{ $actor->name }}</option>
                                        @endforeach
                                    </datalist>
                                </td>
                                <td>
                                    <select name="role_id">
                                        <option value=""></option>
                                        @foreach (BehinUserRoles\Models\Role::all() as $role)
                                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td><button class="btn btn-sm btn-primary">{{ trans('Create') }}</button></td>
                            </tr>
                        </tfoot>
                    </form>
                </table>
            </div>
        </div>
    </div>
    <div class="card shadow-sm mt-4">
        @include('SimpleWorkflowView::Core.TaskJump.edit', ['task' => $task])
    </div>
    <div class="card shadow-sm mt-4">
        <form action="{{ route('simpleWorkflow.task.update', $task->id) }}" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="task_id" value="{{ $task->id }}">
            <div class="row col-sm-12">
                <div class="row col-sm-6">
                    <label for="number_of_task_to_back">{{ trans('fields.Number of task to back') }}</label>
                    <input type="text" name="number_of_task_to_back" id="number_of_task_to_back" class="form-control" dir="ltr" value="{{ $task->number_of_task_to_back }}">
                </div>
            </div>
            <button type="submit" class="btn btn-primary" style="float: left">{{ trans('Edit') }}</button>
        </form>
    </div>
@endsection

@section('script')
    <script>
        initial_view();
    </script>
@endsection
