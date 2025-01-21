@extends('behin-layouts.app')

@section('content')
    <div class="container table-responsive card p-2">
        <h2>{{ trans('fields.User Inbox') }}</h2>
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        @if ($rows->isEmpty())
            {{-- <div class="alert alert-info">
            {{ trans('You have no items in your inbox.') }}
        </div> --}}
        @else
            <table class="table table-striped" id="inbox-list">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ trans('fields.Process Title') }}</th>
                        <th>{{ trans('fields.Task Title') }}</th>
                        <th>{{ trans('fields.Case Number') }}</th>
                        <th>{{ trans('fields.Case Title') }}</th>
                        <th>{{ trans('fields.Actor') }}</th>
                        <th>{{ trans('fields.Status') }}</th>
                        <th>{{ trans('fields.Created At') }}</th>
                        <th>{{ trans('fields.Updated At') }}</th>
                        <th>{{ trans('fields.Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rows as $index => $row)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $row->task->process->name }}</td>
                            <td>{{ $row->task->name }}</td>
                            <td>{{ $row->case->number }}</td>
                            <td>{{ $row->case_name }}</td>
                            <td>{{ getUserInfo($row->actor)?->name }}</td>
                            <td>
                                <span class="badge bg-{{$row->status}}">{{ trans('fields.' . $row->status) }}</span>
                            </td>
                            <td dir="ltr">{{ toJalali($row->created_at)->format('Y-m-d H:i') }}</td>
                            <td dir="ltr">{{ $row->created_at == $row->updated_at ? '' : toJalali($row->updated_at)->format('Y-m-d H:i') }}</td>
                            <td>
                                <a href="{{ route('simpleWorkflow.inbox.edit', $row->id) }}"
                                    class="btn btn-sm btn-primary">{{ trans('fields.Edit') }}</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

    </div>
@endsection

@section('script')
    <script>
        $('#inbox-list').DataTable({
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Persian.json"
            },
            order: [
                [7, 'desc']
            ]
        });
    </script>
@endsection
