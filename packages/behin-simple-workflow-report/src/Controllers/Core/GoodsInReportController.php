<?php

namespace Behin\SimpleWorkflowReport\Controllers\Core;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Morilog\Jalali\Jalalian;

class GoodsInReportController extends Controller
{
    /**
     * Custom labels for known columns.
     *
     * @var array<string, string>
     */
    protected array $customLabels = [
        'id' => 'شناسه',
        'created_at' => 'تاریخ ثبت',
        'updated_at' => 'آخرین ویرایش',
        'goods_name' => 'نام کالا',
        'product_name' => 'نام کالا',
        'item_name' => 'نام کالا',
        'supplier' => 'تأمین‌کننده',
        'vendor' => 'تأمین‌کننده',
        'provider' => 'تأمین‌کننده',
        'tracking_code' => 'کد رهگیری',
        'receipt_number' => 'شماره رسید',
        'receive_date' => 'تاریخ ورود',
        'arrival_date' => 'تاریخ ورود',
        'quantity' => 'تعداد',
        'qty' => 'تعداد',
        'count' => 'تعداد',
        'unit' => 'واحد',
        'description' => 'توضیحات',
        'total_amount' => 'مبلغ کل',
        'total_price' => 'مبلغ کل',
        'amount' => 'مبلغ',
        'price' => 'قیمت',
        'warehouse' => 'انبار',
    ];

