<div class="modal fade bs-example-modal-lg" id="ca-modal" tabindex="-1" role="dialog"
    aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="javascript:void(0)" id="ca-form">
                <input type="hidden" name="ca_id" id="ca_id">
                <div class="modal-body" id="modal-body"></div>
            </form>
            
            <div class="modal-footer">
                <button class="btn btn-info" onclick="save_and_next()">{{ __('save and next') }}</button>
                <button class="btn btn-default" onclick="save()">{{ __('save') }}</button>
                <button type="button" class="btn btn-danger waves-effect text-left" data-dismiss="modal">{{ __('close') }}</button>
            </div>
        </div>
    </div>
</div>

<script>
    function save_and_next(){
        var form = $('#ca-form');
        var data = form.serialize();
        send_ajax_request(
            '{{ route("admin.case-activity.save_next") }}',
            data,
            function(data){
                hide_loading();
                alert_notification('{{ __("done") }}');
                $('#ca-modal').modal('hide');
                refresh_table()
            }
        );
    }
    function save(){
        var form = $('#ca-form');
        var data = form.serialize();
        send_ajax_request(
            '{{ route("admin.case-activity.save") }}',
            data,
            function(data){
                hide_loading();
                alert_notification('{{ __("done") }}');
                refresh_table();
            }
        );
    }
</script>