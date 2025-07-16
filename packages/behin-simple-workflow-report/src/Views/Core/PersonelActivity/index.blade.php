@extends('behin-layouts.app')

@section('title', 'گزارش اقدامات پرسنل')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header">

            </div>
            <div class="card-body">
                <div class="col-sm-12 table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>شماره</th>
                                <th>نام</th>
                                <th>در دست اقدام</th>
                                <th>انجام داده</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td>{{ $user->number }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->inbox }}</td>
                                    <td onclick="showDones(`{{ $user->id }}`)">
                                        {{ $user->done }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        function showDones(userId){
            url = "{{ route('simpleWorkflowReport.personel-activity.showDones', 'user_id') }}";
            url = url.replace('user_id', userId)
            open_admin_modal(url);
        }
    </script>
@endsection
