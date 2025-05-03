@extends('behin-layouts.app')


@section('title')
    خلاصه گزارش فرایند فرایند حذف پرونده
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
                @include('SimpleWorkflowView::Core.Partial.back-btn')
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
                                        <th>شماره پرونده حذف شده</th>
                                        <th>توضیحات</th>
                                        <th>تاریخ ایجاد</th>
                                        <th>اقدام</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($process->cases as $case)
                                        <tr ondblclick="window.location.href='{{ route('simpleWorkflowReport.summary-report.edit', ['summary_report' => $case->id]) }}'">
                                            {{-- <td>{{ $loop->iteration }}</td> --}}
                                            <td class="d-none">{{ $case->id }}</td>
                                            <td>{{ $case->number }} 
                                                <a href="{{ route('simpleWorkflowReport.summary-report.edit', [ 'summary_report' => $case->id ]) }}"><i class="fa fa-external-link"></i></a>
                                                {!! $case->history !!}
                                            </td>
                                            <td>{{ getUserInfo($case->creator)->name ?? '' }}</td>
                                            <td>{{ $case->getVariable('case_number') }}</td>
                                            <td>{{ $case->getVariable('remove_case_description') }}</td>
                                            <td>{{ toJalali($case->created_at)->format('Y-m-d H:i') }}</td>
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
