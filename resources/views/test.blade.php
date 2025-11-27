@extends('behin-layouts.app')


@section('content')
@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif
@foreach($fins as $fin)
    <form action="{{ url('/test3') }}" method="POST" class="row align-items-center mb-3 border rounded p-3 bg-light">
        @csrf

        {{-- نام مشتری --}}
        <div class="col-md-4 mb-2 mb-md-0">
            <label class="form-label fw-bold">نام مشتری</label>
            <input 
                type="text" 
                name="customer_name" 
                class="form-control" 
                value="{{ $fin->case->getVariable('customer_workshop_or_ceo_name') }}" 
                readonly
            >
            <small>{{ $fin->case_number }}</small>
        </div>

        {{-- طرف حساب --}}
        <div class="col-md-5 mb-2 mb-md-0">
            <label class="form-label fw-bold">طرف حساب</label>
            <select name="counter_party_id" class="form-select select2">
                <option value="">انتخاب کنید...</option>
                @foreach($counterParties as $counterParty)
                    <option 
                        value="{{ $counterParty->id }}" 
                        {{ $counterParty->id == $fin->counter_party_id ? 'selected' : '' }}
                    >
                        {{ $counterParty->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- دکمه ثبت --}}
        <div class="col-md-3 text-end">
            <button type="submit" class="btn btn-primary mt-3 mt-md-0 w-100">
                ثبت
            </button>
        </div>
    </form>
@endforeach

<div>
    {{ $fins->links('pagination::bootstrap-4') }}
</div>

@endsection