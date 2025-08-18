@extends('behin-layouts.app')

@section('title', 'دفتر تلفن')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header bg-success">دفتر تلفن
                <a href="{{ route('simpleWorkflowReport.phonebook.create') }}" class="btn btn-light btn-sm float-end" style="color: black !important">افزودن</a>
            </div>
            <div class="card-body table-responsive">
                <table class="table" id="phonebook">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ trans('fields.customer_name') }}</th>
                            <th>{{ trans('fields.customer_mobile') }}</th>
                            <th>{{ trans('fields.customer_nid') }}</th>
                            <th>{{ trans('fields.customer_address') }}</th>
                            <th>عملیات</th>
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
                                <td>
                                    <a href="{{ route('simpleWorkflowReport.phonebook.edit', $customer->id) }}" class="btn btn-primary btn-sm">ویرایش</a>
                                    <form method="POST" action="{{ route('simpleWorkflowReport.phonebook.destroy', $customer->id) }}" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">حذف</button>
                                    </form>
                                </td>
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
            @if(auth()->user()->access('خروجی اکسل دفتر تلفن'))
            "buttons": [{
                "extend": 'excelHtml5',
                "text": "خروجی اکسل",
                "title": "دفتر تلفن",
                "className": "btn btn-success btn-sm",
                "exportOptions": {
                    "columns": ':visible'
                }
            }],
            @endif
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Persian.json"
            }
        });
    </script>
@endsection
