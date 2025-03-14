@extends('behin-layouts.app')


@section('title')
    خلاصه گزارش فرایند {{ $process->name }}
@endsection


@section('content')
    <div class="container">
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">لیست پرونده های فرآیند {{ $process->name }}</div>
                    
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="draft-list">
                                <thead>
                                    <tr>
                                        {{-- <th>ردیف</th> --}}
                                        <th class="d-none">شناسه</th>
                                        <th>شماره پرونده</th>
                                        <th>ایجاد کننده</th>
                                        <th>نام</th>
                                        <th>دستگاه</th>
                                        <th>کارشناس</th>
                                        <th>سریال مپا</th>
                                        <th>آخرین وضعیت</th>
                                        <th>ایجاد شده در</th>
                                        <th>اقدام</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($process->cases as $case)
                                        @php
                                            $name = $case->variables()->where('key', 'customer_fullname')->first()?->value;
                                            $name .= ' ';
                                            $name .= $case
                                                ->variables()
                                                ->where('key', 'customer_workshop_or_ceo_name')
                                                ->first()?->value;
                                            $device_name = $case->variables()->where('key', 'device_name')->first()?->value;

                                            $mapa_expert = $case->variables()->where('key', 'mapa_expert')->first()
                                                ?->value;
                                            $mapa_expert = getUserInfo($mapa_expert)?->name ?? '';
                                            $mapa_serial =
                                                $case->variables()->where('key', 'mapa_serial')->first()?->value ?? '';
                                        @endphp
                                        <tr>
                                            {{-- <td>{{ $loop->iteration }}</td> --}}
                                            <td class="d-none">{{ $case->id }}</td>
                                            <td>{{ $case->number }} <a href="{{ route('simpleWorkflowReport.summary-report.edit', [ 'summary_report' => $case->id ]) }}"><i class="fa fa-external-link"></i></a></td>
                                            <td>{{ $case->creator()?->name }}</td>

                                            <td>{{ $name }}</td>
                                            <td>{{ $device_name }}</td>
                                            <td>{{ $mapa_expert }}</td>
                                            <td>{{ $mapa_serial }}</td>
                                            @php
                                                $w = '';
                                                foreach ($case->whereIs() as $inbox) {
                                                    $w .= $inbox->task->name ?? '';
                                                    $w .= '(' . getUserInfo($inbox->actor)?->name . ')';
                                                    $w .= '<br>';
                                                }
                                            @endphp
                                            <td>{!! $w !!}</td>
                                            <td dir="ltr">{{ toJalali($case->created_at)->format('Y-m-d H:i') }}</td>
                                            <td><a href="{{ route('simpleWorkflowReport.summary-report.edit', [ 'summary_report' => $case->id ]) }}"><button class="btn btn-primary btn-sm">{{ trans('fields.Show More') }}</button></a></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        initial_view();
        $('#draft-list').DataTable({
            "order": [
                [1, "desc"]
            ],
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Persian.json"
            }
        });
    </script>
@endsection
