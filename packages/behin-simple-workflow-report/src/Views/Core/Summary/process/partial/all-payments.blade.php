@extends('behin-layouts.app')


@section('title')
    گزارش مالی
@endsection

@php
    use Behin\SimpleWorkflowReport\Controllers\Core\FinReportController;
    use Illuminate\Support\Carbon;
    use Morilog\Jalali\Jalalian;
    use Behin\SimpleWorkflowReport\Helper\ReportHelper;

    $today = Carbon::today();
    $todayShamsi = Jalalian::fromCarbon($today);
    $thisYear = $todayShamsi->getYear();
    $thisMonth = $todayShamsi->getMonth();

    $year = isset($_GET['year']) ? $_GET['year'] : $thisYear;
    $month = isset($_GET['month']) ? $_GET['month'] : $thisMonth;
    $user = isset($_GET['user']) ? $_GET['user'] : null;
    // dd(json_encode($rows['destinations']));
@endphp

@section('content')
<div class="card">
    <div class="card-header">
        <a href="javascript:history.back()" class="btn btn-outline-primary float-left">
            <i class="fa fa-arrow-left"></i> {{ trans('fields.Back') }}
        </a>
    </div>
</div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">گزارش کل تعیین هزینه ها و دریافت هزینه ها</h3>
            <form action="{{ url()->current() }}" class="form-inline">
                <div class="form-group col-sm-3 ">
                    <label for="year" class="mr-2">سال</label>
                    <select name="year" id="year" class="form-control">
                        @for ($i = $thisYear; $i >= 1403; $i--)
                            <option value="{{ $i }}" {{ $i == $year ? 'selected' : '' }}>
                                {{ $i }}</option>
                        @endfor
                    </select>
                </div>

                <div class="form-group col-sm-3">
                    <label for="month" class="mr-2">ماه</label>
                    <select name="month" id="month" class="form-control">
                        <option value="">{{ trans('fields.All') }}</option>
                        @for ($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ $i == $month ? 'selected' : '' }}>
                                {{ $i }}</option>
                        @endfor
                    </select>
                </div>

                <div class="form-group col-sm-3">
                    <label for="user" class="mr-2">مقصد حساب</label>
                    <div class="col-sm-9">
                        <select name="user" id="user" class="select2">
                            <option value="">{{ trans('fields.All') }}
                            </option>
                            @foreach ($rows['destinations'] as $key => $destination)
                                <option value="{{ $key }}" {{ $key == $user ? 'selected' : '' }}>
                                    {{ $key }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <input type="submit" class="btn btn-primary col-sm-3" value="فیلتر">
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="total-cost" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ trans('fields.Process') }}</th>
                            <th>{{ trans('fields.Case Number') }}</th>
                            <th>{{ trans('fields.Fix Cost Date') }}</th>
                            <th>{{ trans('fields.Cost Amount') }}</th>
                            <th>{{ trans('fields.Payment Amount') }}</th>
                            <th>{{ trans('fields.Destination Account Name') }}</th>
                            <th>{{ trans('fields.Destination Account Number') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rows['rows'] as $row)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $row->process()?->name ?? trans('fields.Unknown') }}</td>
                                <td>{{ $row->case_number }}</td>
                                <td>{{ toJalali((int)$row->fix_cost_date)->format('Y-m-d') }}</td>
                                <td>{{ number_format($row->cost) }}</td>
                                <td>{{ number_format($row->payment) }}</td>
                                <td>{{ $row->destination_account_name }}</td>
                                <td>{{ $row->destination_account }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        initial_view();
        $(document).ready(function() {
            $('#total-cost').DataTable({
                "dom": 'Bfrtip',
                "buttons": [{
                    "extend": 'excelHtml5',
                    "text": "خروجی اکسل",
                    "title": "گزارش مجموع هزینه های دریافت شده به ازای کارشناس",
                    "className": "btn btn-success btn-sm",
                    "exportOptions": {
                        "columns": ':visible',
                        "footer": true
                    }
                }, ],

                "pageLength": -1,
                "order": [
                    [3, "desc"]
                ],
                "language": {
                    "url": "https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Persian.json"
                },
            });
            $('#mapa-expert').DataTable({
                "dom": 'Bfrtip',
                "buttons": [{
                    "extend": 'excelHtml5',
                    "text": "خروجی اکسل",
                    "title": "گزارش مجموع هزینه های دریافت شده به ازای کارشناس",
                    "className": "btn btn-success btn-sm",
                    "exportOptions": {
                        "columns": ':visible',
                        "footer": true
                    }
                }, ],
                "searching": false,
                "pageLength": -1,
                "order": [
                    [0, "asc"]
                ],
                "language": {
                    "url": "https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Persian.json"
                },
            });
        });
    </script>
@endsection