    /**
     * نمایش گزارش کالاهای ورودی.
     */
    public function index(Request $request)
    {
        $tableName = 'wf_entity_goods_in';
        $appTimezone = $this->getAppTimezone();

        $filters = [
            'search' => trim((string) $request->input('search', '')),
            'from_date' => $request->input('from_date'),
            'to_date' => $request->input('to_date'),
            'per_page' => (int) $request->input('per_page', 25),
        ];

        $perPageOptions = [10, 25, 50, 100];
        if (! in_array($filters['per_page'], $perPageOptions, true)) {
            $filters['per_page'] = 25;
        }

        $tableExists = Schema::hasTable($tableName);
        if (! $tableExists) {
            return view('SimpleWorkflowReportView::Core.GoodsIn.index', [
                'tableExists' => false,
                'records' => collect(),
                'paginator' => null,
                'columns' => collect(),
                'columnMetadata' => collect(),
                'metrics' => collect(),
                'filters' => $filters,
                'dateColumns' => collect(),
                'selectedDateColumn' => null,
                'perPageOptions' => $perPageOptions,
                'validationErrors' => ['جدول اطلاعات کالاهای ورودی در دسترس نیست.'],
            ]);
        }

        $columns = collect(Schema::getColumnListing($tableName));

        $columnMetadata = $columns->mapWithKeys(function ($column) {
            $lower = Str::lower($column);
            $isDate = Str::contains($lower, ['date']) || Str::endsWith($lower, '_at');
            $isNumeric = Str::contains($lower, ['quantity', 'qty', 'count', 'amount', 'price', 'total', 'number', 'qty'])
                && ! $isDate;

            return [
                $column => [
                    'key' => $column,
                    'label' => $this->customLabels[$column] ?? $this->makeLabel($column),
                    'is_date' => $isDate,
                    'is_numeric' => $isNumeric,
                ],
            ];
        });

        $dateColumns = $columnMetadata->filter(fn ($meta) => $meta['is_date'])->keys()->values();
        $selectedDateColumn = $request->input('date_column');
        if ($selectedDateColumn && ! $dateColumns->contains($selectedDateColumn)) {
            $selectedDateColumn = null;
        }

        if (! $selectedDateColumn && $dateColumns->contains('receive_date')) {
            $selectedDateColumn = 'receive_date';
        }
        if (! $selectedDateColumn && $dateColumns->contains('arrival_date')) {
            $selectedDateColumn = 'arrival_date';
        }
        if (! $selectedDateColumn && $dateColumns->contains('created_at')) {
            $selectedDateColumn = 'created_at';
        }
        if (! $selectedDateColumn && $dateColumns->isNotEmpty()) {
            $selectedDateColumn = $dateColumns->first();
        }

        $query = DB::table($tableName);
        $validationErrors = [];
        $fromCarbon = null;
        $toCarbon = null;

        if ($selectedDateColumn) {
            if (! empty($filters['from_date'])) {
                $fromCarbon = $this->parseJalaliDate($filters['from_date'], $appTimezone);

                if ($fromCarbon) {
                    $query->whereDate($selectedDateColumn, '>=', $fromCarbon->format('Y-m-d'));
                } else {
                    $validationErrors[] = 'فرمت تاریخ شروع معتبر نیست.';
                }
            }

            if (! empty($filters['to_date'])) {
                $toCarbon = $this->parseJalaliDate($filters['to_date'], $appTimezone);

                if ($toCarbon) {
                    $query->whereDate($selectedDateColumn, '<=', $toCarbon->format('Y-m-d'));
                } else {
                    $validationErrors[] = 'فرمت تاریخ پایان معتبر نیست.';
                }
            }
        }

        if ($filters['search'] !== '') {
            $query->where(function ($subQuery) use ($columns, $filters) {
                foreach ($columns as $column) {
                    $subQuery->orWhere($column, 'like', '%' . $filters['search'] . '%');
                }
            });
        }

        $baseQuery = clone $query;

        if ($selectedDateColumn) {
            $query->orderByDesc($selectedDateColumn);
        } elseif ($columns->contains('id')) {
            $query->orderByDesc('id');
        }

        /** @var LengthAwarePaginator $paginator */
        $paginator = $query->paginate($filters['per_page'])->withQueryString();

        $collection = $paginator->getCollection()->map(function ($item) use ($columnMetadata, $appTimezone) {
            $record = (array) $item;

            foreach ($columnMetadata as $column => $meta) {
                if (! array_key_exists($column, $record)) {
                    continue;
                }

                if ($meta['is_date'] && ! empty($record[$column])) {
                    try {
                        $carbon = Carbon::parse($record[$column])->setTimezone($appTimezone);
                        $record[$column . '_gregorian'] = $carbon->format('Y-m-d H:i');
                        $record[$column] = Jalalian::fromCarbon($carbon)->format('Y-m-d H:i');
                    } catch (\Throwable $exception) {
                        // اگر تاریخ قابل تبدیل نبود، مقدار اصلی را نگه می‌داریم
                        $record[$column . '_gregorian'] = $record[$column];
                    }
                }

                if ($meta['is_numeric'] && $record[$column] !== null) {
                    $record[$column] = is_numeric($record[$column])
                        ? $this->formatNumber((float) $record[$column])
                        : $record[$column];
                }
            }

            return $record;
        });

        $paginator->setCollection($collection);

        $totalRecords = (clone $baseQuery)->count();

        $quantityColumn = $this->detectColumn($columns, ['quantity', 'qty', 'count', 'tedad', 'number_of_items']);
        $amountColumn = $this->detectColumn($columns, ['total_amount', 'total_price', 'amount', 'price', 'value', 'sum']);
        $supplierColumn = $this->detectColumn($columns, ['supplier', 'vendor', 'provider', 'seller']);

        $totalQuantity = null;
        $totalAmount = null;
        $uniqueSuppliers = null;
        $latestRecordValue = null;

        if ($quantityColumn) {
            $totalQuantity = (clone $baseQuery)->sum($quantityColumn);
        }

        if ($amountColumn) {
            $totalAmount = (clone $baseQuery)->sum($amountColumn);
        }

        if ($supplierColumn) {
            $uniqueSuppliers = (clone $baseQuery)->distinct()->count($supplierColumn);
        }

        if ($selectedDateColumn) {
            $latestRecordValue = (clone $baseQuery)->orderByDesc($selectedDateColumn)->value($selectedDateColumn);
            if ($latestRecordValue) {
                try {
                    $latestCarbon = Carbon::parse($latestRecordValue)->setTimezone($appTimezone);
                    $latestRecordValue = Jalalian::fromCarbon($latestCarbon)->format('Y-m-d H:i');
                } catch (\Throwable $exception) {
                    // اگر تبدیل ممکن نبود همان مقدار اصلی نمایش داده می‌شود
                }
            }
        }

        $metrics = collect([
            [
                'label' => 'تعداد کل رکوردها',
                'value' => $this->formatNumber($totalRecords),
                'icon' => 'inventory_2',
                'background' => 'linear-gradient(135deg, #42a5f5, #478ed1)',
            ],
        ]);

        if ($totalQuantity !== null) {
            $metrics->push([
                'label' => 'مجموع تعداد اقلام',
                'value' => $this->formatNumber($totalQuantity),
                'icon' => 'countertops',
                'background' => 'linear-gradient(135deg, #66bb6a, #43a047)',
            ]);
        }

        if ($totalAmount !== null) {
            $metrics->push([
                'label' => 'ارزش کل ورود کالا',
                'value' => $this->formatNumber($totalAmount),
                'icon' => 'payments',
                'background' => 'linear-gradient(135deg, #ffa726, #fb8c00)',
            ]);
        }

        if ($uniqueSuppliers !== null) {
            $metrics->push([
                'label' => 'تعداد تأمین‌کنندگان یکتا',
                'value' => $this->formatNumber($uniqueSuppliers),
                'icon' => 'handshake',
                'background' => 'linear-gradient(135deg, #ab47bc, #8e24aa)',
            ]);
        }

        if ($latestRecordValue) {
            $metrics->push([
                'label' => 'آخرین ثبت ورود کالا',
                'value' => $latestRecordValue,
                'icon' => 'schedule',
                'background' => 'linear-gradient(135deg, #26c6da, #00acc1)',
            ]);
        }

        return view('SimpleWorkflowReportView::Core.GoodsIn.index', [
            'tableExists' => true,
            'records' => $paginator->items(),
            'paginator' => $paginator,
            'columns' => $columns,
            'columnMetadata' => $columnMetadata,
            'metrics' => $metrics,
            'filters' => $filters,
            'dateColumns' => $dateColumns,
            'selectedDateColumn' => $selectedDateColumn,
            'perPageOptions' => $perPageOptions,
            'validationErrors' => $validationErrors,
        ]);
    }

