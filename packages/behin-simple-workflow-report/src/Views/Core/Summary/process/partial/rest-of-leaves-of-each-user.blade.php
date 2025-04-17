<div class="card-header text-center bg-success">گزارش ماهانه مرخصی کاربران
    <a href="{{ route('simpleWorkflowReport.process.export', $process->id) }}">
        <button class="btn btn-primary btn-sm">{{ trans('fields.Excel') }}</button>
    </a>
</div>
<div class="card-body">
    <div class="table-responsive">
        <table class="table table-striped" id="timeoff-report">
            <thead>
                <tr>
                    <th>شماره پرسنلی</th>
                    <th>نام کاربر</th>
                    <th>سال</th>
                    <th>ماه</th>
                    <th>مانده مرخصی</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    @if (!in_array($user->id, [43]))
                        <tr>
                            <td>{{ $user->number }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $thisYear }}</td>
                            <td>{{ $thisMonth }}</td>
                            <td>
                                @if (auth()->user()->access('تغییر مانده مرخصی ها'))
                                    <form
                                        action="{{ route('simpleWorkflowReport.process.update', ['processId' => $process->id]) }}"
                                        method="POST" id="leave-form">
                                        @csrf
                                        <input type="hidden" name="userId" id=""
                                            value="{{ $user->id }}">
                                        <input type="hidden" name="restBySystem" id=""
                                            class="form-control"
                                            value="{{ round($user->restLeaves, 2) }}">
                                        <input type="text" name="restByUser" id=""
                                            value="{{ round($user->restLeaves, 2) }}">
                                        <input type="submit" value="ثبت" name=""
                                            class="btn btn-primary btn-sm">
                                    </form>
                                @else
                                    {{ round($user->restLeaves, 2) }}
                                @endif
                            </td>
                            <td>
                                <a
                                    href="?userId={{ $user->id }}&year={{ $thisYear }}&month={{ $thisMonth }}">
                                    <button
                                        class="btn btn-primary btn-sm">{{ trans('fields.Show More') }}</button>
                                </a>
                            </td>
                        </tr>
                    @endif
                @endforeach
                {{-- @foreach ($monthlyLeaves as $leave)
                    @if (!in_array($leave->user_id, [1, 43]))
                        <tr>
                            <td>{{ getUserInfo($leave->user_id)?->number }}</td>
                            <td>{{ getUserInfo($leave->user_id)?->name }}</td>
                            <td>{{ $leave->start_year }}</td>
                            <td>{{ $leave->start_month }}</td>
                            <td dir="ltr">
                                @if (auth()->user()->access('تغییر مانده مرخصی ها'))
                                    <form
                                        action="{{ route('simpleWorkflowReport.process.update', ['processId' => $process->id]) }}"
                                        method="POST" id="leave-form">
                                        @csrf
                                        <input type="hidden" name="userId" id=""
                                            value="{{ $leave->user_id }}">
                                        <input type="hidden" name="restBySystem" id=""
                                            class="form-control"
                                            value="{{ round($totalLeaves - $leave->total_leaves, 2) }}">
                                        <input type="text" name="restByUser" id=""
                                            value="{{ round($totalLeaves - $leave->total_leaves, 2) }}">

                                        <input type="submit" value="ثبت" name=""
                                            id="">
                                    </form>
                                @else
                                    {{ round($totalLeaves - $leave->total_leaves, 2) }}
                                @endif
                            </td>
                            <td>
                                <a
                                    href="?userId={{ $leave->user_id }}&year={{ $thisYear }}&month={{ $thisMonth }}">
                                    <button
                                        class="btn btn-primary btn-sm">{{ trans('fields.Show More') }}</button>
                                </a>
                            </td>
                        </tr>
                    @endif
                @endforeach --}}
            </tbody>
        </table>
    </div>
</div>