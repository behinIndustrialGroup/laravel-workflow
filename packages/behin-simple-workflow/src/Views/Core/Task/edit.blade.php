@extends('behin-layouts.app')

@section('style')
    <style>
        .material-card {
            border-radius: 16px;
            border: none;
            overflow: hidden;
        }

        .material-card .card-header {
            background: linear-gradient(135deg, #1976d2, #42a5f5);
            color: #fff;
            border-bottom: none;
            padding: 1.25rem 1.5rem;
        }

        .material-card .card-header .badge {
            background: rgba(255, 255, 255, 0.25);
            color: #fff;
            font-weight: 600;
            padding: 0.5rem 1rem;
            border-radius: 999px;
        }

        .material-card .card-body {
            background-color: #fafafa;
        }

        .material-tabs {
            border-bottom: none;
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 0;
            padding: 0 1.5rem;
        }

        .material-tabs > li {
            margin-bottom: 0;
        }

        .material-tabs > li > a {
            border: none !important;
            border-bottom: 2px solid transparent;
            border-radius: 8px 8px 0 0;
            color: #546e7a;
            font-weight: 600;
            letter-spacing: 0.3px;
            padding: 0.75rem 1rem;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
        }

        .material-tabs > li > a .material-icons {
            font-size: 18px;
            margin-left: 6px;
        }

        .material-tabs > li > a .md-tab-text {
            font-size: 0.9rem;
        }

        .material-tabs > li.active > a,
        .material-tabs > li > a:hover,
        .material-tabs > li > a:focus {
            background-color: rgba(25, 118, 210, 0.1) !important;
            border: none !important;
            border-bottom: 2px solid #1976d2 !important;
            color: #1976d2 !important;
        }

        .material-tab-content {
            background-color: #fff;
            border-radius: 0 0 16px 16px;
            box-shadow: inset 0 1px 0 rgba(0, 0, 0, 0.05);
            padding: 1.75rem 1.5rem;
        }

        .material-tab-content .row {
            margin-left: -0.5rem;
            margin-right: -0.5rem;
        }

        .material-tab-content .col-md-6,
        .material-tab-content .col-md-12 {
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }

        .md-form-group {
            margin-bottom: 1.75rem;
        }

        .md-form-group label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: #455a64;
            margin-bottom: 0.35rem;
        }

        .material-input,
        .material-select {
            border-radius: 10px;
            border: 1px solid #d0d7de;
            box-shadow: none;
            padding: 0.65rem 0.85rem;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .material-input:focus,
        .material-select:focus {
            border-color: #1976d2;
            box-shadow: 0 0 0 0.2rem rgba(25, 118, 210, 0.12);
        }

        .material-input[readonly] {
            background-color: #eceff1;
            color: #607d8b;
        }

        .material-input-group {
            display: flex;
            align-items: center;
            border: 1px solid #d0d7de;
            border-radius: 10px;
            overflow: hidden;
            background-color: #fff;
        }

        .material-input-group .material-input {
            border: none;
            border-radius: 0;
            flex: 1;
            box-shadow: none;
        }

        .material-input-group .material-input:focus {
            box-shadow: none;
        }

        .material-input-group span {
            background-color: #f1f5f9;
            color: #546e7a;
            font-weight: 600;
            padding: 0.65rem 1rem;
        }

        .material-color-picker {
            display: flex;
            gap: 0.75rem;
            align-items: center;
        }

        .material-color-picker input[type='color'] {
            border: none;
            width: 48px;
            height: 48px;
            padding: 0;
            background: transparent;
        }

        .material-link {
            color: #1976d2;
            display: inline-block;
            font-size: 0.82rem;
            font-weight: 600;
            margin-top: 0.35rem;
            text-decoration: none;
        }

        .material-link:hover {
            text-decoration: underline;
        }

        .material-card .card-footer {
            background-color: #fff;
            border-top: none;
            padding: 1.25rem 1.5rem;
        }

        .material-card .btn-primary {
            background-color: #1976d2;
            border: none;
            font-weight: 600;
            padding: 0.6rem 1.75rem;
        }

        .material-card .btn-primary:hover {
            background-color: #125ba1;
        }

        .material-card-header-secondary {
            background: linear-gradient(135deg, #26a69a, #4db6ac);
        }

        .material-section-title {
            font-size: 1.05rem;
            font-weight: 700;
            color: #37474f;
            margin-bottom: 1.5rem;
        }

        .material-subcard {
            border-radius: 14px;
            overflow: hidden;
        }

        .material-subcard .card-header {
            background: linear-gradient(135deg, #5c6bc0, #7986cb);
            color: #fff;
        }

        .material-subcard .card-header.material-card-header-secondary {
            background: linear-gradient(135deg, #26a69a, #4db6ac);
        }

        .material-subcard .card-body {
            background-color: #fff;
        }

        .material-subcard .card-header h5 {
            font-weight: 700;
            margin: 0;
        }

        .material-table thead {
            background-color: #f1f5f9;
        }

        .material-table thead th,
        .material-table thead td {
            color: #546e7a;
            font-weight: 600;
            border-bottom: none;
        }

        .material-table tbody tr:hover {
            background-color: rgba(25, 118, 210, 0.05);
        }

        .material-danger-card .card-header {
            background: linear-gradient(135deg, #d32f2f, #ef5350);
            color: #fff;
        }

        .material-header-icon {
            font-size: 20px;
            margin-left: 8px;
            vertical-align: middle;
        }
    </style>
@endsection

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
    $executiveEditRoute = null;
    if ($task->type == 'form' && $task->executive_element_id) {
        $executiveEditRoute = route('simpleWorkflow.form.edit', ['id' => $task->executive_element_id]);
    } elseif ($task->type == 'script' && $task->executive_element_id) {
        $executiveEditRoute = route('simpleWorkflow.scripts.edit', ['script' => $task->executive_element_id]);
    } elseif (in_array($task->type, ['condition', 'timed_condition']) && $task->executive_element_id) {
        $executiveEditRoute = route('simpleWorkflow.conditions.edit', ['condition' => $task->executive_element_id]);
    }
    $isTimedCondition = $task->type == 'timed_condition';
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

    <form action="{{ route('simpleWorkflow.task.update', $task->id) }}" method="POST"
        class="card shadow-sm mb-4 material-card">
        @csrf
        @method('PUT')
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <span class="material-icons material-header-icon">edit</span>
                {{ $task->name }}
            </h5>
            <div class="d-flex align-items-center">
                <span class="badge bg-{{ $bgColor }}">{{ ucfirst($task->type) }}</span>
                <span class="badge {{ $task->is_preview ? 'bg-secondary text-dark ml-2' : 'bg-success ml-2' }}">
                    {{ $task->is_preview ? trans('fields.Preview Mode') : trans('fields.Published') }}
                </span>
            </div>
        </div>
        <div class="card-body">
            <ul class="nav nav-tabs material-tabs" role="tablist">
                <li role="presentation" class="active">
                    <a href="#tab-general" aria-controls="tab-general" role="tab" data-toggle="tab" class="active">
                        <span class="material-icons">info</span>
                        <span class="md-tab-text">{{ __('Basic Info') }}</span>
                    </a>
                </li>
                <li role="presentation">
                    <a href="#tab-workflow" aria-controls="tab-workflow" role="tab" data-toggle="tab">
                        <span class="material-icons">hub</span>
                        <span class="md-tab-text">{{ __('Workflow') }}</span>
                    </a>
                </li>
                <li role="presentation">
                    <a href="#tab-appearance" aria-controls="tab-appearance" role="tab" data-toggle="tab">
                        <span class="material-icons">palette</span>
                        <span class="md-tab-text">{{ __('Appearance & Timing') }}</span>
                    </a>
                </li>
            </ul>
            <div class="tab-content material-tab-content">
                <div role="tabpanel" class="tab-pane fade show active" id="tab-general">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="md-form-group">
                                <label for="id">{{ trans('ID') }}</label>
                                <input type="text" name="id" id="id" class="form-control material-input"
                                    value="{{ $task->id }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="md-form-group">
                                <label for="name">{{ trans('Name') }}</label>
                                <input type="text" name="name" id="name" class="form-control material-input"
                                    value="{{ $task->name }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="md-form-group">
                                <label for="is_preview">{{ trans('fields.Preview Status') }}</label>
                                <select name="is_preview" id="is_preview" class="form-control material-select">
                                    <option value="1" {{ $task->is_preview ? 'selected' : '' }}>{{ trans('fields.Preview Mode') }}</option>
                                    <option value="0" {{ !$task->is_preview ? 'selected' : '' }}>{{ trans('fields.Published') }}</option>
                                </select>
                                <small class="text-muted d-block mt-1">{{ trans('fields.Preview Status Hint') }}</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="md-form-group">
                                <label for="show_save_button">{{ trans('fields.Show Save Button') }}</label>
                                <select name="show_save_button" id="show_save_button" class="form-control material-select">
                                    <option value="1" {{ $task->show_save_button ? 'selected' : '' }}>{{ trans('fields.Yes') }}</option>
                                    <option value="0" {{ !$task->show_save_button ? 'selected' : '' }}>{{ trans('fields.No') }}</option>
                                </select>
                            </div>
                            <div class="md-form-group">
                                <label for="show_reminder_button">{{ trans('fields.Show Reminder Button') }}</label>
                                <select name="show_reminder_button" id="show_reminder_button" class="form-control material-select">
                                    <option value="1" {{ $task->show_reminder_button ? 'selected' : '' }}>{{ trans('fields.Yes') }}</option>
                                    <option value="0" {{ !$task->show_reminder_button ? 'selected' : '' }}>{{ trans('fields.No') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="md-form-group">
                                <label for="parent_id">{{ trans('Parent Task') }}</label>
                                <select name="parent_id" id="parent_id"
                                    class="form-control material-select select2">
                                    <option value="">{{ trans('None') }}</option>
                                    @foreach ($task->process->tasks() as $item)
                                        <option dir="ltr" value="{{ $item->id }}"
                                            {{ $item->id == $task->parent_id ? 'selected' : '' }}>
                                            {{ $item->name }}
                                            @if ($item->is_preview)
                                                ({{ trans('fields.Preview') }})
                                            @endif
                                            ({{ $item->id }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="md-form-group">
                                <label for="next_element_id">{{ trans('Next Element') }}</label>
                                <select name="next_element_id" id="next_element_id"
                                    class="form-control material-select select2">
                                    <option value="">{{ trans('None') }}</option>
                                    @foreach ($task->process->tasks() as $item)
                                        <option value="{{ $item->id }}"
                                            {{ $item->id == $task->next_element_id ? 'selected' : '' }}>
                                            {{ $item->name }}
                                            @if ($item->is_preview)
                                                ({{ trans('fields.Preview') }})
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="md-form-group">
                                <label for="order">{{ trans('Order') }}</label>
                                <input type="text" name="order" id="order" class="form-control material-input"
                                    dir="ltr" value="{{ $task->order }}">
                            </div>
                        </div>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane fade" id="tab-workflow">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="md-form-group">
                                <label for="executive_element_id">{{ trans('Executive File') }}</label>
                                <select name="executive_element_id" id="executive_element_id"
                                    class="form-control material-select select2">
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
                                    @if (in_array($task->type, ['condition', 'timed_condition']))
                                        @foreach ($conditions as $condition)
                                            <option value="{{ $condition->id }}"
                                                {{ $condition->id == $task->executive_element_id ? 'selected' : '' }}>
                                                {{ $condition->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                @if ($executiveEditRoute)
                                    <a href="{{ $executiveEditRoute }}" class="material-link">
                                        {{ trans('Edit') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="md-form-group">
                                <label for="assignment_type">{{ trans('Assignment') }}</label>
                                <select name="assignment_type" id="assignment_type"
                                    class="form-control material-select">
                                    <option value="">{{ trans('None') }}</option>
                                    <option value="normal"
                                        {{ $task->assignment_type == 'normal' ? 'selected' : '' }}>
                                        {{ trans('Normal') }}
                                    </option>
                                    <option value="dynamic"
                                        {{ $task->assignment_type == 'dynamic' ? 'selected' : '' }}>
                                        {{ trans('Dynamic') }}
                                    </option>
                                    <option value="public"
                                        {{ $task->assignment_type == 'public' ? 'selected' : '' }}>
                                        {{ trans('Public') }}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="md-form-group">
                                <label for="case_name">{{ trans('Case Name') }}</label>
                                <input type="text" name="case_name" id="case_name"
                                    class="form-control material-input" dir="ltr"
                                    value="{{ $task->case_name }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="md-form-group">
                                <label for="duration">{{ trans('Duration') }}</label>
                                <div class="material-input-group">
                                    <input type="text" name="duration" id="duration"
                                        class="form-control material-input" dir="ltr"
                                        value="{{ $task->duration }}">
                                    <span>{{ trans('fields.Minutes') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane fade" id="tab-appearance">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="md-form-group">
                                <label for="color_value">{{ trans('Color') }}</label>
                                <div class="material-color-picker">
                                    <input type="text" name="color" id="color_value"
                                        class="form-control material-input" dir="ltr"
                                        value="{{ $task->color }}">
                                    <input type="color" id="color_picker" value="{{ $task->color }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="md-form-group">
                                <label for="background_value">{{ trans('Background') }}</label>
                                <div class="material-color-picker">
                                    <input type="text" name="background" id="background_value"
                                        class="form-control material-input" dir="ltr"
                                        value="{{ $task->background }}">
                                    <input type="color" id="background_picker" value="{{ $task->background }}">
                                </div>
                            </div>
                        </div>
                        @if ($isTimedCondition)
                            <div class="col-md-6">
                                <div class="md-form-group">
                                    <label for="timing_type">{{ trans('Timing Type') }}</label>
                                    <select name="timing_type" id="timing_type"
                                        class="form-control material-select">
                                        <option value="static"
                                            {{ $task->timing_type == 'static' ? 'selected' : '' }}>
                                            {{ trans('fields.static') }}
                                        </option>
                                        <option value="dynamic"
                                            {{ $task->timing_type == 'dynamic' ? 'selected' : '' }}>
                                            {{ trans('fields.dynamic') }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                        @endif
                        @if ($task->timing_type == 'static')
                            <div class="col-md-6">
                                <div class="md-form-group">
                                    <label for="timing_value">{{ trans('Timing Value') }}</label>
                                    <input type="text" name="timing_value" id="timing_value"
                                        class="form-control material-input" dir="ltr"
                                        value="{{ $task->timing_value }}">
                                </div>
                            </div>
                        @endif
                        @if ($task->timing_type == 'dynamic')
                            <div class="col-md-6">
                                <div class="md-form-group">
                                    <label for="timing_key_name">{{ trans('Timing Key') }}</label>
                                    <input type="text" name="timing_key_name" id="timing_key_name"
                                        class="form-control material-input" dir="ltr"
                                        value="{{ $task->timing_key_name }}">
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer text-start">
            <button type="submit" class="btn btn-primary">
                <span class="material-icons material-header-icon">save</span>
                {{ trans('Edit') }}
            </button>
        </div>
    </form>

    <div class="card shadow-sm material-subcard mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <span class="material-icons material-header-icon">group</span>
                {{ trans('Task Actors') }}
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0 material-table">
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
                                    <input type="text" name="task_name" value="{{ $task->name }}"
                                        class="form-control material-input">
                                </td>
                                <td></td>
                                <td>
                                    <input type="text" name="actor" list="actors" class="form-control material-input">
                                    <datalist id="actors">
                                        @foreach (App\Models\User::all() as $actor)
                                            <option value="{{ $actor->id }}">{{ $actor->name }}</option>
                                        @endforeach
                                    </datalist>
                                </td>
                                <td>
                                    <select name="role_id" class="form-control material-select">
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

    <div class="card shadow-sm material-subcard mt-4">
        @include('SimpleWorkflowView::Core.TaskJump.edit', ['task' => $task])
    </div>

    <form action="{{ route('simpleWorkflow.task.update', $task->id) }}" method="POST"
        class="card shadow-sm material-subcard mt-4">
        @csrf
        @method('PUT')
        <input type="hidden" name="task_id" value="{{ $task->id }}">
        <div class="card-header material-card-header-secondary">
            <h5 class="mb-0">
                <span class="material-icons material-header-icon">history</span>
                {{ trans('fields.Number of task to back') }}
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="md-form-group">
                        <label for="number_of_task_to_back">{{ trans('fields.Number of task to back') }}</label>
                        <input type="text" name="number_of_task_to_back" id="number_of_task_to_back"
                            class="form-control material-input" dir="ltr"
                            value="{{ $task->number_of_task_to_back }}">
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer text-start">
            <button type="submit" class="btn btn-primary">
                <span class="material-icons material-header-icon">done</span>
                {{ trans('Edit') }}
            </button>
        </div>
    </form>

    <form action="{{ route('simpleWorkflow.task.delete', $task->id) }}" method="POST"
        onsubmit="return confirm('{{ trans('Are you sure?') }}')"
        class="card shadow-sm material-subcard material-danger-card mt-4">
        @csrf
        @method('DELETE')
        <div class="card-header">
            <h5 class="mb-0">
                <span class="material-icons material-header-icon">delete_forever</span>
                {{ trans('Delete') }}
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="md-form-group">
                        <label for="transfer_task_id">{{ trans('fields.Transfer cases to task') }}</label>
                        <select name="transfer_task_id" id="transfer_task_id"
                            class="form-control material-select select2">
                            @foreach ($task->process->tasks()->where('id', '!=', $task->id) as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer text-start">
            <button type="submit" class="btn btn-danger">
                <span class="material-icons material-header-icon">warning</span>
                {{ trans('Delete') }}
            </button>
        </div>
    </form>
@endsection
@section('script')
    <script>
        initial_view();
        const syncMaterialColor = (pickerId, inputId) => {
            const picker = document.getElementById(pickerId);
            const input = document.getElementById(inputId);
            if (!picker || !input) {
                return;
            }
            const isValidHex = (value) => /^#([0-9a-f]{3}){1,2}$/i.test(value);
            picker.addEventListener('change', function () {
                input.value = this.value;
            });
            input.addEventListener('input', function () {
                if (isValidHex(this.value)) {
                    picker.value = this.value;
                }
            });
        };
        syncMaterialColor('color_picker', 'color_value');
        syncMaterialColor('background_picker', 'background_value');
        if (window.parent !== window) {
            const success = document.querySelector('.alert-success');
            if (success) {
                window.parent.postMessage('task-updated', '*');
            }
        }
    </script>
@endsection
