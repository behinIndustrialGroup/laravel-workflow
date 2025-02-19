@extends('behin-layouts.app')

@php
    $forms = getProcessForms();
    $scripts = getProcessScripts();
    $conditions = getProcessConditions();
@endphp

@section('content')
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    <div class="mb-3">
        <a href="{{ route('simpleWorkflow.task.index', $task->process_id) }}" class="btn btn-secondary">
            {{ trans('Back to list') }}
        </a>
    </div>
    <form action="{{ route('simpleWorkflow.task.update', $task->id) }}" method="POST" class="card row col-sm-12">
        @csrf
        @method('PUT')
        <div class="panel-heading p-2 bg-light">
            <a data-toggle="collapse" href="#{{ $task->id }}">{{ $task->name }}</a>
            <span
                class="badge bg-{{ $task->type == 'form' ? 'primary' : ($task->type == 'script' ? 'success' : 'warning') }}">
                {{ ucfirst($task->type) }}
            </span>
            <div class="row mb-3">
                <label for="parent_id" class="col-sm-2 col-form-label">{{ trans('ID') }}</label>
                <input type="text" name="id" id="" class="col-sm-10 form-control" value="{{ $task->id }}" readonly>
            </div>
            <div class="row mb-3">
                <label for="parent_id" class="col-sm-2 col-form-label">{{ trans('Name') }}</label>
                <div class="col-sm-10">
                    <input type="text" name="name" id="" class="form-control" value="{{ $task->name }}">
                </div>
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
                        <a
                            href="{{ route('simpleWorkflow.conditions.edit', ['condition' => $task->executive_element_id]) }}">
                            {{ trans('Edit') }}
                        </a>
                    @endif
                </div>
            </div>
            <div class="row mb-3">
                <label for="parent_id" class="col-sm-2 col-form-label">{{ trans('Parent Task') }}</label>
                <div class="col-sm-10">
                    <select name="parent_id" id="parent_id" class="form-control select2">
                        <option value="">{{ trans('None') }}</option>
                        @foreach ($task->process->tasks() as $item)
                            <option value="{{ $item->id }}" {{ $item->id == $task->parent_id ? 'selected' : '' }}>
                                {{ $item->name }}</option>
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
                            <option value="{{ $item->id }}"
                                {{ $item->id == $task->next_element_id ? 'selected' : '' }}>{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row mb-3">
                <label for="assignment_type" class="col-sm-2 col-form-label">{{ trans('Assignment') }}</label>
                <div class="col-sm-10">
                    <select name="assignment_type" id="assignment_type" class="form-control">
                        <option value="">{{ trans('None') }}</option>
                        <option value="normal" {{ $task->assignment_type == 'normal' ? 'selected' : '' }}>
                            {{ trans('Normal') }}</option>
                        <option value="dynamic" {{ $task->assignment_type == 'dynamic' ? 'selected' : '' }}>
                            {{ trans('Dynamic') }}</option>
                    </select>
                </div>
            </div>
            <div class="row mb-3">
                <label for="case_name" class="col-sm-2 col-form-label">{{ trans('Case Name') }}</label>
                <div class="col-sm-10">
                    <input type="text" name="case_name" class="form-control" dir="ltr"
                        value="{{ $task->case_name }}">
                </div>
            </div>
            <button type="submit" class="btn btn-primary" style="float: left">{{ trans('Edit') }}</button>



        </div>
    </form>
    <div class="card row col-sm-12">
        <table class="table table-stripped">
            <thead>
                <tr>
                    <td>{{ trans('Row') }}</td>
                    <td>{{ trans('ID') }}</td>
                    <td>{{ trans('Task') }}</td>
                    <td>{{ trans('Task Assignment Type') }}</td>
                    <td>{{ trans('Actor') }}</td>
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
                        <td>{{ $value->created_at }}</td>
                        <td>
                            <form action="{{ route('simpleWorkflow.task-actors.destroy', $value->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button>{{ trans('Delete') }}</button>
                        </td>
                        </form>

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
                            <input type="text" name="task_id" id="" value="{{ $task->id }}" class="d-none">
                            <input type="text" name="task_name" id="" value="{{ $task->name }}"
                                class="form-control">
                        </td>
                        <td></td>
                        <td>
                            <input type="text" name="actor" id="" list="actors">


                            <datalist id="actors">
                                @foreach (App\Models\User::all() as $actor)
                                    <option value="{{ $actor->id }}">{{ $actor->name }}</option>
                                @endforeach
                            </datalist>
                        </td>
                        <td><button>{{ trans('Create') }}</button></td>
                    </tr>
                </tfoot>
            </form>
        </table>
    </div>
    <div class="row card col-sm-12">
        @include('SimpleWorkflowView::Core.TaskJump.edit', ['task' => $task])
    </div>
@endsection

@section('script')
    <script>
        initial_view();
    </script>
@endsection
