@extends('behin-layouts.app')

@section('title')
    کارتابل
@endsection
@section('content')
    <div class="container table-responsive card p-2">
        <h2>{{ trans('fields.User Inbox') }}</h2>
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        @if ($rows->isEmpty())
            <div class="alert alert-info">
                {{ trans('fields.You have no items in your inbox') }}
            </div>
        @else
            <table class="table table-striped" id="inbox-list">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ trans('fields.Process Title') }}</th>
                        <th>{{ trans('fields.Task Title') }}</th>
                        <th>{{ trans('fields.Case Number') }}</th>
                        <th>{{ trans('fields.Case Title') }}</th>
                        <th>{{ trans('fields.Status') }}</th>
                        <th>{{ trans('fields.Received At') }}</th>
                        <th>{{ trans('fields.Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rows as $index => $row)
                        <tr ondblclick="window.location.href = '{{ route('simpleWorkflow.inbox.view', $row->id) }}'">
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $row->task->process->name }}</td>
                            <td>{{ $row->task->name }}</td>
                            <td>{{ $row->case->number ?? '' }}</td>
                            <td>{{ $row->case_name }}</td>
                            <td>
                                @if ($row->status == 'new')
                                    <span class="badge bg-primary">{{ trans('fields.New') }}</span>
                                @elseif($row->status == 'in_progress')
                                    <span class="badge bg-warning">{{ trans('fields.In Progress') }}</span>
                                @elseif($row->status == 'draft')
                                    <span class="badge bg-info">{{ trans('fields.Draft') }}</span>
                                @else
                                    <span class="badge bg-success">{{ trans('fields.Completed') }}</span>
                                @endif
                            </td>
                            <td dir="ltr">{{ toJalali($row->created_at)->format('Y-m-d H:i') }}</td>
                            <td>
                                <a href="{{ route('simpleWorkflow.inbox.view', $row->id) }}"
                                    class="btn btn-sm btn-primary">{{ trans('fields.View') }}<i
                                        class="fa fa-external-link"></i></a>
                                @if ($row->status == 'draft')
                                    <a href="{{ route('simpleWorkflow.inbox.delete', $row->id) }}"
                                        class="btn btn-sm btn-danger">{{ trans('fields.Delete') }}
                                        <i class="fa fa-trash"></i></a>
                                @endif
                                @if ($row->task->id == '7f62e4ce-a96e-419a-8972-358fd642f39b')
                                    <a href="{{ route('simpleWorkflow.inbox.delete', $row->id) }}"
                                        class="btn btn-sm btn-danger">{{ trans('fields.Delete') }}
                                        <i class="fa fa-trash"></i></a>
                                @endif
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
            }
        });
    </script>
@endsection
