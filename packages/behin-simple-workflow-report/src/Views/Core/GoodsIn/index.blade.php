@extends('behin-layouts.app')

@section('title', 'گزارش کالاهای ورودی')

@php
    use Morilog\Jalali\Jalalian;
    $todayJalali = Jalalian::now()->format('Y-m-d');

    $showInTable = [
        'created_at',
        'notes',
        'in_or_out',
        'sender_or_receiver_name',
        'sender_name',
        'vehicle_plate',
    ];
@endphp

@section('style')
    <style>
        .goods-in-report .filter-card {
            background: linear-gradient(135deg, rgba(33, 150, 243, 0.12), rgba(33, 203, 243, 0.08));
            border: none;
            border-radius: 16px;
        }

        .goods-in-report .filter-card .card-header {
            border-bottom: none;
            padding-bottom: 0;
            background: transparent;
        }

        .goods-in-report .filter-card .card-body {
            padding-top: 0.5rem;
        }

        .goods-in-report .metric-card {
            border: none;
            border-radius: 18px;
            color: #fff;
            overflow: hidden;
            position: relative;
            box-shadow: 0 12px 24px rgba(33, 150, 243, 0.12);
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }

        .goods-in-report .metric-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 18px 40px rgba(0, 0, 0, 0.18);
        }

        .goods-in-report .metric-icon {
            font-size: 42px;
            opacity: 0.2;
            position: absolute;
            inset-inline-end: 16px;
            inset-block-start: 16px;
        }

        .goods-in-report .metric-card .metric-value {
            font-size: 1.75rem;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .goods-in-report .metric-card .metric-label {
            font-size: 0.95rem;
            margin-top: 0.5rem;
            opacity: 0.9;
        }

        .goods-in-report .table-card {
            border-radius: 18px;
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }

        .goods-in-report .table thead th {
            border: none;
            background: #f6f8fb;
            font-weight: 600;
            color: #374151;
            padding-block: 1rem;
        }

        .goods-in-report .table tbody td {
            vertical-align: middle;
            border-top: 1px solid rgba(55, 65, 81, 0.08);
            padding-block: 0.85rem;
        }

        .goods-in-report .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: #6b7280;
        }

        .goods-in-report .empty-state .material-icons {
            font-size: 58px;
            color: #90a4ae;
            margin-bottom: 1rem;
        }

        .goods-in-report .filter-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(33, 150, 243, 0.08);
            color: #1976d2;
            padding: 0.35rem 0.9rem;
            border-radius: 999px;
            font-size: 0.9rem;
            margin-inline-start: 0.5rem;
            margin-bottom: 0.5rem;
        }

        .goods-in-report .form-label {
            font-weight: 600;
            color: #1f2937;
        }

        .goods-in-report .form-control,
        .goods-in-report .form-select {
            border-radius: 12px;
            border: 1px solid rgba(55, 65, 81, 0.12);
            padding: 0.6rem 0.85rem;
            box-shadow: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .goods-in-report .form-control:focus,
        .goods-in-report .form-select:focus {
            border-color: #2196f3;
            box-shadow: 0 0 0 0.2rem rgba(33, 150, 243, 0.15);
        }

        .goods-in-report .btn-primary {
            border-radius: 12px;
            padding-block: 0.6rem;
            font-weight: 600;
            background: linear-gradient(135deg, #2196f3, #1976d2);
            border: none;
        }

        .goods-in-report .btn-outline-secondary {
            border-radius: 12px;
        }

        @media (max-width: 768px) {
            .goods-in-report .metric-card {
                margin-bottom: 0.75rem;
            }
        }
    </style>
@endsection

@section('content')
    <div class="goods-in-report">
        <div class="row g-3 mb-3">
            <div class="col-12">
                <div class="card filter-card shadow-sm">
                    <div class="card-header d-flex flex-column flex-lg-row align-items-lg-center justify-content-between">
                        <div>
                            <h5 class="mb-1 fw-bold">مرور کالاهای ورودی یا خروجی</h5>
                            <p class="mb-0 text-muted">گزارش جامع بر اساس داده‌های ثبت شده در موجودیت Goods_in</p>
                        </div>
                        @if (!empty($filters['search']) || !empty($filters['from_date']) || !empty($filters['to_date']))
                            <div class="mt-3 mt-lg-0">
                                @if (!empty($filters['search']))
                                    <span class="filter-chip"><i class="material-icons" style="font-size: 18px;">search</i>
                                        جستجو: {{ $filters['search'] }}</span>
                                @endif
                                @if (!empty($filters['from_date']))
                                    <span class="filter-chip"><i class="material-icons"
                                            style="font-size: 18px;">calendar_month</i> از
                                        {{ $filters['from_date'] }}</span>
                                @endif
                                @if (!empty($filters['to_date']))
                                    <span class="filter-chip"><i class="material-icons" style="font-size: 18px;">event</i>
                                        تا {{ $filters['to_date'] }}</span>
                                @endif
                            </div>
                        @endif
                    </div>
                    <div class="card-body">
                        <form id="goods-in-filter-form" method="GET" class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label for="search" class="form-label">جستجوی آزاد</label>
                                <input type="text" name="search" id="search" class="form-control" placeholder=""
                                    value="{{ $filters['search'] }}">
                            </div>
                            <div class="col-md-2">
                                <label for="from_date" class="form-label">از تاریخ</label>
                                <input type="text" name="from_date" id="from_date" class="form-control persian-date"
                                    placeholder="" value="{{ $filters['from_date'] }}">
                                <input type="hidden" name="from_date_alt" id="from_date_alt"
                                    value="{{ $filters['from_date_alt'] }}">
                            </div>
                            <div class="col-md-2">
                                <label for="to_date" class="form-label">تا تاریخ</label>
                                <input type="text" name="to_date" id="to_date" class="form-control persian-date"
                                    placeholder="" value="{{ $filters['to_date'] }}">
                                <input type="hidden" name="to_date_alt" id="to_date_alt"
                                    value="{{ $filters['to_date_alt'] }}">
                            </div>
                            {{-- <div class="col-md-2">
                                <label for="date_column" class="form-label">ستون تاریخ</label> --}}
                            <input type="hidden" name="date_column" id="date_column" value="created_at">
                            {{-- <select name="date_column" id="date_column" class="form-select">
                                    <option value="">انتخاب ستون</option>
                                    @foreach ($dateColumns as $column)
                                        <option value="{{ $column }}"
                                            {{ $selectedDateColumn === $column ? 'selected' : '' }}>
                                            {{ $columnMetadata[$column]['label'] ?? $column }}
                                        </option>
                                    @endforeach
                                </select>
                            </div> --}}
                            <div class="col-md-2">
                                <label for="per_page" class="form-label">تعداد در هر صفحه</label>
                                <select name="per_page" id="per_page" class="form-select">
                                    @foreach ($perPageOptions as $option)
                                        <option value="{{ $option }}"
                                            {{ (int) $filters['per_page'] === $option ? 'selected' : '' }}>
                                            {{ $option }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 col-lg-1 d-grid">
                                <button type="submit" class="btn btn-primary">فیلتر</button>
                            </div>
                            <div class="col-md-3 col-lg-1 d-grid">
                                <a href="{{ route('simpleWorkflowReport.goods-in.index') }}"
                                    class="btn btn-outline-secondary">پاک‌سازی</a>
                            </div>
                        </form>
                        @if (!empty($validationErrors))
                            <div class="alert alert-warning mt-3">
                                @foreach ($validationErrors as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @if ($tableExists)
            {{-- <div class="row g-3 mb-4">
                @foreach ($metrics as $metric)
                    <div class="col-12 col-md-6 col-lg-3">
                        <div class="card metric-card" style="background: {{ $metric['background'] }};">
                            <div class="card-body">
                                <span class="material-icons metric-icon">{{ $metric['icon'] }}</span>
                                <div class="metric-value">{{ $metric['value'] }}</div>
                                <div class="metric-label">{{ $metric['label'] }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div> --}}

            <div class="card table-card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 70px;">#</th>
                                    @foreach ($showInTable as $column)
                                        @if (isset($columnMetadata[$column]))
                                            <th>{{ $columnMetadata[$column]['label'] }}</th>
                                        @endif
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($paginator as $index => $record)
                                    <tr>
                                        <td>{{ $paginator->firstItem() ? $paginator->firstItem() + $index : $index + 1 }}
                                        </td>
                                        @foreach ($showInTable as $column)
                                            @if (isset($columnMetadata[$column]))
                                                @php
                                                    $meta = $columnMetadata[$column];
                                                    $value = $record[$column] ?? null;
                                                    $display = $value === null || $value === '' ? '—' : $value;
                                                    if ($column == 'created_by') {
                                                        $display = getUserInfo($value)->name;
                                                    }
                                                    $tooltip = $meta['is_date']
                                                        ? $record[$column . '_gregorian'] ?? null
                                                        : null;
                                                @endphp
                                                <td
                                                    @if ($tooltip) data-toggle="tooltip" title="{{ $tooltip }}" @endif>
                                                    {{ $display }}
                                                </td>
                                            @endif
                                        @endforeach
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ $columnMetadata->count() + 1 }}">
                                            <div class="empty-state">
                                                <i class="material-icons">inventory_2</i>
                                                <p class="mb-1">هیچ داده‌ای مطابق فیلترهای انتخابی یافت نشد.</p>
                                                <small>فیلترها را تغییر دهید یا پاک‌سازی کنید.</small>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white">
                    {{ $paginator->links() }}
                </div>
            </div>
        @else
            <div class="empty-state">
                <i class="material-icons">warning</i>
                <p class="mb-1">جدول اطلاعات کالاهای ورودی یافت نشد.</p>
                <small>لطفاً با مدیر سیستم تماس بگیرید.</small>
            </div>
        @endif
    </div>
@endsection

@section('script')
    <script>
        initial_view();
        $(function() {
            $("#from_date").persianDatepicker({
                viewMode: 'day',
                initialValue: false,
                format: 'YYYY-MM-DD',
                initialValueType: 'persian',
                altField: '#from_date_alt',
                calendar: {
                    persian: {
                        leapYearMode: 'astronomical',
                        locale: 'fa'
                    }
                }
            });

            $("#to_date").persianDatepicker({
                viewMode: 'day',
                initialValue: false,
                format: 'YYYY-MM-DD',
                initialValueType: 'persian',
                altField: '#to_date_alt',
                calendar: {
                    persian: {
                        leapYearMode: 'astronomical',
                        locale: 'fa'
                    }
                }
            });

            $('#per_page').on('change', function() {
                document.getElementById('goods-in-filter-form').submit();
            });

            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@endsection
