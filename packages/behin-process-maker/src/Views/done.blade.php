@extends(config('pm_config.layout_name'))

@section('content')
    <div class="col-sm-12" style="height: 10px;"></div>
    <div class="card row table-responsive" style="padding: 5px">
        <table class="table table-striped " id="draft-list">
            <thead>
                <tr>
                    {{-- <th>{{__('Id')}}</th> --}}
                    <th>{{__('Number')}}</th>
                    <th>{{__('Process Name')}}</th>
                    <th>{{__('Task Title')}}</th>
                    <th>{{__('Case')}}</th>
                    <th>{{__('Pending')}}</th>
                    <th>{{__('Status')}}</th>
                    <th style="text-align: center; direction: ltr">{{__('Create Date')}}</th>
                    <th style="text-align: center; direction: ltr">{{__('DURATION')}}</th>
                    <th>{{__('ID')}}</th>
                </tr>
            </thead>
        </table>
    </div>
@endsection

@section('script')
    <script>
        
        $.get('{{ route("MkhodrooProcessMaker.api.done") }}', function(r){
            console.log(r);
        })
        var table = create_datatable(
            'draft-list',
            '{{ route("MkhodrooProcessMaker.api.done") }}',
            [
                // {data : 'APP_UID', render: function(APP_UID){return APP_UID.substr(APP_UID.length - 8)}},
                {data : 'APP_NUMBER'},
                {data : 'PRO_TITLE'},
                {data : 'TAS_TITLE'},
                {data : 'DEL_TITLE'},
                {data : 'PENDING', render: function(data){
                    var s = '';
                    data.forEach(element => {
                        s += '1- ' + element.tas_title + '(' + element.user_tooltip.usr_firstname + ' ' + element.user_tooltip.usr_lastname + ')<br>'
                    })
                    return s;
                }},
                {data : 'PENDING', render: function(data){
                    var s = '';
                    data.forEach(element => {
                        s = element.tas_status
                    })
                    return `{{ __('${s}') }}`;
                }},
                {data : 'APP_CREATE_DATE', render: function(DEL_DELEGATE_DATE){ 
                    date = DEL_DELEGATE_DATE.split(" ")[0]
                    time = DEL_DELEGATE_DATE.split(" ")[1]
                    return `<span style="float: left; direction: ltr">${date} ${time}</span>`; 
                }},
                {data : 'DURATION' , render: function(DELAY, type, row){ 
                    delay_day = DELAY.split(" ")[1]
                    delay_h = DELAY.split(" ")[3]
                    delay_m = DELAY.split(" ")[5]
                    delay_s = DELAY.split(" ")[7]
                    return `<span style="float: left; direction: ltr; color: ${row.TAS_COLOR_LABEL}">${delay_day}d  ${delay_h}h ${delay_m}m ${delay_s}s</span>`; 
                }},
                {data : 'APP_UID'},
            ],
            function(row){
                $(row).css('cursor', 'pointer')
            }
        );
        table.on('dblclick', 'tr', function(){
            var data = table.row( this ).data();
            var fd = new FormData();
            fd.append('processId', data.PRO_UID)
            fd.append('caseId', data.APP_UID)
            send_ajax_formdata_request(
                '{{ route("MkhodrooProcessMaker.api.getCaseMainForm") }}',
                fd,
                function(res){
                    console.log(res);
                    open_admin_modal_with_data(res)
                }
            )
            
        })

        // function delete_case(caseId){
        //     url = "{{ route('MkhodrooProcessMaker.api.deleteCase', [ 'caseId' => 'caseId' ]) }}";
        //     url = url.replace('caseId', caseId)
        //     console.log(url);
        //     send_ajax_get_request_with_confirm(
        //         url,
        //         function(response){
        //             console.log(response);
        //             refresh_table()
        //         },
        //         '{{__("Are You Sure For Delete This Item?")}}'
        //     )
        // }
    </script>
@endsection