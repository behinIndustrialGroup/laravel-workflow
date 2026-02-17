@extends('behin-layouts.app')

@section('title', 'درخواست کالای مصرفی از انبار')

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">درخواست دریافت کالای مصرفی از انبار</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('simpleWorkflowReport.consumable-parts.store') }}" method="POST"
                class="row g-3 align-items-end">
                @csrf
                <input type="hidden" name="viewModelId" value="608bf9ee-ad4e-4931-80ae-d27ca8537dad">

                <div class="col-md-5">
                    <label class="form-label">نام کالا</label>
                    <input type="text" name="product_name" class="form-control" placeholder="مثلاً کابل شبکه">
                </div>

                <div class="col-md-4">
                    <label class="form-label">تعداد موردنیاز</label>
                    <input type="number" name="requested_quantity" class="form-control" placeholder="مثلاً 10">
                </div>

                <div class="col-md-3 d-grid">
                    <button type="submit" class="btn btn-primary">ثبت درخواست</button>
                </div>
            </form>
        </div>

    </div>

    <div class="card">
        <div class="card-header">
            درخواست های من
        </div>
        <div class="card-body">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>تاریخ درخواست</th>
                        <th>قطعه</th>
                        <th>تعداد مورد نیاز</th>
                        <th>وضعیت</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($userConsumableParts as $consumablePart)
                        <tr>
                            <td dir="ltr">{{ toJalali($consumablePart->created_at) }}</td>
                            <td>{{ $consumablePart->product_name }}</td>
                            <td>{{ $consumablePart->requested_quantity }}</td>
                            <td>{{ $consumablePart->consumable_part_status }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>

@endsection
