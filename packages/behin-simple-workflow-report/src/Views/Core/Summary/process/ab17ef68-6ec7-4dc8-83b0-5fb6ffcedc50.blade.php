@extends('behin-layouts.app')

@section('title')
    مپا سنتر
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">مپا سنتر</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="draft-list">
                    <thead>
                        <tr>
                            {{-- <th>ردیف</th> --}}
                            <th class="d-none">شناسه</th>
                            <th>شماره پرونده</th>
                            <th>دستگاه</th>
                            <th>تاریخ ایجاد</th>
                            <th>اقدام</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($process->cases as $case)
                            <tr ondblclick="window.location.href='{{ route('simpleWorkflowReport.mapa-center.show', ['mapa_center' => $case->id]) }}'">
                                {{-- <td>{{ $loop->iteration }}</td> --}}
                                <td class="d-none">{{ $case->id }}</td>
                                <td>{{ $case->number }} 
                                    <a href="{{ route('simpleWorkflowReport.mapa-center.show', [ 'mapa_center' => $case->id ]) }}"><i class="fa fa-external-link"></i></a>
                                </td>
                                <td>{{ $case->getVariable('device_name') }}</td>
                                <td>{{ toJalali($case->created_at)->format('Y-m-d H:i') }}</td>
                                <td><a href="{{ route('simpleWorkflowReport.mapa-center.show', [ 'mapa_center' => $case->id ]) }}"><button class="btn btn-primary btn-sm">{{ trans('fields.Show More') }}</button></a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
