@extends('PMViews::dyna-form-layout')
@section('content')
    {!! $html !!}
@endsection

@section('script')
    <script>
        function delete_doc(id) {
            var fd = new FormData();
            fd.append('id', id);
            fd.append('processId', '{{$processId}}');
            fd.append('caseId', '{{$caseId}}');
            fd.append('taskId', '{{$taskId}}');
            fd.append('processTitle', '{{$processTitle}}');
            fd.append('caseTitle', '{{$caseTitle}}');
            fd.append('delIndex', '{{$delIndex}}');
            send_ajax_formdata_request_with_confirm(
                '{{route("MkhodrooProcessMaker.api.deleteDoc")}}',
                fd,
                function(response) {
                    open_admin_modal_with_data(response, '' , function(){initial_view()})
                }
            )
        }
    </script>
@endsection
