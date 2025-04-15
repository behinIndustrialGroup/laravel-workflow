@extends('behin-layouts.app')

@php
    // dd($rows)
@endphp

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
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>{{ trans('fields.Process Title') }}</th>
                        <th>{{ trans('fields.Case Number') }}</th>
                        <th>{{ trans('fields.Case Name') }}</th>
                        <th>{{ trans('fields.Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rows as $index => $row)
                        <tr>
                            <td>{{ $index + 1 }} 
                                @if (isset($row->case->id))
                                    <a href="{{ route('simpleWorkflow.inbox.cases.inboxes', $row->case->id) }}" target="_blank">
                                        <i class="fa fa-external-link">
                                        </i>
                                    </a>
                                @endif
                            </td>
                            <td>{{ $row->task->process->name }}</td>
                            <td>{{ $row->case->number ?? '' }}</td>
                            <td>{{ $row->case_name }}</td>
                            <td>
                                @if (isset($row->case->id))
                                    <a href="{{ route('simpleWorkflow.inbox.cases.inboxes', $row->case->id) }}"
                                        class="btn btn-sm btn-primary">{{ trans('fields.Show More') }}</a>
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
            },
            order: [
                [2, 'desc']
            ]
        });
    </script>
@endsection
