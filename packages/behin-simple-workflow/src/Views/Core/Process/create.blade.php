@extends('behin-layouts.app')

@section('content')
<div class="container">
    <form action="javascript:void(0)" id="create-process-form">
        <input type="text" name="name" id="">
        <input type="text" name="category" id="">
        <input type="text" name="case_prefix" id="">
    </form>
    <button onclick="create_process()">{{ trans('Create') }}</button>
</div>
@endsection

@section('script')
    <script>
        function create_process(){
            var form = $('#create-process-form')[0];
            var fd = new FormData(form);
            send_ajax_formdata_request(
                "{{ route('simpleWorkflow.process.store') }}",
                fd,
                function(response){
                    console.log(response);

                }
            )

        }
    </script>
@endsection