    protected function makeLabel(string $column): string
    {
        $clean = str_replace(['_', '-'], ' ', $column);
        $clean = preg_replace('/\s+/', ' ', $clean ?? '');

        return trim($clean) !== '' ? trim($clean) : $column;
    }

    protected function detectColumn(Collection $columns, array $needles): ?string
    {
        foreach ($columns as $column) {
            $lower = Str::lower($column);
            if (Str::contains($lower, $needles)) {
                return $column;
            }
        }

        return null;
    }

    protected function formatNumber(float|int $value): string
    {
        return number_format($value, 0, '.', ',');
    }

    protected function getAppTimezone(): string
    {
        return config('app.timezone', 'UTC') ?: 'UTC';
    }

    protected function parseJalaliDate(?string $value, string $appTimezone): ?Carbon
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        $value = trim($value);
        $formats = [
            'Y-m-d',
            'Y/m/d',
            'Y-m-d H:i',
            'Y/m/d H:i',
            'Y-m-d\TH:i',
            'Y/m/d\TH:i',
        ];

        foreach ($formats as $format) {
            try {
                $jalali = Jalalian::fromFormat($format, $value);
                return $jalali->toCarbon()->setTimezone($appTimezone);
            } catch (\Throwable $exception) {
                continue;
            }
        }

        try {
            $jalali = Jalalian::forge($value);

            if ($jalali) {
                return $jalali->toCarbon()->setTimezone($appTimezone);
            }
        } catch (\Throwable $exception) {
            // مقدار قابل تبدیل نیست
        }

        return null;
    }
}
