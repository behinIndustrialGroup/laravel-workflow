{{-- <div class="row" style="border-bottom: solid 1px black">
    <h4>{{ $caseTitle ?? '' }} - {{ $processTitle ?? '' }}</h4>
    <button type="button" style="flex: auto; text-align: left" class="close" data-dismiss="modal"
        aria-hidden="true">&times;</button>
</div>
<div class="row" style="height: 10px"></div>
 --}}

@yield('content')

<div class="row form-group">
    <button class="btn btn-primary m-1" onclick="save_and_next()">{{ __('save and next') }}</button>

    <button class="btn btn-default m-1" onclick="save()">{{ __('save') }}</button>
</div>
<script>
    

    

    function save_and_next() {
        var fd = new FormData($("#main-form")[0]);
        fd.append('caseId', '{{ $caseId }}')
        fd.append('taskId', '{{ $taskId }}')
        fd.append('processId', '{{ $processId }}')

        // fd.append('del_index', '')
        // fd.append('task', '')
        // fd.append('user_logged', '')

        send_ajax_formdata_request(
            "{{ route('MkhodrooProcessMaker.api.saveAndNext') }}",
            fd,
            function(response) {
                console.log(response);
                show_message("ذخیره و ارسال شد");
                refresh_table();
                close_admin_modal();
            },
            function(er) {
                console.log(er);
                show_error(er);
                open_case_dynaform()
            }
        )
    }

    function save() {
        var fd = new FormData($("#main-form")[0]);
        fd.append('caseId', '{{ $caseId }}')
        fd.append('taskId', '{{ $taskId }}')
        fd.append('processId', '{{ $processId }}')
        // fd.append('del_index', '')
        // fd.append('task', '')
        // fd.append('user_logged', '')

        send_ajax_formdata_request(
            "{{ route('MkhodrooProcessMaker.api.save') }}",
            fd,
            function(response) {
                console.log(response);
                show_message("ذخیره شد");
                open_case_dynaform()
                refresh_table();
            },
            function(er) {
                console.log(er);
                show_error(er);
                open_case_dynaform()
            }
        )
    }

    function show_process_map(caseId) {
        url = "{{ route('MkhodrooProcessMaker.api.getCaseProcessMap', ['caseId' => 'caseId']) }}";
        url = url.replace('caseId', caseId);
        send_ajax_get_request(
            url,
            function(data) {
                open_admin_modal_with_data(data)
            }
        )
    }

    $(`input[type="checkbox"]`).change(function() {
        var input = $(`input[name="${$(this).attr('name')}"]`)
        if (this.checked) {
            input.val('on')
        } else {
            input.val('off')
        }
    });
</script>
@yield('script')
