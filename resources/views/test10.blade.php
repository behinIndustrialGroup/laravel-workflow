@extends('behin-layouts.app')


@section('content')
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    <table class="table table-stripped">
        @foreach ($fins as $fin)
            @php
                $is_ok = str_contains($fin->fix_cost_type, 'نقدی') && $fin->cost == $fin->payment;
                $formId = 'form_' . $fin->id;
            @endphp

            <form id="{{ $formId }}" action="{{ url('test11') }}" method="POST"
                class="row align-items-center mb-3 border rounded p-3 bg-light">

                @csrf

                <tr class="{{ $is_ok ? 'bg-success' : '' }}">
                    <input type="hidden" name="id" value="{{ $fin->id }}">
                    <td>{{ $fin->case_number }}</td>
                    <td>
                        @if(!isset($fin->counterparty->name))
{{ $fin->case_number }} - {{ $fin->counterparty }}
@php
    exit();
@endphp
                        @endif
                        {{ $fin->counterparty->name }}
                    </td>
                    <td>{{ $fin->cost }}</td>
                    <td>{{ $fin->payment }}</td>
                    <td>{{ $fin->fix_cost_type }}</td>
                    <td class="col-md-3 text-end">
                        <button type="submit" class="btn btn-primary mt-3 mt-md-0 w-100">
                            ثبت
                        </button>
                    </td>
                </tr>
            </form>

            {{-- اگر شرط برقرار بود، فرم خودکار ارسال می‌شود --}}
            @if ($is_ok)
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        document.getElementById("{{ $formId }}").submit();
                    });
                </script>
            @endif
        @endforeach

    </table>
    <div>
        {{ $fins->links('pagination::bootstrap-4') }}
    </div>
@endsection
