@extends(config('pm_config.layout_name'))

@section('content')
    <div class="col-sm-12" style="height: 10px;"></div>
    <div class="card row">
        <table class="table table-striped " id="draft-list">
            <thead>
                <tr>
                    <th>{{ __('Process Name') }}</th>
                    <th>{{ __('Description') }}</th>
                </tr>
            </thead>
        </table>
    </div>
@endsection

@section('script')
    <script>
        $.get('{{ route('MkhodrooProcessMaker.api.startProcess') }}', function(r) {
            console.log(r);
        })
        var table = create_datatable(
            'draft-list',
            '{{ route('MkhodrooProcessMaker.api.startProcess') }}',
            [{
                    data: 'pro_title'
                },
                {
                    data: 'pro_description'
                },
            ]
        );

        table.on('dblclick', 'tr', function() {
            var data = table.row(this).data();
            console.log(data);
            var fd = new FormData();
            fd.append('taskId', data.tas_uid);
            fd.append('processId', data.pro_uid);
            send_ajax_formdata_request(
                "{{ route('MkhodrooProcessMaker.api.newCase') }}",
                fd,
                function(response) {
                    console.log(response);
                    var fd = new FormData();
                    fd.append('processTitle', data.pro_title);
                    fd.append('taskId', data.tas_uid);
                    fd.append('caseId', response.caseId);
                    fd.append('caseTitle', data.pro_title);
                    fd.append('processId', data.pro_uid);
                    fd.append('delIndex', "1");
                    url = "{{ route('MkhodrooProcessMaker.api.getCaseDynaForm') }}";
                    // url = url.replace('caseId', response.app_uid);
                    // console.log(url);
                    send_ajax_formdata_request(
                        url,
                        fd,
                        function(response){
                            // console.log(response);
                            open_admin_modal_with_data(response, '' , function(){initial_view()})
                        }
                    )
                }
            )
        })
    </script>
@endsection
