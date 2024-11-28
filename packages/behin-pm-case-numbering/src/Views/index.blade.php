@extends(config('pm_config.layout_name'))

@section('content')
    <div class="row">
        <div class="table-responsive">
            <table class="table table-stripped">
                <thead>
                    <tr>
                        <th>{{ trans("id") }}</th>
                        <th>{{ trans("process id") }}</th>
                        <th>{{ trans("count") }}</th>
                        <th>{{ trans("api key") }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rows as $row)
                        <tr>
                            <td>{{ $row->id }}</td>
                            <td>{{ $row->process_id }}</td>
                            <td>{{ $row->count }}</td>
                            <td>{{ $row->api_key }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection