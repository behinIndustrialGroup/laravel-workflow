@extends('behin-layouts.app')

@section('content')
    <h1>Create ViewModel</h1>
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
    <form action="{{ route('simpleWorkflow.view-model.store') }}" method="POST" class="table-responsive">
        @csrf
        <table class="table table-stripped table-warning">
            <tr>
                <td>{{ trans('fields.Name') }}</td>
                <td>
                    <input type="text" name="name" class="form-control" id="" dir="ltr">
                </td>
            </tr>
            <tr>
                <td>{{ trans('fields.API Key') }}</td>
                <td>
                    <input type="text" name="api_key" class="form-control" id="" dir="ltr">
                </td>
            </tr>
            <tr>
                <td>{{ trans('fields.Entity Name') }}</td>
                <td>
                    <select name="entity_id" id="">
                        @foreach ($entities as $entity)
                            <option value="{{ $entity->id }}">{{ $entity->name }}</option>
                        @endforeach
                    </select>
                </td>
            </tr>
            <tr>
                <td>{{ trans('fields.Max Number of Rows') }}</td>
                <td>
                    <input type="text" name="max_number_of_rows" class="form-control" id="" dir="ltr">
                </td>
            </tr>
            <tr>
                <td>{{ trans('fields.Default Fields') }}</td>
                <td>
                    <input type="text" name="default_fields" class="form-control" id="" dir="ltr"
                        placeholder="seperate with ,">
                </td>
            </tr>
            <tr>
                <td>{{ trans('fields.Show as') }}</td>
                <td>
                    <select name="show_as" id="">
                        <option value="table">table</option>
                        <option value="box">box</option>
                    </select>
                </td>
            </tr>
        </table>
        <table class="table table-primary">
            <tr>
                <td>{{ trans('fields.allow_create_row') }}</td>
                <td>
                    <select name="allow_create_row" id="">
                        <option value="1">{{ trans('fields.yes') }}</option>
                        <option value="0">{{ trans('fields.no') }}</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>{{ trans('fields.create_form') }}</td>
                <td>
                    <select name="create_form" id="" class="select2">
                        @foreach ($forms as $form)
                            <option value="{{ $form->id }}">{{ $form->name }}</option>
                        @endforeach
                    </select>
                </td>
            </tr>
            <tr>
                <td>{{ trans('fields.show_create_form_at_the_end') }}</td>
                <td>
                    <select name="show_create_form_at_the_end" id="">
                        <option value="1">{{ trans('fields.yes') }}</option>
                        <option value="0">{{ trans('fields.no') }}</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>{{ trans('fields.script_after_create') }}</td>
                <td>
                    <select name="script_after_create" id="" class="select2">
                        <option value=""></option>
                        @foreach ($scripts as $script)
                            <option value="{{ $script->id }}">{{ $script->name }}</option>
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
                        <option value="1">{{ trans('fields.yes') }}</option>
                        <option value="0">{{ trans('fields.no') }}</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>{{ trans('fields.update_form') }}</td>
                <td>
                    <select name="update_form" id="" class="select2">
                        @foreach ($forms as $form)
                            <option value="{{ $form->id }}">{{ $form->name }}</option>
                        @endforeach
                    </select>
                </td>
            </tr>
            <tr>
                <td>{{ trans('fields.which_rows_user_can_update') }}</td>
                <td>
                    <select name="which_rows_user_can_update" id="" multiple>
                        <option value="all">{{ trans('fields.All') }}</option>
                        <option value="user-created-it">{{ trans('fields.user created it') }}</option>
                        <option value="user-contributed-it">{{ trans('fields.user contributed it') }}</option>
                        <option value="user-updated-it">{{ trans('fields.user updated it') }}</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>{{ trans('fields.script_after_update') }}</td>
                <td>
                    <select name="script_after_update" id="" class="select2">
                        <option value=""></option>
                        @foreach ($scripts as $script)
                            <option value="{{ $script->id }}">{{ $script->name }}</option>
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
                        <option value="1">{{ trans('fields.yes') }}</option>
                        <option value="0">{{ trans('fields.no') }}</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>{{ trans('fields.which_rows_user_can_delete') }}</td>
                <td>
                    <select name="which_rows_user_can_delete" id="" multiple>
                        <option value="all">{{ trans('fields.All') }}</option>
                        <option value="user-created-it">{{ trans('fields.user created it') }}</option>
                        <option value="user-contributed-it">{{ trans('fields.user contributed it') }}</option>
                        <option value="user-updated-it">{{ trans('fields.user updated it') }}</option>
                    </select>
                </td>
            </tr>
        </table>
        <table class="table table-success">
            <tr>
                <td>{{ trans('fields.allow_read_row') }}</td>
                <td>
                    <select name="allow_read_row" id="">
                        <option value="1">{{ trans('fields.yes') }}</option>
                        <option value="0">{{ trans('fields.no') }}</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>{{ trans('fields.read_form') }}</td>
                <td>
                    <select name="read_form" id="" class="select2">
                        @foreach ($forms as $form)
                            <option value="{{ $form->id }}">{{ $form->name }}</option>
                        @endforeach
                    </select>
                </td>
            </tr>
            <tr>
                <td>{{ trans('fields.which_rows_user_can_read') }}</td>
                <td>
                    <select name="which_rows_user_can_read" id="" multiple>
                        <option value="all">{{ trans('fields.All') }}</option>
                        <option value="user-created-it">{{ trans('fields.user created it') }}</option>
                        <option value="user-contributed-it">{{ trans('fields.user contributed it') }}</option>
                        <option value="user-updated-it">{{ trans('fields.user updated it') }}</option>
                    </select>
                </td>
            </tr>
        </table>
        <button class="btn btn-primary">{{ trans('fields.Submit') }}</button>
    </form>
@endsection
