@extends('behin-layouts.app')

@section('title', 'افزودن مشتری')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header bg-success">افزودن مشتری</div>
            <div class="card-body">
                <form method="POST" action="{{ route('simpleWorkflowReport.phonebook.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">{{ trans('fields.customer_name') }}</label>
                        <input type="text" name="fullname" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ trans('fields.customer_mobile') }}</label>
                        <input type="text" name="mobile" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ trans('fields.customer_nid') }}</label>
                        <input type="text" name="national_id" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ trans('fields.customer_address') }}</label>
                        <textarea name="address" class="form-control"></textarea>
                    </div>
                    <button type="submit" class="btn btn-success">ذخیره</button>
                </form>
            </div>
        </div>
    </div>
@endsection
