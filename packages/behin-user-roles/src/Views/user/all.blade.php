@extends('behin-layouts.app')

@section('title')
    کاربران
@endsection

@section('content')
    <div class="container">
        <div class="card ">
            <div class="card-header">
                <a href="{{route('users.create')}}">
                    <button class="btn btn-primary">
                        ایجاد کاربر
                    </button>
                </a>
            </div>

            <div class="card-body table-responsive">
                <table class="table" id="table">
                    <thead>
                        <tr>
                            <th>شناسه</th>
                            <th>شماره پرسنلی</th>
                            <th>نام</th>
                            <th>نام کاربری</th>
                            <th>نقش</th>
                            <th>ویرایش</th>

                        </tr>
                    </thead>
                    @foreach($users as $user)
                        <tr>
                            <td>{{$user->id}}</td>
                            <td>{{$user->number}}</td>
                            <td>{{$user->name}}</td>
                            <td>{{$user->email}}</td>
                            <td>{{$user->role()->name ?? ''}}</td>
                            <td><a href="{{$user->id}}"><i class="fa fa-edit"></i></a></td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $('#table').DataTable({
                responsive: true,
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.10.24/i18n/Persian.json",
                },
            });
        });
    </script>
@endsection
