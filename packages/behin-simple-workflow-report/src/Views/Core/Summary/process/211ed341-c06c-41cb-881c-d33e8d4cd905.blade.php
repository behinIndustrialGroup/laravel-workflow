@extends('behin-layouts.app')

@section('title')
    خلاصه گزارش فرایند {{ $process->name }}
@endsection

@php
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Carbon;
    use Morilog\Jalali\Jalalian;
    use Behin\SimpleWorkflow\Models\Entities\Timeoffs;
    use Behin\SimpleWorkflowReport\Controllers\Core\TimeoffController;

    $todayShamsi = Jalalian::now();
    $thisYear = $todayShamsi->getYear();
    $thisMonth = str_pad($todayShamsi->getMonth(), 2, '0', STR_PAD_LEFT);
    $userId = isset($_GET['userId']) ? $_GET['userId'] : null;
    $users = TimeoffController::totalLeaves($userId);

    $items = TimeoffController::items($userId);

@endphp


@section('content')
    <div class="container">
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        <div class="row justify-content-center">

            <div class="col-md-12">
                @include('SimpleWorkflowView::Core.Partial.back-btn')

                @if (auth()->user()->access('خلاصه گزارش فرایند: مرخصی > گزارش ماهانه مرخصی کاربران'))
                    <div class="card">

                        <div class="card-header text-center bg-success">گزارش ماهانه مرخصی کاربران
                            <a href="{{ route('simpleWorkflowReport.totalTimeoff') }}">
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
                                            <th>مانده مرخصی</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($users as $user)
                                            <tr>
                                                <td>{{ $user->number }}</td>
                                                <td>{{ $user->name }}</td>
                                                <td>{{ $thisYear }}</td>
                                                <td class="d-none">{{ $thisMonth }}</td>
                                                <td>
                                                    @if (auth()->user()->access('تغییر مانده مرخصی ها'))
                                                        <form
                                                            method="POST"
                                                            action="{{ route('simpleWorkflowReport.timeoff.update', ['processId' => $process->id]) }}">
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
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                @include('SimpleWorkflowReportView::Core.Summary.process.partial.now-and-later-leaves', [
                    'items' => $items,
                    'process' => $process,
                    'userId' => $userId
                ])
            </div>
        </div>
    </div>
@endsection
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.1/xlsx.full.min.js"></script>

@section('script')
    <script>
        initial_view();
        $('#timeoff-report').DataTable({
            "pageLength": 50,
            "order": [
                [0, "asc"]
            ],
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Persian.json"
            }
        });
        $('#hourly-leaves').DataTable({
            "order": [
                [4, "desc"],
                [5, "desc"]
            ],
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Persian.json"
            }
        });
        $('#daily-leaves').DataTable({
            "order": [
                [4, "desc"],
                [5, "desc"]
            ],
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Persian.json"
            }
        });
    </script>
@endsection
