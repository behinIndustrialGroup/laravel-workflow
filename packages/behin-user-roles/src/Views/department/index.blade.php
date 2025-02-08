@extends('behin-layouts.app')

@section('title')
    دپارتمان ها
@endsection

@section('content')
    <div class="container">
        <div class="card ">
            <div class="card-header">
                <a href="{{route('department.create')}}" class="btn btn-primary">
                    ایجاد گروه
                </a>
            </div>

            <div class="card-body table-responsive">
                <table class="table" id="table">
                    <thead>
                        <tr>
                            <th>شناسه</th>
                            <th>نام</th>
                            <th>مدیر</th>
                            <th>والد</th>
                            <th>ویرایش</th>

                        </tr>
                    </thead>
                    @foreach($departments as $department)
                        <tr>
                            <td>{{$department->id}}</td>
                            <td>{{$department->name}}</td>
                            <td>{{$department->manager}}</td>
                            <td>{{$department->parent_id}}</td>
                            <td><a href="{{route('department.edit', $department->id)}}"><i class="fa fa-edit"></i></a></td>
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
