<button type="button" id="closer" class="btn-close border-0 bg-transparent" style="font-size:32px" data-dismiss="modal" aria-label="Close">&times;</button>
<form action="javascript:void(0)" method="POST" id="task-detail" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="id" id="" value="{{ $task->id }}">
    <div class="col-sm-12 mt-3">
        <input type="checkbox" id="done" name="done"
            @if ($task->done) checked @endif>
        <span>کار انجام شد</span>
    </div>
    <div class="col-sm-12 mt-2">
        <label for="task" class="col-sm-12">کار :</label>
        <input type="text" id="task" name="task" value="{{ $task->task }}" class="col-sm-12 mb-2">
    </div>
    <div class="col-sm-12 mt-2">
        <label for="description" class="col-sm-12">توضیحات کار :</label>
        <textarea name="description" id="description" cols="30" rows="10" class="col-sm-12">{{ $task->description }}</textarea>
    </div>
    @if ($files)
        @foreach ($files as $file)
            <div class="col-sm-12 mt-2">
                <a href="{{ url($file->file_path) }}" download="">{{ $file->file_path }}</a>
            </div>
        @endforeach
    @endif
    <div class="col-sm-12 mt-2">
        <label for="file">فایل</label>
        <input type="file" class="form-control form-control-sm" name="file" id="file">
    </div>

    <div class="col-sm-12 mt-2">
        <label for="edit_reminder_date" class="col-sm-12">تاریخ یادآوری :</label>
        <input type="hidden" id="edit_reminder_date" name="reminder_date" value="{{ $task->reminder_date }}">
        <input type="text" id="edit_reminder_date_view" class="col-sm-12 form-control m-1">
    </div>
    <div class="col-sm-12 mt-2">
        <label for="edit_due_date">تاریخ تحویل :</label class="col-sm-12">
        <input type="hidden" id="edit_due_date" name="due_date" value="{{ $task->due_date }}">
        <input type="text" id="edit_due_date_view" class="col-sm-12 form-control m-1">
    </div>
    <button type="submit" onclick="update()" class="mt-2 mr-2 btn btn-primary">بروزرسانی</button>
    @if ($task->creator == Auth::id())
    <button type="submit" onclick="destroy()" class="mt-2 ml-2 btn btn-danger" style="float: left">حذف</button>
    @endif
</form>

<script>
    function update() {
        fd = new FormData($('#task-detail')[0])
        fd.append('_method', 'PUT')
        send_ajax_formdata_request(
            "{{ route('todoList.update') }}",
            fd,
            function(res) {
                show_message(res);
                console.log(res);
                refresh_table();
                show_task_modal("{{ $task->id }}");
            }
        )
    }

    function destroy() {
        fd = new FormData($('#task-detail')[0])
        fd.append('_method', 'DELETE')
        send_ajax_formdata_request_with_confirm(
            "{{ route('todoList.delete') }}",
            fd,
            function(res) {
                show_message(res);
                console.log(res);
                refresh_table();
                $("#closer").trigger( "click" );

            }
        )
    }


    $("#edit_due_date_view").persianDatepicker({
        format: 'YYYY-MM-DD',
        toolbox: {
            calendarSwitch: {
                enabled: true
            }
        },
        initialValue: false,
        observer: true,
        altField: '#edit_due_date'
    });


    $("#edit_reminder_date_view").persianDatepicker({
        format: 'YYYY-MM-DD',
        toolbox: {
            calendarSwitch: {
                enabled: true
            }
        },
        initialValue: false,
        observer: true,
        altField: '#edit_reminder_date'
    });

    var ReminderDateData = "{{ $task->reminder_date }}";
    var ReminderDate = convertTimeStampToJalali(parseInt(ReminderDateData));
    $("#edit_reminder_date_view").val(ReminderDate);

    var DueDateData = "{{ $task->due_date }}";
    var DueDate = convertTimeStampToJalali(parseInt(DueDateData));
    $("#edit_due_date_view").val(DueDate);

    function convertTimeStampToJalali(timestamp) {
        var date = new Date(timestamp);
        if (!date)
            return false;
        return (gregorian_to_jalali(date.getFullYear(), (date.getMonth() + 1), date.getDate()));
    } //end of function convertTimeStampToJalali

    function gregorian_to_jalali(gy, gm, gd) {
        g_d_m = [0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334];
        if (gy > 1600) {
            jy = 979;
            gy -= 1600;
        } else {
            jy = 0;
            gy -= 621;
        }
        gy2 = (gm > 2) ? (gy + 1) : gy;
        days = (365 * gy) + (parseInt((gy2 + 3) / 4)) - (parseInt((gy2 + 99) / 100)) + (parseInt((gy2 + 399) / 400)) -
            80 + gd + g_d_m[gm - 1];
        jy += 33 * (parseInt(days / 12053));
        days %= 12053;
        jy += 4 * (parseInt(days / 1461));
        days %= 1461;
        if (days > 365) {
            jy += parseInt((days - 1) / 365);
            days = (days - 1) % 365;
        }
        jm = (days < 186) ? 1 + parseInt(days / 31) : 7 + parseInt((days - 186) / 30);
        jd = 1 + ((days < 186) ? (days % 31) : ((days - 186) % 30));
        return [jy, jm, jd];
    } //end of function gregorian_to_jalali

    function show_task_modal(id) {
        var fd = new FormData();
        fd.append('id', id);
        send_ajax_formdata_request(
            "{{ route('todoList.edit') }}",
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
</script>
