@extends('layouts.app')

@section('content')
    <div>
        <h2 class="col-12">اضافه کردن شهر</h2>
        <form action="javascript:void(0)" id="city-form">
            @csrf
            <label for="province" class="col-6">استان :</label>
            <input type="text" id="province" name="province" placeholder="{{ __('province') }}" class="col-6">
            <label for="city" class="col-6">شهر :</label>
            <input type="text" id="city" name="city" placeholder="{{ __('city') }}" class="col-6">
            <button class="btn btn-primary" onclick="add()">اضافه کردن</button>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-stripped" id="cities-table">
            <thead>
                <tr>
                    <th>شناسه</th>
                    <th>استان</th>
                    <th>شهرستان</th>
                </tr>
            </thead>
        </table>
    </div>
@endsection

@section('script')
    <script>
        var table = create_datatable(
            'cities-table',
            "{{ route('city.list') }}",
            [{
                    data: 'id'
                },
                {
                    data: 'province'
                },
                {
                    data: 'city'
                },
            ],
        )

        table.on('dblclick', 'tr', function() {
            var data = table.row(this).data();
            if (data != undefined) {
                show_task_modal(data.id);
            }
        });

        function show_task_modal(id) {
            var fd = new FormData();
            fd.append('id', id);
            send_ajax_formdata_request(
                "{{ route('city.edit') }}",
                fd,
                function(body) {
                    open_admin_modal_with_data(body, '', function() {
                        $(".direct-chat-messages").animate({
                            scrollTop: $('.direct-chat-messages').prop("scrollHeight")
                        }, 1);
                    });
                },
                function(data) {
                    show_error(data);
                }
            )
        }

        function add() {
            var form = $('#city-form')[0];
            var fd = new FormData(form);
            send_ajax_formdata_request(
                '{{ route('city.create') }}',
                fd,
                function(response) {
                    console.log(response);
                    table.ajax.reload();
                    form.reset();
                }
            )

        }
    </script>
@endsection
