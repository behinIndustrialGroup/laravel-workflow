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
                <table class="table table-stripped" id="mapa-center-reports">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>تاریخ</th>
                            <th>ساعت شروع</th>
                            <th>ساعت پایان</th>
                            <th>مدت زمان صرف شده(ساعت)</th>
                            <th>تکنسین</th>
                            <th>گزارش</th>
                            <th>اقدام</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{ $totalDuration = 0 }}
                        @foreach ($reports as $report)
                            @php
                                $duration = round(((int) $report->end - (int) $report->start) / 3600, 2);
                                $totalDuration += $duration;
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td dir="ltr">{{ toJalali((int) $report->start)->format('Y-m-d') }}</td>
                                <td dir="ltr">{{ toJalali((int) $report->start)->format('H:i') }}</td>
                                <td dir="ltr">{{ toJalali((int) $report->end)->format('H:i') }}</td>
                                <td>{{ $duration }}</td>
                                <td>{{ getUserInfo($report->expert)?->name }}</td>
                                <td>{{ $report->report }}</td>
                                <td>
                                    <button class="btn btn-sm btn-danger" onclick="deleteReport('{{ $report->id }}')"><i class="fa fa-trash"></i></button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-success">
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>مجموع</td>
                            <td>{{ $totalDuration }}</td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tfoot>
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
            @if (auth()->user()->access('ثبت پایان کار مپا سنتر'))
                <div class="col-sm-12 text-center bg-info p-2">
                    پایان کار
                </div>
                <div class="col-sm-4 mt-2">
                    <button class="btn btn-info" onclick="sendForFixPrice()">ثبت پایان تعمیرات</button>
                </div>
                <script>
                    function sendForFixPrice() {
                        var scriptId = "7ac4388d-c783-4ac2-8f9b-0bb01bee5818";
                        var fd = new FormData();
                        fd.append('caseId', '{{ $case->id }}')
                        runScript(scriptId, fd, function(response) {
                            show_message('ارسال شد برای تعیین هزینه')
                            show_message('چند لحظه منتظر بمانید')

                            console.log(response)

                            // صبر کن 3 ثانیه بعد ریدایرکت کن
                            setTimeout(function() {
                                window.location.href = '{{ route('simpleWorkflowReport.mapa-center.index') }}';
                            }, 3000);
                        })
                    }
                </script>
            @endif
        </div>
    @endsection

    @section('script')
        <script>
            initial_view()
        </script>
        <script>
            $(document).ready(function() {
                $('#mapa-center-reports').DataTable({
                    'language': {
                        'url': 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/fa.json'
                    },
                    'order': [[1, 'desc']],
                });
            });

            function deleteReport(id) {
                var scriptId = "05a8fc79-b957-441b-8de4-275d7893c827";
                var fd = new FormData();
                fd.append('reportId', id)
                if(confirm("آیا از حذف این گزارش مطمئن هستید؟")){
                    runScript(scriptId, fd, function(response) {
                        alert(response)
                        window.location.reload();
                    })
                }
            }
        </script>
    @endsection
