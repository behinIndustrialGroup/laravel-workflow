@extends('behin-layouts.app')

@section('title', 'دفتر تلفن')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header bg-success">دفتر تلفن</div>
            <div class="card-body table-responsive">
                <table class="table" id="phonebook">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ trans('fields.customer_name') }}</th>
                            <th>{{ trans('fields.customer_mobile') }}</th>
                            <th>{{ trans('fields.customer_nid') }}</th>
                            <th>{{ trans('fields.customer_address') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($customers as $customer)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $customer->name }}</td>
                                <td>{{ $customer->mobile }}</td>
                                <td>{{ $customer->national_id }}</td>
                                <td>{{ $customer->address }}</td>
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
        $('#phonebook').DataTable({
            "dom": 'Bfrtip',
            "buttons": [{
                "extend": 'excelHtml5',
                "text": "خروجی اکسل",
                "title": "دفتر تلفن",
                "className": "btn btn-success btn-sm",
                "exportOptions": {
                    "columns": ':visible'
                }
            }],
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Persian.json"
            }
        });
    </script>
@endsection
