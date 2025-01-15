@extends('behin-layouts.app')


@section('title')

@endsection


@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">لیست پرونده های فرآیند {{ $process->name }}</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="draft-list">
                            <thead>
                                <tr>
                                    <th>ردیف</th>
                                    <th class="d-none">شناسه</th>
                                    <th>شماره پرونده</th>
                                    <th>ایجاد کننده</th>
                                    <th>نام</th>
                                    <th>آخرین وضعیت</th>
                                    <th>ایجاد شده در</th>
                                    <th>اقدام</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($process->cases as $case)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td class="d-none">{{ $case->id }}</td>
                                        <td>{{ $case->number }}</td>
                                        <td>{{ $case->creator()?->name }}</td>
                                        @php
                                            $s = $case->variables()->where('key', 'customer_fullname')->first()?->value;
                                            $s .= ' - ';
                                            $s .= $case->variables()->where('key', 'device_name')->first()?->value;
                                            $s .= ' - ';
                                            $s .= 'سریال ';
                                            $s .= $case->variables()->where('key', 'device_serial_no')->first()?->value;
                                        @endphp
                                        <td>{{ $s }}</td>
                                        @php
                                            $last_status = $case->variables()->where('key', 'last_status')->first()?->value;
                                        @endphp
                                        <td>{{ $last_status }}</td>
                                        <td>{{ $case->created_at }}</td>
                                        <td><a href="{{ route('simpleWorkflowReport.report.edit', [ 'report' => $case->id ]) }}"><i class="btn btn-primary fa fa-external-link"></i></a></td>
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
            "order": [[ 2, "desc" ]],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Persian.json"
            }
        });
    </script>   
@endsection
