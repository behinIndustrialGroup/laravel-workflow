@extends('behin-layouts.app')
@section('style')
    <style>
        table tr td:first-child {
            width: 300px;
            min-width: 300px;
            max-width: 300px;
            white-space: nowrap;
        }
    </style>
@endsection

@section('content')
    <h1>Edit ViewModel</h1>
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

    <div class="container">
        <div class="card">
            <form action="{{ route('simpleWorkflow.view-model.update', $view_model->id) }}" method="POST" class="table-responsive">
                @csrf
                @method('PUT')
                <table class="table table-stripped table-warning">
                    <tr>
                        <td>{{ trans('fields.Name') }}</td>
                        <td>
                            <input type="text" name="name" class="form-control" id="" dir="ltr"
                                value="{{ $view_model->name }}">
                        </td>
                    </tr>
                    <tr>
                        <td>{{ trans('fields.API Key') }}</td>
                        <td>
                            <input type="text" name="api_key" class="form-control" id="" dir="ltr"
                                value="{{ $view_model->api_key }}">
                        </td>
                    </tr>
                    <tr>
                        <td>{{ trans('fields.Entity Name') }}</td>
                        <td>
                            <select name="entity_id" id="">
                                @foreach ($entities as $entity)
                                    <option value="{{ $entity->id }}"
                                        {{ $entity->id == $view_model->entity_id ? 'selected' : '' }}>{{ $entity->name }}</option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>{{ trans('fields.Max Number of Rows') }}</td>
                        <td>
                            <input type="text" name="max_number_of_rows" class="form-control" id="" dir="ltr"
                                value="{{ $view_model->max_number_of_rows }}">
                        </td>
                    </tr>
                    <tr>
                        <td>{{ trans('fields.Default Fields') }}</td>
                        <td>
                            <input type="text" name="default_fields" class="form-control" id="" dir="ltr"
                                placeholder="seperate with ," value="{{ $view_model->default_fields }}">
                        </td>
                    </tr>
                    <tr>
                        <td>{{ trans('fields.Show as') }}</td>
                        <td>
                            <select name="show_as" id="">
                                <option value="table" {{ $view_model->show_as == 'table' ? 'selected' : '' }}>table</option>
                                <option value="box" {{ $view_model->show_as == 'box' ? 'selected' : '' }}>box</option>
                            </select>
                        </td>
                    </tr>
                </table>
        
                {{-- FOR CREATE --}}
                <table class="table table-primary">
                    <tr>
                        <td>{{ trans('fields.allow_create_row') }}</td>
                        <td>
                            <select name="allow_create_row" id="">
                                <option value="1" {{ $view_model->allow_create_row == 1 ? 'selected' : '' }}>
                                    {{ trans('fields.yes') }}</option>
                                <option value="0" {{ $view_model->allow_create_row == 0 ? 'selected' : '' }}>
                                    {{ trans('fields.no') }}</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>{{ trans('fields.create_form') }}</td>
                        <td>
                            <select name="create_form" id="" class="select2">
                                @foreach ($forms as $form)
                                    <option value="{{ $form->id }}"
                                        {{ $view_model->create_form == $form->id ? 'selected' : '' }}>{{ $form->name }}</option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>{{ trans('fields.show_create_form_at_the_end') }}</td>
                        <td>
                            <select name="show_create_form_at_the_end" id="">
                                <option value="1" {{ $view_model->show_create_form_at_the_end == 1 ? 'selected' : '' }}>
                                    {{ trans('fields.yes') }}</option>
                                <option value="0" {{ $view_model->show_create_form_at_the_end == 0 ? 'selected' : '' }}>
                                    {{ trans('fields.no') }}</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>{{ trans('fields.script_after_create') }}</td>
                        <td>
                            <select name="script_after_create" id="" class="select2">
                                <option value=""></option>
                                @foreach ($scripts as $script)
                                    <option value="{{ $script->id }}"
                                        {{ $view_model->script_after_create == $script->id ? 'selected' : '' }}>{{ $script->name }}</option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                </table>
                <table class="table table-info">
                    <tr>
                        <td>{{ trans('fields.allow_update_row') }}</td>
                        <td>
                            <select name="allow_update_row" id="">
                                <option value="1" {{ $view_model->allow_update_row == 1 ? 'selected' : '' }}>
                                    {{ trans('fields.yes') }}</option>
                                <option value="0" {{ $view_model->allow_update_row == 0 ? 'selected' : '' }}>
                                    {{ trans('fields.no') }}</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>{{ trans('fields.update_form') }}</td>
                        <td>
                            <select name="update_form" id="" class="select2">
                                @foreach ($forms as $form)
                                    <option value="{{ $form->id }}"
                                        {{ $view_model->update_form == $form->id ? 'selected' : '' }}>{{ $form->name }}</option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>{{ trans('fields.which_rows_user_can_update') }}</td>
                        <td>
                            @php
                                $view_model->which_rows_user_can_update = is_array($view_model->which_rows_user_can_update)
                                    ? $view_model->which_rows_user_can_update
                                    : json_decode($view_model->which_rows_user_can_update, true) ?? [];
                            @endphp
                            <select name="which_rows_user_can_update[]" id="" multiple class="select2">
                                <option value="all"
                                    {{ in_array('all', $view_model->which_rows_user_can_update) ? 'selected' : '' }}>
                                    {{ trans('fields.All') }}</option>
                                <option value="user-created-it"
                                    {{ in_array('user-created-it', $view_model->which_rows_user_can_update) ? 'selected' : '' }}>
                                    {{ trans('fields.user created it') }}</option>
                                <option value="user-contributed-it"
                                    {{ in_array('user-contributed-it', $view_model->which_rows_user_can_update) ? 'selected' : '' }}>
                                    {{ trans('fields.user contributed it') }}</option>
                                <option value="user-updated-it"
                                    {{ in_array('user-updated-it', $view_model->which_rows_user_can_update) ? 'selected' : '' }}>
                                    {{ trans('fields.user updated it') }}</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>{{ trans('fields.script_after_update') }}</td>
                        <td>
                            <select name="script_after_update" id="" class="select2">
                                <option value=""></option>
                                @foreach ($scripts as $script)
                                    <option value="{{ $script->id }}"
                                        {{ $view_model->script_after_update == $script->id ? 'selected' : '' }}>{{ $script->name }}</option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                </table>
                <table class="table table-danger">
                    <tr>
                        <td>{{ trans('fields.allow_delete_row') }}</td>
                        <td>
                            <select name="allow_delete_row" id="">
                                <option value="1" {{ $view_model->allow_delete_row == 1 ? 'selected' : '' }}>
                                    {{ trans('fields.yes') }}</option>
                                <option value="0" {{ $view_model->allow_delete_row == 0 ? 'selected' : '' }}>
                                    {{ trans('fields.no') }}</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>{{ trans('fields.which_rows_user_can_delete') }}</td>
                        <td>
                            @php
                                $view_model->which_rows_user_can_delete = is_array($view_model->which_rows_user_can_delete)
                                    ? $view_model->which_rows_user_can_delete
                                    : json_decode($view_model->which_rows_user_can_delete, true) ?? [];
                            @endphp
                            <select name="which_rows_user_can_delete[]" id="" multiple class="select2">
                                <option value="all"
                                    {{ in_array('all', $view_model->which_rows_user_can_delete) ? 'selected' : '' }}>
                                    {{ trans('fields.All') }}</option>
                                <option value="user-created-it"
                                    {{ in_array('user-created-it', $view_model->which_rows_user_can_delete) ? 'selected' : '' }}>
                                    {{ trans('fields.user created it') }}</option>
                                <option value="user-contributed-it"
                                    {{ in_array('user-contributed-it', $view_model->which_rows_user_can_delete) ? 'selected' : '' }}>
                                    {{ trans('fields.user contributed it') }}</option>
                                <option value="user-updated-it"
                                    {{ in_array('user-updated-it', $view_model->which_rows_user_can_delete) ? 'selected' : '' }}>
                                    {{ trans('fields.user updated it') }}</option>
                            </select>
                        </td>
                    </tr>
                </table>
                <table class="table table-success">
                    <tr>
                        <td>{{ trans('fields.allow_read_row') }}</td>
                        <td>
                            <select name="allow_read_row" id="">
                                <option value="1" {{ $view_model->allow_read_row == 1 ? 'selected' : '' }}>
                                    {{ trans('fields.yes') }}</option>
                                <option value="0" {{ $view_model->allow_read_row == 0 ? 'selected' : '' }}>
                                    {{ trans('fields.no') }}</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>{{ trans('fields.show_rows_based_on') }}</td>
                        <td>
                            <select name="show_rows_based_on" id="">
                                <option value=""></option>
                                <option value="case_id" {{ $view_model->show_rows_based_on == 'case_id' ? 'selected' : '' }}>
                                    {{ trans('Case ID') }}</option>
                                <option value="case_number" {{ $view_model->show_rows_based_on == 'case_number' ? 'selected' : '' }}>
                                    {{ trans('Case Number') }}</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>{{ trans('fields.read_form') }}</td>
                        <td>
                            <select name="read_form" id="" class="select2">
                                @foreach ($forms as $form)
                                    <option value="{{ $form->id }}"
                                        {{ $view_model->read_form == $form->id ? 'selected' : '' }}>{{ $form->name }}</option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>{{ trans('fields.which_rows_user_can_read') }}</td>
                        <td>
                            @php
                                $view_model->which_rows_user_can_read = is_array($view_model->which_rows_user_can_read)
                                    ? $view_model->which_rows_user_can_read
                                    : json_decode($view_model->which_rows_user_can_read, true) ?? [];
                            @endphp
                            <select name="which_rows_user_can_read[]" id="" multiple class="select2">
                                <option value="all"
                                    {{ in_array('all', $view_model->which_rows_user_can_read) ? 'selected' : '' }}>
                                    {{ trans('fields.All') }}</option>
                                <option value="user-created-it"
                                    {{ in_array('user-created-it', $view_model->which_rows_user_can_read) ? 'selected' : '' }}>
                                    {{ trans('fields.user created it') }}</option>
                                <option value="user-contributed-it"
                                    {{ in_array('user-contributed-it', $view_model->which_rows_user_can_read) ? 'selected' : '' }}>
                                    {{ trans('fields.user contributed it') }}</option>
                                <option value="user-updated-it"
                                    {{ in_array('user-updated-it', $view_model->which_rows_user_can_read) ? 'selected' : '' }}>
                                    {{ trans('fields.user updated it') }}</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>{{ trans('fields.script_before_show_rows') }}</td>
                        <td>
                            <select name="script_before_show_rows" id="" class="select2">
                                <option value=""></option>
                                @foreach ($scripts as $script)
                                    <option value="{{ $script->id }}"
                                        {{ $view_model->script_before_show_rows == $script->id ? 'selected' : '' }}>{{ $script->name }}</option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                </table>
                <button class="btn btn-primary">{{ trans('fields.Submit') }}</button>
            </form>
        
            <div class="card-body" style="direction: ltr; text-align: left">
                <h6>{{ trans('fields.Open update form') }}</h6>
                <code>
                    open_view_model_form('{{ $view_model->update_form }}', '{{ $view_model->id }}', row_id, '{{ $view_model->api_key }}')
                </code>
                <h6>{{ trans('fields.Open create new form') }}</h6>
                <code>
                    open_view_model_create_new_form('{{ $view_model->create_form }}', '{{ $view_model->id }}', '{{ $view_model->api_key }}')
                </code>
                <h6>{{ trans('fields.Delete row') }}</h6>
                <code>
                    delete_view_model_row('{{ $view_model->id }}', row_id, '{{ $view_model->api_key }}')
                </code>
            </div>
        </div>
    </div>
    
@endsection

@section('script')
    <script>
        initial_view()
    </script>
@endsection
