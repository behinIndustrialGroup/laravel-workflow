@extends('behin-layouts.app')

@section('title')
    {{ trans('fields.Expired Tasks') }}
@endsection

@php
    $categprizedTask = [
        'مالی اعلام هزینه' => [
            '19a1be98-7b4a-4100-903d-e6612c4c4a6c',
            '19f15a6f-1ec8-488c-adea-6c8419fe850a',
            '062b5000-07c2-435c-bb45-621ed15cb42c',
        ],
        'مالی دریافت هزینه' => [
            'adee777f-da9d-4d54-bf00-020a27e0f861',
            'c008cd7d-ea9c-4b0b-917b-97e8ff651155',
            '9cfbbbf7-e53f-4706-b7c9-c69c0dd84cc4',
            '1c63c629-b27b-4fe6-a993-a7a149926c55',
        ],
        '⁠پذیرش خارجی ( خانم یگانه و مردانلو ) پذیرشها و درخواستهای مجدد' => [
            'b9ab688a-7819-4d83-a53a-a396aa540232',
            '8bee90b3-6bc0-4537-86d0-715583566064',
            '0fcd1c57-183e-4c4a-8e5a-b218f972a57d',
            '5d26a68f-d772-442f-b89a-5628755aa3f7',
            '36f0f696-5694-4731-a179-a70e9a686ef5',
            'af1b47b1-167f-4371-b028-ac1fe94ee532',
        ],
        'فنی ( تاییدیه کارها و تقسیم کارها )' => [
            '9772ccb9-7f8d-4cc0-a80c-bcd340e49e34',
            '7b96d0c0-e2aa-43d2-bcda-67bcfb4b8c87',
            '66c66e46-35dd-491f-a89f-82f18b808b41',
            'ffa2f261-f3b0-4c7f-b705-1ff46705a118',
        ],
        'فنی  ( کارشناسان داخلی و خارجی)' => [
            'd213de29-832b-4de5-8ef1-03295835e5ae',
            'f9cc1cf5-e3b0-4a46-91a5-2e7e59d29784',
        ],
        '⁠پذیرش داخلی و تحویل داخلی ( خانم طالب زاده )' => [
            '9f6b7b5c-155e-4698-8b05-26ebb061bb7d',
            '039a097e-2159-49df-b866-f7766aaf2cfc',
        ],
    ];
@endphp

@section('content')
    <div class="container">
        @foreach ($categprizedTask as $category => $tasks)
            <div class="card table-responsive">
                <div class="card-header bg-info text-center">{{ $category }}</div>
                <div class="card-body">
                    @php
                        $filteredTasks = $expiredTasks->whereIn('task_id', $tasks);
                    @endphp
                    @if (count($filteredTasks) == 0)
                        <div class="alert alert-light">
                            {{ trans('fields.No expired tasks found') }}
                        </div>
                    @else
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>{{ trans('fields.Task Name') }}</th>
                                    <th>{{ trans('fields.Case Name') }}</th>
                                    <th>{{ trans('fields.Case Number') }}</th>
                                    <th>{{ trans('fields.Actor') }}</th>
                                    <th>{{ trans('fields.Duration') }}</th>
                                    <th>{{ trans('fields.Created At') }}</th>
                                    <th>{{ trans('fields.Deadline') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($filteredTasks as $task)
                                    <tr>
                                        <td>{{ $task->task->name }}</td>
                                        <td>{{ $task->case_name }}</td>
                                        <td>{{ $task->case->number }}
                                            <a
                                                href="{{ route('simpleWorkflowReport.external-internal.show', ['external_internal' => $task->case->number]) }}"><i
                                                    class="fa fa-external-link"></i></a>
                                        </td>
                                        <td>{{ getUserInfo($task->actor)->name }}</td>
                                        <td>{{ $task->task->duration }}</td>
                                        <td dir="ltr">{{ toJalali($task->created_at) }}</td>
                                        <td>{!! $task->time_status !!}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@endsection
