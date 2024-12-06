@extends('behin-layouts.app')

@section('content')
    <div class="container">
        <table class="table">
            <thead>
                <tr>
                    <th>{{ trans('Id') }}</th>
                    <th>{{ trans('Name') }}</th>
                    <th>{{ trans('Action') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($forms as $form)
                    <form action="{{ route('simpleWorkflow.form.store') }}" method="POST">
                        @csrf
                        <tr>
                            <td>{{ $form->id }} <input type="hidden" name="id" value="{{ $form->id }}"></td>
                            <td>{{ $form->name }}</td>
                            <td><a class="btn btn-success"
                                    href="{{ route('simpleWorkflow.form.edit', ['id' => $form->id]) }}">{{ trans('Edit') }}</a>
                            </td>
                        </tr>
                    </form>
                @endforeach
            </tbody>
            <tfoot>
                <form action="{{ route('simpleWorkflow.form.create') }}" method="POST">
                    @csrf
                    <tr>
                        <td></td>
                        <td><input type="text" name="name" id="" value=""></td>
                        </td>
                        <td><button class="btn btn-success">{{ trans('Create') }}</button></td>
                    </tr>
                </form>
            </tfoot>
        </table>
    </div>
@endsection

@section('script')
    <script>
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
    </script>
@endsection
