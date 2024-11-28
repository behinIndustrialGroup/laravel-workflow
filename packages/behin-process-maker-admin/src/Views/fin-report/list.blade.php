@extends(config('pm_config.layout_name'))

@section('content')
    <div class="row p-2">
        <form action="javascript:void(0)" id="filter_form" class="form-inline">
            <div class="form-group mb-2">
                <label for="last_status" class="mr-2">{{ trans('Last Status') }}</label>
                <select name="last_status" id="last_status" class="form-control mr-3">
                    <option value="">{{ trans('All') }}</option>
                    @foreach ($last_statuses as $status)
                        <option value="{{ $status->value }}">{{ $status->value }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group mb-2">
                <button class="btn btn-primary" onclick="filter()">{{ trans('Filter') }}</button>
            </div>
        </form>
    </div>

    <div class="card row table-responsive" style="padding: 5px">
        <table class="table table-striped " id="list">
            <thead>
                <tr>
                    {{-- <th>{{__('Id')}}</th> --}}
                    <th>{{ __('Case ID') }}</th>
                    <th>{{ __('Case Number') }}</th>
                    <th>{{ __('Number') }}</th>
                    <th>{{ __('Customer Fullname') }}</th>
                    <th>{{ __('Receive Date') }}</th>
                    <th>{{ __('Device Name') }}</th>
                    <th>{{ __('Repairman') }}</th>
                    <th>{{ __('Repair Report') }}</th>
                    <th>{{ __('Repair Cost') }}</th>
                    <th>{{ __('Last Status') }}</th>
                    {{-- <th>{{__('Current User')}}</th> --}}
                    <th>{{ __('Case History') }}</th>
                </tr>
            </thead>
        </table>
    </div>
@endsection

@section('script')
    <script>
        createTable()

        function filter() {
            var fd = new FormData($('#filter_form')[0]);
            send_ajax_formdata_request(
                "{{ route('pmAdmin.api.getDataOfFinReport') }}",
                fd,
                function(response) {
                    console.log(response);
                    update_datatable(response)
                }
            )
        }

        function createTable() {
            var table = create_empty_datatable(
                'list',
                [
                    // {data : 'APP_UID', render: function(APP_UID){return APP_UID.substr(APP_UID.length - 8)}},
                    {
                        data: 'app_number',
                        @if (auth()->user()->name != 'admin')
                            visible: false
                        @endif
                    },
                    {
                        data: 'case_number'
                    },
                    {
                        data: 'case_id',
                        visible: false
                    },
                    {
                        data: 'customer_fullname'
                    },
                    {
                        data: 'receive_date'
                    },
                    {
                        data: 'device_name'
                    },
                    {
                        data: 'repairman_name'
                    },
                    {
                        data: 'repair_report',
                        visible: false,
                        render: function(data) {
                            if (data && data.length > 10) {
                                return data.substring(0, 10) + '...';
                            } else {
                                return data;
                            }
                        }
                    },
                    {
                        data: 'repair_cost', render: function(data){
                            // const p2e = s => s.replace(/[۰-۹]/g, d => '۰۱۲۳۴۵۶۷۸۹'.indexOf(d))
                            // if(data){
                            //     return parseInt(p2e(data).replace(/,/g, '')).toLocaleString()
                            // }
                            return data;
                        }
                    },
                    {
                        data: 'last_status'
                    },
                    {
                        data: 'case_id',
                        render: function(data) {
                            return `<button class='btn btn-info' onclick='show_history("${data}")'>show</button>`
                        },
                        visible: false
                    }
                ],
                function(row) {
                    $(row).css('cursor', 'pointer')
                },
                [4, 'desc']
            );
            table.on('dblclick', 'tr', function() {
                var data = table.row(this).data();
                console.log(data);
                var fd = new FormData();
                fd.append('caseId', data.case_id);
                fd.append('processId', data.process_id);
                url = "{{ route('pmAdmin.form.caseFinDetails') }}";
                console.log(url);
                send_ajax_formdata_request(
                    url,
                    fd,
                    function(response) {
                        // console.log(response);
                        open_admin_modal_with_data(response, '', function() {
                            initial_view()
                        })
                    }
                )
            })
        }

        function delete_case_info_from_db(processId, caseId) {
            url = "{{ route('pmAdmin.api.deleteCase') }}";
            var fd = new FormData();
            fd.append('processId', processId);
            fd.append('caseId', caseId);
            send_ajax_formdata_request_with_confirm(
                url,
                fd,
                function(response) {
                    console.log(response);
                    refresh_table()
                },
                '{{ __('Are You Sure For Delete This Item?') }}'
            )
        }

        function reassign(caseId) {
            url = "{{ route('pmAdmin.api.reassign') }}";
            var fd = new FormData();
            // fd.append('processId', processId);
            fd.append('caseId', caseId);
            send_ajax_formdata_request_with_confirm(
                url,
                fd,
                function(response) {
                    console.log(response);
                    refresh_table()
                },
                '{{ __('Are You Sure For Delete This Item?') }}'
            )
        }

        function show_history(case_id) {
            var url = "{{ route('pmAdmin.form.caseHistoryForm') }}";
            var fd = new FormData();
            fd.append('caseId', case_id);
            send_ajax_formdata_request(
                url,
                fd,
                function(res) {
                    console.log(res);

                    open_admin_modal_with_data(res)
                }
            )
        }
    </script>
@endsection
