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
    $thisMonth = str_pad($thisMonth, 2, '0', STR_PAD_LEFT);
    $to = Jalalian::fromFormat('Y-m-d', "$thisYear-$thisMonth-01")
        ->addMonths(1)
        ->subDays(1)
        ->format('Y-m-d');

    $from = isset($_GET['from']) ? $_GET['from'] : "$thisYear-$thisMonth-01";
    $to = isset($_GET['to']) ? $_GET['to'] : (string) $to;
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
        <div class="card-header  text-center bg-info">
            جستجو
        </div>
        <div class="card-body">
            <form action="{{ url()->current() }}" class="form-row align-items-end">

                <div class="form-group col-md-2">
                    <label for="year">از</label>
                    <input type="text" name="from" value="{{ $from }}" class="form-control persian-date">
                </div>
                <div class="form-group col-md-2">
                    <label for="year">تا</label>
                    <input type="text" name="to" value="{{ $to }}" class="form-control persian-date">
                </div>

                <div class="form-group col-md-3">
                    <label for="user">مقصد حساب</label>
                    <select name="user" id="user" class="form-control select2">
                        <option value="">{{ trans('fields.All') }}</option>
                        @foreach ($rows['destinations'] as $key => $destination)
                            <option value="{{ $key }}" {{ $key == $user ? 'selected' : '' }}>{{ $key }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col-md-3">
                    <button type="submit" class="btn btn-primary btn-block">فیلتر</button>
                </div>

            </form>
        </div>
    </div>
    <div class="card">
        <div class="card-header  text-center bg-info">
            <h3 class="card-title">گزارش کل دریافت هزینه ها</h3>
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
                            <th class="d-none">{{ trans('fields.Cost Amount') }}</th>
                            <th>{{ trans('fields.Payment Date') }}</th>
                            <th>{{ trans('fields.Payment Amount') }}</th>
                            <th>{{ trans('fields.Destination Account Name') }}</th>
                            <th>{{ trans('fields.Destination Account Number') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $numberOfInternalProcess = 0;
                            $numberOfExternalProcess = 0;
                            $totalPayment = 0;
                        @endphp
                        @foreach ($rows['rows'] as $row)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $row->process_name }}
                                    @php
                                        if ($row->process()?->name == 'داخلی') {
                                            $numberOfInternalProcess++;
                                        }
                                        if ($row->process()?->name == 'خارجی') {
                                            $numberOfExternalProcess++;
                                        }
                                    @endphp
                                </td>
                                <td>{{ $row->case_number }}</td>
                                <td>{{ $row->fix_cost_date ? toJalali((int) $row->fix_cost_date)->format('Y-m-d') : '' }}
                                </td>
                                <td class="d-none">{{ number_format($row->cost) }}</td>
                                <td>{{ $row->payment_date ? toJalali((int) $row->payment_date)->format('Y-m-d') : '' }}</td>
                                <td>{{ number_format($row->payment) }}
                                    @php
                                        $totalPayment += $row->payment;
                                    @endphp
                                </td>
                                <td>{{ $row->destination_account_name }}</td>
                                <td>{{ $row->destination_account }}</td>
                            </tr>
                            @if($row->cost2){
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $row->process_name }}
                                        @php
                                            if ($row->process()?->name == 'داخلی') {
                                                $numberOfInternalProcess++;
                                            }
                                            if ($row->process()?->name == 'خارجی') {
                                                $numberOfExternalProcess++;
                                            }
                                        @endphp
                                    </td>
                                    <td>{{ $row->case_number }}</td>
                                    <td>{{ $row->fix_cost_date ? toJalali((int) $row->fix_cost_date)->format('Y-m-d') : '' }}
                                    </td>
                                    <td class="d-none">{{ number_format($row->cost2) }}</td>
                                    <td>{{ $row->payment_date ? toJalali((int) $row->payment_date)->format('Y-m-d') : '' }}</td>
                                    <td>{{ number_format($row->payment2) }}
                                        @php
                                            $totalPayment += $row->payment2;
                                        @endphp
                                    </td>
                                    <td>{{ $row->destination_account_name_2 }}</td>
                                    <td>{{ $row->destination_account_2 }}</td>
                                </tr>
                            }
                        @endforeach
                        <tfoot>
                            <tr class="bg-success">
                                <td></td>
                                <td>
                                    داخلی: {{ $numberOfInternalProcess }}<br>
                                    خارجی: {{ $numberOfExternalProcess }}
                                </td>
                                <td></td>
                                <td></td>
                                <td class="d-none"></td>
                                <td >مجموع</td>
                                <td>{{ number_format($totalPayment) }}</td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tfoot>
                        
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
                    [5, "desc"]
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
