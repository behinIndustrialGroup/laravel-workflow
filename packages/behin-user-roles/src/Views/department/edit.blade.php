@extends('behin-layouts.app')

@section('title')
    ویرایش دپارتمان
@endsection

@section('content')
    <div class="container">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        <div class="card p-2">
            <a href="{{ route('department.index') }}" class="btn btn-primary">بازگشت به لیست دپارتمان</a>
            <div class="row mb-3">
                
            </div>
            

        </div>
    </div>
@endsection
