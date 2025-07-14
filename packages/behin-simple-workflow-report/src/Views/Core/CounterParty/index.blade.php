@extends('behin-layouts.app')

@section('title')
@endsection

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-body">
                <a href="{{ route('simpleWorkflowReport.counter-party.create') }}" class="btn btn-primary btn-sm">ایجاد
                </a>
            </div>
        </div>
        <div class="card">
            <div class="card-header bg-success">طرف حساب ها</div>
            <div class="card-body table-responsive">
                <table class="table" id="cases">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>نام</th>
                            <th>شماره حساب</th>
                            <th>اقدام</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($counterParties as $counterParty)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $counterParty->name }}</td>
                                <td>{{ $counterParty->account_number }}</td>
                                <td>
                                    // در blade
                                    <form action="{{ route('counter-party.destroy', $counterParty->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"><i
                                                class="fa fa-trash"></i></button>
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
