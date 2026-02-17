@php
    use Behin\SimpleWorkflow\Models\Entities\Parts;

    $parts = Parts::where('case_number', $case->number)->get();
@endphp

<div class="card">
    <div class="card-header bg-warning">
        <h6 class="m-0 font-weight-bold">مشخصات مشتری</h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="name">نام</label>
                    {{ $case->getVariable('customer_workshop_or_ceo_name') }}
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="name">تاریخ پذیرش</label>
                    {{ $case->getVariable('admision_date') }}
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="name">توضیحات اولیه مشتری</label>
                    {{ $case->getVariable('initial_description') }}
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="name">شماره پرونده</label>
                    {{ $case->number }}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-success">
        <h6 class="m-0 font-weight-bold">قطعه ها</h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="name">تعداد قطعات پذیرش شده</label>
                    {{ $parts->count() }}
                </div>
            </div>
            @foreach ($parts as $part)
                <div class="col-md-12 row">
                    <div class="form-group">
                        <label for="name">نام</label>
                        {{ $part->name }}
                    </div>
                    <div class="form-group">
                        <label for="name">تصویر اولیه</label>
                        @if ($part->initial_part_pic)
                            <a href="{{ url($part->initial_part_pic) }}" alt="{{ $part->name }}"
                                width="100" download><i class="fa fa-download"></i></a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-info">
        <h6 class="m-0 font-weight-bold">تاریخچه عملیات صورت گرفته</h6>
    </div>
    <div class="card-body">

        <div class="row table-responsive">
            <table class="table table-bordered">
                @foreach ($case->getHistoryList()->unique(fn($row) => $row->task->id . '|' . $row->created_at->format('Y-m-d H:i')) as $row)
                    @if (in_array($row->task->id, [
                        '9f6b7b5c-155e-4698-8b05-26ebb061bb7d',
                        '19a1be98-7b4a-4100-903d-e6612c4c4a6c',
                        'adee777f-da9d-4d54-bf00-020a27e0f861',
                        '9cfbbbf7-e53f-4706-b7c9-c69c0dd84cc4',
                        '039a097e-2159-49df-b866-f7766aaf2cfc',
                        'd213de29-832b-4de5-8ef1-03295835e5ae', //گزارش تعمیر
                        'ffa2f261-f3b0-4c7f-b705-1ff46705a118', //تایید تعمیر
                        'b9ab688a-7819-4d83-a53a-a396aa540232', //
                        '8bee90b3-6bc0-4537-86d0-715583566064',
                        '0fcd1c57-183e-4c4a-8e5a-b218f972a57d',
                        'f9cc1cf5-e3b0-4a46-91a5-2e7e59d29784',
                        '36f0f696-5694-4731-a179-a70e9a686ef5',
                        'af1b47b1-167f-4371-b028-ac1fe94ee532',
                        '19f15a6f-1ec8-488c-adea-6c8419fe850a',
                    ]))
                        <tr class="@if($row->status == 'done') text-success @endif">
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                {{ $row->task->name }} @if($row->status == 'new') <span class="badge badge-warning">در حال انجام</span> @endif
                            </td>
                            <td dir="ltr">
                                {{ toJalali($row->created_at)->format('Y-m-d H:i') }}
                            </td>
                        </tr>
                    @endif
                @endforeach
            </table>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#done-container').remove();
        $('#button-container').remove();
    });
</script>
