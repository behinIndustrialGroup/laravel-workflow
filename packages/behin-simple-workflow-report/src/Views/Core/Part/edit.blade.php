@extends('behin-layouts.app')

@section('title', 'ویرایش قطعه ' . $part->name)
@php
    $backUrl = route('simpleWorkflowReport.external-internal.show', $part->case_number);
@endphp

@section('content')

<div class="card">
    <div class="card-header">
        ویرایش قطعه: {{ $part->name }}
        پرونده: {{ $part->case_number }}
    </div>
    <div class="card-body">
        <form action="{{ route('simpleWorkflowReport.parts.update', $part->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="">نام</label>
                <input type="text" name="name" id="" class="form-control" value="{{ $part->name }}">
            </div>
            <div class="form-group">
                <label for="">سریال مپا</label>
                <input type="text" name="mapa_serial" id="" class="form-control" value="{{ $part->mapa_serial }}">
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-sm btn-primary">ویرایش</button>
            </div>
        </form>
    </div>
</div>

@endsection