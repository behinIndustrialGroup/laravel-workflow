@extends('behin-layouts.app')

@section('content')
<div class="container card p-2">
    <a href="{{ route('simpleWorkflow.inbox.cases.inboxes', $inbox->case_id) }}"
        class="btn btn-sm btn-primary">{{ trans('fields.Back') }}</a>
</div>
    <div class="container table-responsive card p-2">
        <h2>{{ trans('fields.Edit Inbox') }}</h2>
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
        <div class="row card">
            <div class="col-md-12">
                <form action="{{ route('simpleWorkflow.inbox.update', $inbox->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="task_id">{{ trans('fields.Task') }}:</label>
                        {{ $inbox->task->name }}
                        <input type="text" name="task_id" id="task_id" class="form-control"
                            value="{{ $inbox->task_id }}" required>
                    </div>
                    <div class="form-group">
                        <label for="case_name">{{ trans('fields.Case Name') }}:</label>
                        <input type="text" name="case_name" id="case_name" class="form-control"
                            value="{{ $inbox->case_name }}" >
                    </div>
                    <div class="form-group">
                        <label for="case_id">{{ trans('fields.Case ID') }}:</label>
                        {{ $inbox->case->number }}
                        <input type="text" name="case_id" id="case_id" class="form-control"
                            value="{{ $inbox->case_id }}" required>
                    </div>
                    <div class="form-group">
                        <label for="status">{{ trans('fields.Status') }}:</label>
                        <select name="status" id="status" class="form-control" required>
                            <option value="new" {{ $inbox->status == 'new' ? 'selected' : '' }}>
                                {{ trans('fields.New') }}
                            </option>
                            <option value="done" {{ $inbox->status == 'done' ? 'selected' : '' }}>
                                {{ trans('fields.Done') }}
                            </option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="actor">{{ trans('fields.Actor') }}:</label>
                        {{ getUserInfo($inbox->actor)?->name }}
                        <input type="text" name="actor" id="actor" class="form-control" value="{{ ($inbox->actor) }}" list="users">
                        <datalist id="users">
                            @foreach (App\Models\User::all() as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </datalist>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        {{ trans('fields.Save') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $('#inbox-list').DataTable({
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Persian.json"
            }
        });
    </script>
@endsection
