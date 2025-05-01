@extends('behin-layouts.app')

@section('title')
    مپا سنتر
@endsection

@php
    $customerForm = getFormInformation('d6a98160-91aa-4f17-9bb3-f9284b2882b2');
    $deviceForm = getFormInformation('670fb05c-a794-4677-be5d-80b6c9b13da9');
    $fixForm = getFormInformation('14a68757-f609-44e1-82e9-4dc5ac35d60e');
    $variables = $case->variables();
@endphp

@section('content')
    <div class="card">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        <div class="card-header">
            <h3 class="card-title">مپا سنتر</h3>
        </div>
        <div class="card-body">
            @include('SimpleWorkflowView::Core.Form.preview', [
                'form' => $customerForm,
                'case' => $case,
                'variables' => $variables,
            ])
            @include('SimpleWorkflowView::Core.Form.preview', [
                'form' => $deviceForm,
                'case' => $case,
                'variables' => $variables,
            ])
            <div class="col-sm-12 text-center bg-info p-2">
                گزارشات تعمیر
            </div>
            <div class="col-sm-12  table-responsive">
                <table class="table table-stripped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>شروع</th>
                            <th>پایان</th>
                            <th>واحد</th>
                            <th>تکنسین</th>
                            <th>گزارش</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reports as $report)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td dir="ltr">{{ toJalali((int) $report->start)->format('Y-m-d H:i') }}</td>
                                <td dir="ltr">{{ toJalali((int) $report->end)->format('Y-m-d H:i') }}</td>
                                <td>{{ $report->unit }}</td>
                                <td>{{ getUserInfo($report->expert)?->name }}</td>
                                <td>{{ $report->report }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <form action="{{ route('simpleWorkflowReport.mapa-center.update', $case->id) }}" method="POST">
                @csrf
                @method('PUT')
                @include('SimpleWorkflowView::Core.Form.preview', [
                    'form' => $fixForm,
                    'case' => $case,
                ])
                <input type="submit" value="ذخیره" class="btn btn-primary">
            </form>
        </div>
    @endsection

    @section('script')
        <script>
            initial_view()
        </script>
    @endsection
