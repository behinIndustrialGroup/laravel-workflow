@extends('behin-layouts.app')

@section('title', 'گزارش قطعات درخواستی از انبار')

@section('content')

    <div class="card row">
        <div class="card-body row">
            <form action="{{ route('simpleWorkflowReport.consumable-parts.report') }}" method="GET" id="search-form"
                class="row">
                <div class="col-sm-4 mb-1">
                    <input type="text" class="form-control" name="item_name" value="{{ request('item_name') }}"
                        placeholder="نام کالا">
                </div>
                <div class="col-sm-3 mb-1">
                    <select name="requester_id" id="" class="form-control select2">
                        <option value="">انتخاب کنید</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}"
                                {{ request('requester_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-5"></div>
                <div class="col-sm-4 mb-1">
                    <select name="date_column" id="date_column" class="form-control">
                        <option value="created_at" {{ request('date_column') == 'created_at' ? 'selected' : '' }}>تاریخ
                            درخواست</option>
                    </select>
                </div>
                <div class="col-sm-3 mb-1">
                    <input type="text" class="form-control persian-date" name="from_date"
                        value="{{ request('from_date') }}" placeholder="از تاریخ">
                </div>
                <div class="col-sm-3 mb-1">
                    <input type="text" class="form-control persian-date" name="to_date" value="{{ request('to_date') }}"
                        placeholder="تا تاریخ">
                </div>
                <div class="col-sm-4 mb-1">
                    <select name="status" id="status" class="form-control">
                        <option value="">انتخاب کنید</option>
                        @foreach ($statuses as $statusKey => $statusValue)
                            <option value="{{ $statusValue['label'] }}"
                                {{ request('status') == $statusValue['label'] ? 'selected' : '' }}>
                                {{ $statusValue['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-2 mb-1">
                    <button class="btn btn-info">جستجو</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">تمام کالاها</div>
        <div class="card-body table-responsive">
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th>قطعه</th>
                        <th>درخواست کننده</th>
                        <th>تعداد درخواست شده</th>
                        <th>تعداد تحویل شده</th>
                        <th>تعداد بازگشت داده شده</th>
                        <th>وضعیت</th>
                        <th>تاریخ درخواست</th>
                        <th>قیمت</th>
                        <th>منبع تامین</th>
                        <th>تاریخ فاکتور</th>
                        <th>شماره فاکتور</th>
                        <th>اقدامات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($allRequests as $request)
                        <tr>
                            <td>
                                <div>{{ $request->product_name }}</div>
                                @if ($request->item_id)
                                    <div class="text-muted small">شناسه: {{ $request->item_id }}</div>
                                @endif
                            </td>
                            <td>{{ getUserInfo($request->created_by)->name ?? '' }}</td>
                            <td>{{ $request->requested_quantity }}</td>
                            <td>{{ $request->delivered_quantity }}</td>
                            <td>{{ $request->returned_quantity }}</td>
                            <td>
                                @php
                                    $status = collect($statuses)
                                        ->where('label', $request->consumable_part_status)
                                        ->first();
                                @endphp
                                <span class="badge"
                                    style="background: {{ $status['color'] ?? '#cccccc' }}">{{ $request->consumable_part_status }}</span>
                            </td>
                            <td dir="ltr">
                                @if ($request->created_at)
                                    {{ toJalali($request->created_at)->format('Y-m-d H:i') }} <br>
                                @else
                                    ـ
                                @endif
                            </td>
                            <td>[[ $request->amount ? number_format($request->amount) : $request->amount ]]</td>
                            <td>{{ $request->supply_source }}</td>
                            <td>{{ $request->invoice_date }}</td>
                            <td>{{ $request->invoice_number }}</td>
                            <td>
                                <button class="btn btn-sm btn-danger"
                                    onclick="confirmDelete('{{ route('simpleWorkflowReport.consumable-parts.delete', $request->id) }}')">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">درخواستی ثبت نشده است.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if ($allRequests->hasPages())
                <div class="text-center mt-2">
                    <span class="mx-2">
                        صفحه {{ $allRequests->currentPage() }} از {{ $allRequests->lastPage() }}
                    </span>
                    <a href="{{ $allRequests->previousPageUrl() }}" class="btn btn-outline-secondary btn-sm">قبلی</a>
                    @for ($i = 1; $i <= $allRequests->lastPage(); $i++)
                        <a href="{{ $allRequests->url($i) }}" class="btn btn-outline-secondary btn-sm"
                            @if ($allRequests->currentPage() == $i) style="background-color: #007bff" @endif>{{ $i }}</a>
                    @endfor
                    <a href="{{ $allRequests->nextPageUrl() }}" class="btn btn-outline-secondary btn-sm">بعدی</a>
                </div>
            @endif

        </div>
    </div>

@endsection

<script>
    function confirmDelete(url) {
        if (confirm("آیا مطمئن هستید؟")) {
            window.location.href = url
        }
        return null;
    }
</script>
