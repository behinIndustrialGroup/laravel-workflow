@extends('behin-layouts.app')

@section('title', 'گزارش کالاهای امانی')

@section('content')

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('warning'))
        <div class="alert alert-warning">{{ session('warning') }}</div>
    @endif

    <div class="card row">
        <div class="card-body row">
            <form action="{{ route('simpleWorkflowReport.borrow-requests.report') }}" method="GET" id="search-form"
                class="row">
                <div class="col-sm-4 mb-1">
                    <input type="text" class="form-control" name="item_name" value="{{ request('item_name') }}"
                        placeholder="نام کالا">
                </div>
                <div class="col-sm-3 mb-1">
                    <select name="requester_id" id="" class="form-control select2">
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
                        <option value="delivered_at_alt"
                            {{ request('date_column') == 'delivered_at_alt' ? 'selected' : '' }}>تاریخ تحویل</option>
                        <option value="expected_return_date_alt"
                            {{ request('date_column') == 'expected_return_date_alt' ? 'selected' : '' }}>تاریخ بازگشت مورد
                            انتظار</option>
                        <option value="actual_return_date"
                            {{ request('date_column') == 'actual_return_date' ? 'selected' : '' }}>تاریخ بازگشت</option>
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
                        <th>کالا</th>
                        <th>تعداد</th>
                        <th>درخواست کننده</th>
                        <th>وضعیت</th>
                        <th>تاریخ درخواست</th>
                        <th>تاریخ تحویل</th>
                        <th>تاریخ بازگشت مورد انتظار</th>
                        <th>تاریخ بازگشت</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($allRequests as $request)
                        <tr>
                            <td>
                                <div>{{ $request->item_name }}</div>
                                @if ($request->item_id)
                                    <div class="text-muted small">شناسه: {{ $request->item_id }}</div>
                                @endif
                            </td>
                            <td>{{ $request->quantity }}</td>
                            <td>{{ getUserInfo($request->requester_id)->name }}</td>
                            <td>
                                <span class="badge" style="background-color: {{ $statuses[$request->status]['color'] }}">{{ $request->status_label }}</span>
                                <div class="text-muted small">{{ $statuses[$request->status]['description'] ?? '' }}
                                </div>
                            </td>
                            <td>
                                @if ($request->created_at)
                                    {{ toJalali($request->created_at)->format('Y-m-d') }}
                                @else
                                    ـ
                                @endif
                            </td>
                            <td>
                                @if ($request->delivered_at_alt)
                                    {{ toJalali((int) $request->delivered_at_alt)->format('Y-m-d') }}
                                @else
                                    ـ
                                @endif
                            </td>
                            <td>
                                @if ($request->expected_return_date_alt)
                                    {{ toJalali((int) $request->expected_return_date_alt)->format('Y-m-d') }}
                                @else
                                    ـ
                                @endif
                            </td>
                            <td>{{ $request->actual_return_date }}</td>
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
                        <a href="{{ $allRequests->url($i) }}"
                            class="btn btn-outline-secondary btn-sm" @if ($allRequests->currentPage() == $i) style="background-color: #007bff" @endif>{{ $i }}</a>
                    @endfor
                    <a href="{{ $allRequests->nextPageUrl() }}" class="btn btn-outline-secondary btn-sm">بعدی</a>
                </div>
            @endif

        </div>
    </div>

@endsection
