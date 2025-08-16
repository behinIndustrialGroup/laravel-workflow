@extends('behin-layouts.app')

@section('title', 'ویرایش مشتری')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header bg-success">ویرایش مشتری</div>
            <div class="card-body">
                <form method="POST" action="{{ route('simpleWorkflowReport.phonebook.update', $customer->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">{{ trans('fields.customer_name') }}</label>
                        <input type="text" name="fullname" class="form-control" value="{{ $customer->fullname }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ trans('fields.customer_mobile') }}</label>
                        <input type="text" name="mobile" class="form-control" value="{{ $customer->mobile }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ trans('fields.customer_nid') }}</label>
                        <input type="text" name="national_id" class="form-control" value="{{ $customer->national_id }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ trans('fields.customer_address') }}</label>
                        <textarea name="address" class="form-control">{{ $customer->address }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-success">ذخیره</button>
                </form>
            </div>
        </div>
    </div>
@endsection
