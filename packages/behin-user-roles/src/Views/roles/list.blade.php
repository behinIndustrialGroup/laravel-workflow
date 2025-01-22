@extends('behin-layouts.app')

@section('content')
    <div class="box">
        <div class="card">
            <table class="table table-stripped" id="role-table">
                <thead>
                    <tr>
                        <th>شناسه</th>
                        <th>نام</th>
                        <th>ایجاد</th>
                        <th>اقدام</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($roles as $role)
                        <tr>
                            <td>{{ $role->id }}</td>
                            <td>{{ $role->name }}</td>
                            <td>{{ $role->created_at }}</td>
                            <td>
                                <a class="btn btn-sm btn-primary" href="{{ route('role.show', $role->id) }}"><i
                                        class="fa fa-edit"></i></a>
                                <a class="btn btn-sm btn-info" href="{{ route('role.copy', $role->id) }}"><i
                                        class="fa fa-copy"></i></a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('script')
    <script>
        // var table = create_datatable(
        //     "role-table",
        //     "{{ route('role.list') }}",
        //     [
        //         {data : 'id'},
        //         {data : 'name'},
        //         {data : 'created_at'}
        //     ]
        // )

        // table.on('click', 'tr', function(){
        //     var data = table.row( this ).data();
        //     show_edit_modal(data.id);
        // })

        function show_edit_modal(id) {
            var fd = new FormData();
            fd.append('id', id);
            send_ajax_formdata_request(
                "{{ route('role.get') }}",
                fd,
                function(body) {
                    open_admin_modal_with_data(body);
                },
                function(data) {
                    show_error(data);
                    console.log(data);
                }
            )
        }
    </script>
@endsection
