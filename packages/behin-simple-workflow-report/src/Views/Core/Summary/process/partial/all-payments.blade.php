@php
    use Behin\SimpleWorkflowReport\Controllers\Core\FinReportController;

    $rows = FinReportController::allPayments();
@endphp

<div class="card">
    <div class="card-header">
        <h3 class="card-title">گزارش مالی</h3>
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
                    @foreach ($rows as $row)
                        <tr>
                            <td>{{ $row->iteration }}</td>
                            <td>{{ $row->process }}</td>
                            <td>{{ $row->case_number }}</td>
                            <td>{{ $row->fix_cost_date }}</td>
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
                [0, "desc"]
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
