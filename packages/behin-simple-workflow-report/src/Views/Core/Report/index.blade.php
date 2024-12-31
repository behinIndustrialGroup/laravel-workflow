@extends('behin-layouts.app')

@section('title')
گزارش‌های گردش کار
@endsection


@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">گزارش‌های گردش کار</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>شناسه</th>
                                        <th>عنوان فرآیند</th>
                                        <th>توضیحات</th>
                                        <th>عملیات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($processes as $process)
                                        <tr>
                                            <td>{{ $process->id }}</td>
                                            <td>{{ $process->name }}</td>
                                            <td>{{ $process->description }}</td>
                                            <td>
                                                <a href="{{ route('simpleWorkflowReport.report.show', [ 'report' => $process ]) }}" class="btn btn-primary btn-sm">مشاهده گزارش</a>
                                            </td>
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
