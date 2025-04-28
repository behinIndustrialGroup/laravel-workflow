@extends('behin-layouts.app')

@section('title')
    تاریخچه
@endsection

@section('content')
    <div class="container">
        <div class="">
            @include('SimpleWorkflowView::Core.Partial.back-btn')
            <div class="card table-responsive">
                <div class="card-header bg-info">
                    تاریخچه انجام کار پرونده شماره {{ $rows[0]->case->number }}
                </div>
                <div class="card-body">
                    <table class="table table-stripped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ trans('fields.Process') }}</th>
                                <th>{{ trans('fields.Task') }}</th>
                                <th>{{ trans('fields. Case') }}</th>
                                <th>{{ trans('fields.Actor') }}</th>
                                <th>{{ trans('fields.Status') }}</th>
                                <th>{{ trans('fields.Created At') }}</th>
                                <th>{{ trans('fields.Done Date') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rows as $row)
                                <tr @if($row->status == 'new') class='bg-warning' @endif>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $row->task->process->name }}</td>
                                    <td>{{ $row->task->name }}</td>
                                    <td>{{ $row->case_name }}</td>
                                    <td>{{ getUserInfo($row->actor)?->name }}</td>
                                    <td>{{ trans('fields.' . $row->status) }}</td>
                                    <td dir="ltr">{{ toJalali($row->created_at)->format('Y-m-d H:i') }}</td>
                                    <td dir="ltr">{{ $row->updated_at != $row->created_at ? toJalali($row->updated_at)->format('Y-m-d H:i') : '' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
@endsection