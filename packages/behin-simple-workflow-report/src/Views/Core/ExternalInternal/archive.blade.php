@extends('behin-layouts.app')

@section('title')
@endsection

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header bg-success">گزارش پرونده های بایگانی شده</div>
            <div class="card-body table-responsive">
                <table class="table" id="cases">
                    <thead>
                        <tr>
                            <th>{{ trans('fields.Case Number') }}</th>
                            <th>{{ trans('fields.Customer') }}</th>
                            <th>{{ trans('fields.Creator') }}</th>
                            <th>{{ trans('fields.Last Status') }}</th>
                            <th>{{ trans('fields.Created At') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($cases as $case)
                            <tr>
                                <td>
                                    <a
                                        href="{{ route('simpleWorkflowReport.external-internal.show', ['external_internal' => $case->number]) }}"><i
                                            class="fa fa-external-link"></i></a>
                                    {{ $case->number }}
                                    
                                    {!! $case->history !!}
                                </td>
                                <td>{{ $case->getVariable('customer_workshop_or_ceo_name') }}</td>
                                <td>{{ getUserInfo($case->creator)->name }}</td>
                                <td>
                                    @foreach ($case->whereIs() as $inbox)
                                        {!! $inbox->task->styled_name !!} 
                                    @endforeach
                                </td>
                                <td dir="ltr">{{ toJalali($case->created_at)->format('Y-m-d H:i') }}</td>
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
        $('#cases').DataTable({
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
            "order": [
                [0, "desc"]
            ],
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Persian.json"
            },
        });
    </script>
@endsection
