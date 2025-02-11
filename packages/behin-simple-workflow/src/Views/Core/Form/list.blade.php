@extends('behin-layouts.app')

@section('title')
    {{ trans('Form List') }}
@endsection

@section('content')
    <div class="container table-responsive card p-2">
        <table class="table table-striped" id="form-list">
            <thead>
                <tr>
                    <th>{{ trans('Id') }}</th>
                    <th>{{ trans('Name') }}</th>
                    <th>{{ trans('Action') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($forms as $form)
                        @csrf
                        <tr>
                            <td>{{ $form->id }} <input type="hidden" name="id" value="{{ $form->id }}"></td>
                            <td>{{ $form->name }}</td>
                            <td><a class="btn btn-sm btn-success"
                                    href="{{ route('simpleWorkflow.form.edit', ['id' => $form->id]) }}">{{ trans('Edit') }}</a>
                                    <a class="btn btn-sm btn-success"
                                    href="{{ route('simpleWorkflow.form.editContent', ['id' => $form->id]) }}">{{ trans('Edit Content') }}</a>
                                    <button class="btn btn-sm btn-info" onclick="copyForm('{{ $form->id }}')"><i class="fa fa-copy" ></i></button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteForm('{{ $form->id }}')"><i class="fa fa-trash"></i></button>
                            </td>
                        </tr>
                @endforeach
            </tbody>
        </table>
        <form action="{{ route('simpleWorkflow.form.create') }}" method="POST">
            @csrf
            <tr>
                <td></td>
                <td><input type="text" name="name" id="" value=""></td>
                </td>
                <td><button class="btn btn-success">{{ trans('Create') }}</button></td>
            </tr>
        </form>
    </div>
@endsection

@section('script')
    <script>
        $('#form-list').DataTable({
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Persian.json"
            }
        });
        function create_process() {
            var form = $('#create-process-form')[0];
            var fd = new FormData(form);
            send_ajax_formdata_request(
                "{{ route('simpleWorkflow.process.create') }}",
                fd,
                function(response) {
                    console.log(response);

                }
            )

        }

        function copyForm(id) {
            var fd = new FormData();
            fd.append('id', id);
            send_ajax_formdata_request(
                "{{ route('simpleWorkflow.form.copy') }}",
                fd,
                function(response) {
                    console.log(response);
                    show_message(response.msg)
                    location.reload();
                }
            )
        }

        function deleteForm(id) {
            var fd = new FormData();
            fd.append('id', id);
            send_ajax_formdata_request_with_confirm(
                "{{ route('simpleWorkflow.form.delete') }}",
                fd,
                function(response) {
                    console.log(response);
                    show_message(response.msg)
                    location.reload();
                }
            )
        }
    </script>
@endsection
