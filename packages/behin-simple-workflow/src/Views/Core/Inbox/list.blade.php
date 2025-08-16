@extends('behin-layouts.app')

@section('content')
    <div class="container table-responsive card p-2">
        <h2>{{ trans('fields.User Inbox') }}</h2>
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        <div class="mb-3">
            <label for="process-filter" class="form-label">{{ trans('fields.Process') }}</label>
            <select id="process-filter" class="form-select">
                <option value="">{{ trans('fields.All') }}</option>
                @foreach ($processes as $process)
                    <option value="{{ $process->id }}" {{ (isset($selectedProcess) && $selectedProcess == $process->id) ? 'selected' : '' }}>{{ $process->name }}</option>
                @endforeach
            </select>
        </div>
        @if ($rows->isEmpty())
            {{-- <div class="alert alert-info">
            {{ trans('You have no items in your inbox.') }}
        </div> --}}
        @else
            <table class="table table-striped" id="inbox-list">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ trans('fields.Process Title') }}</th>
                        <th>{{ trans('fields.Task Title') }}</th>
                        <th>{{ trans('fields.Case Number') }}</th>
                        <th>{{ trans('fields.Case Title') }}</th>
                        <th>{{ trans('fields.Status') }}</th>
                        <th>{{ trans('fields.Received At') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rows as $index => $row)
                        <tr ondblclick="window.location.href = '{{ route('simpleWorkflow.inbox.view', $row->id) }}'">
                            <td>
                            <a href="{{ route('simpleWorkflow.inbox.view', $row->id) }}"
                                    class="btn btn-sm btn-primary"><i class="fa fa-external-link"></i></a>
                                {{ str_pad($index + 1, 3, '0', STR_PAD_LEFT) }}
                                
                                @if ($row->status == 'draft')
                                    <a href="{{ route('simpleWorkflow.inbox.delete', $row->id) }}"
                                        class="btn btn-sm btn-danger">{{ trans('fields.Delete') }}
                                        <i class="fa fa-trash"></i></a>
                                @endif
                            </td>
                            <td>{{ $row->task->process->name }}</td>
                            <td>{!! $row->task->styled_name !!}</td>
                            <td>{{ $row->case->number ?? '' }}</td>
                            <td>{{ $row->case_name }}</td>
                            <td>
                                @if ($row->status == 'new')
                                    <span class="badge bg-primary">{{ trans('fields.New') }}</span>
                                @elseif($row->status == 'in_progress')
                                    <span class="badge bg-warning">{{ trans('fields.In Progress') }}</span>
                                @elseif($row->status == 'draft')
                                    <span class="badge bg-info">{{ trans('fields.Draft') }}</span>
                                @else
                                    <span class="badge bg-success">{{ trans('fields.Completed') }}</span>
                                @endif
                            </td>
                            <td dir="ltr">
                                {{ toJalali($row->created_at)->format('Y-m-d') }}&nbsp;&nbsp;{{ toJalali($row->created_at)->format('H:i') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

    </div>
    <script>
        document.getElementById('process-filter').addEventListener('change', function () {
            const url = new URL(window.location.href);
            if (this.value) {
                url.searchParams.set('process', this.value);
            } else {
                url.searchParams.delete('process');
            }
            window.location.href = url.toString();
        });
    </script>
    @if (auth()->user()->access('کارتابل پراسس میکر'))
        <div class="container table-responsive card">
            <div class="alert alert-warning">کارتابل قدیم</div>
            <table class="table table-striped " id="draft-list">
                <thead>
                    <tr>
                        {{-- <th>{{__('Id')}}</th> --}}
                        <th>{{ __('Number') }}</th>
                        <th>{{ __('Process Name') }}</th>
                        <th>{{ __('Task Title') }}</th>
                        <th>{{ __('Case') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Send By') }}</th>
                        <th style="text-align: center; direction: ltr">{{ __('Send Date') }}</th>
                        <th style="text-align: center; direction: ltr">{{ __('Delay/Deadline') }}</th>
                    </tr>
                </thead>
            </table>
        </div>
        <script>
            var table = create_datatable(
                'draft-list',
                '{{ route('MkhodrooProcessMaker.api.todo') }}',
                [
                    // {data : 'APP_UID', render: function(APP_UID){return APP_UID.substr(APP_UID.length - 8)}},
                    {
                        data: 'APP_NUMBER'
                    },
                    {
                        data: 'PRO_TITLE'
                    },
                    {
                        data: 'TAS_TITLE'
                    },
                    {
                        data: 'DEL_TITLE',
                        render: function(data) {
                            data = data.replace('"', "")
                            return data.replace('"', "")
                        }
                    },
                    {
                        data: 'TAS_STATUS',
                        render: function(data) {
                            if (data == 'ON_TIME') {
                                return '{{ trans('ON_TIME') }}';
                            } else if (data == 'OVERDUE') {
                                return '{{ trans('OVERDUE') }}';
                            } else {
                                return data;
                            }
                        }
                    },
                    {
                        data: 'SEND_BY_INFO',
                        render: function(SEND_BY_INFO) {
                            if (SEND_BY_INFO.user_tooltip.usr_firstname) {
                                user = SEND_BY_INFO.user_tooltip;
                                name = user.usr_firstname + ' ' + user.usr_lastname
                                return name.substring(0, 15);
                            } else {
                                return '-';
                            }
                        }
                    },
                    {
                        data: 'DEL_DELEGATE_DATE',
                        render: function(DEL_DELEGATE_DATE) {
                            date = DEL_DELEGATE_DATE.split(" ")[0]
                            time = DEL_DELEGATE_DATE.split(" ")[1]
                            datetime = new Date(DEL_DELEGATE_DATE);
                            date = datetime.toLocaleDateString('fa-IR');
                            time = datetime.toLocaleTimeString('fa-IR');
                            return `<span style="float: left; direction: ltr">${date} ${time}</span>`;
                        }
                    },
                    {
                        data: 'DELAY',
                        render: function(DELAY, type, row) {
                            delay_day = DELAY.split(" ")[1]
                            delay_h = DELAY.split(" ")[3]
                            delay_m = DELAY.split(" ")[5]
                            delay_s = DELAY.split(" ")[7]
                            h = parseFloat(parseFloat(delay_day * 24) + parseFloat(delay_h) + parseFloat(delay_m / 60));
                            h = h.toFixed(2)
                            return `<span style="float: left; direction: ltr; color: ${row.TAS_COLOR_LABEL}">${h} h</span>`;
                        }
                    }
                ],
                function(row) {
                    $(row).css('cursor', 'pointer')
                },
                [
                    6, 'desc'
                ]
            );
            var APP_UID = PRO_TITLE = TAS_UID = DEL_TITLE = PRO_UID = DEL_INDEX = TAS_STATUS = '';
            table.on('dblclick', 'tr', function() {
                var data = table.row(this).data();
                console.log(data);

                APP_UID = data.APP_UID;
                PRO_TITLE = data.PRO_TITLE;
                TAS_UID = data.TAS_UID;
                DEL_TITLE = data.DEL_TITLE;
                PRO_UID = data.PRO_UID;
                DEL_INDEX = data.DEL_INDEX;
                TAS_STATUS = data.TAS_STATUS;

                open_case_dynaform();
            })

            function open_case_dynaform() {
                var fd = new FormData();
                fd.append('appUid', APP_UID);
                fd.append('processTitle', PRO_TITLE);
                fd.append('taskId', TAS_UID);
                fd.append('caseId', APP_UID);
                fd.append('caseTitle', DEL_TITLE);
                fd.append('processId', PRO_UID);
                fd.append('delIndex', DEL_INDEX);
                fd.append('taskStatus', TAS_STATUS);
                url = "{{ route('MkhodrooProcessMaker.api.getCaseDynaForm') }}";
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
            }



            function delete_case(caseId) {
                url = "{{ route('MkhodrooProcessMaker.api.deleteCase', ['caseId' => 'caseId']) }}";
                url = url.replace('caseId', caseId)
                console.log(url);
                send_ajax_get_request_with_confirm(
                    url,
                    function(response) {
                        console.log(response);
                        refresh_table()
                    },
                    '{{ __('Are You Sure For Delete This Item?') }}'
                )
            }
        </script>
    @endif
@endsection

@section('script')
    <script>
        $('#inbox-list').DataTable({
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Persian.json"
            }
        });
    </script>
@endsection
