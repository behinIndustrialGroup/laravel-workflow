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
        'sender_name' => 'فرستنده',
        'vehicle_plate' => 'شماره پلاک',
        'driver_name' => 'نام راننده',
        'notes' => 'یادداشت',
        'created_by' => 'ایجاد کننده',
        'in_or_out' => 'ورود یا خروج',
        'sender_or_receiver_name' => 'نام فرستنده یا گیرنده',
    ];

    /**
     * نمایش گزارش کالاهای ورودی.
     */
    public function index(Request $request)
    {
        $tableName = 'wf_entity_goods_in';
        $timezone = $this->getAppTimezone();

        $filters = [
            'search' => trim((string) $request->input('search', '')),
            'from_date' => trim((string) $request->input('from_date', '')),
            'from_date_alt' => trim((string) $request->input('from_date_alt', '')),
            'to_date' => trim((string) $request->input('to_date', '')),
            'to_date_alt' => trim((string) $request->input('to_date_alt', '')),
            'per_page' => (int) $request->input('per_page', 25),
        ];

        $perPageOptions = [10, 25, 50, 100];
        if (! in_array($filters['per_page'], $perPageOptions, true)) {
            $filters['per_page'] = 25;
        }
        if (! Schema::hasTable($tableName)) {
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

        $columns = collect(Schema::getColumnListing($tableName))->values();
        $columnMetadata = $this->buildColumnMetadata($columns);
        $dateColumns = $columnMetadata->filter(fn ($meta) => $meta['is_date'])->keys()->values();

        $selectedDateColumn = $request->input('date_column');
        if ($selectedDateColumn && ! $columns->contains($selectedDateColumn)) {
            $selectedDateColumn = null;
        }

        if (! $selectedDateColumn) {
            $selectedDateColumn = $dateColumns->first();

            if (! $selectedDateColumn && $columns->contains('created_at')) {
                $selectedDateColumn = 'created_at';
            }
        }

        if ($selectedDateColumn && ! $dateColumns->contains($selectedDateColumn)) {
            $dateColumns = $dateColumns->prepend($selectedDateColumn);
        }

        $query = DB::table($tableName);
        $validationErrors = [];

        $fromCarbon = $this->parseDate($filters['from_date'], $filters['from_date_alt'], $timezone);
        if ($fromCarbon) {
            $filters['from_date'] = Jalalian::fromCarbon($fromCarbon)->format('Y-m-d');

            if ($selectedDateColumn) {
                $query->whereDate($selectedDateColumn, '>=', $fromCarbon->format('Y-m-d'));
            }
        } elseif ($filters['from_date'] !== '' || $filters['from_date_alt'] !== '') {
            $validationErrors[] = 'فرمت تاریخ شروع معتبر نیست.';
        }

        $toCarbon = $this->parseDate($filters['to_date'], $filters['to_date_alt'], $timezone);
        if ($toCarbon) {
            $filters['to_date'] = Jalalian::fromCarbon($toCarbon)->format('Y-m-d');

            if ($selectedDateColumn) {
                $query->whereDate($selectedDateColumn, '<=', $toCarbon->format('Y-m-d'));
            }
        } elseif ($filters['to_date'] !== '' || $filters['to_date_alt'] !== '') {
            $validationErrors[] = 'فرمت تاریخ پایان معتبر نیست.';
        }

        if (($filters['from_date'] !== '' || $filters['from_date_alt'] !== '' || $filters['to_date'] !== '' || $filters['to_date_alt'] !== '')
            && ! $selectedDateColumn
        ) {
            $validationErrors[] = 'برای فیلتر تاریخ، یک ستون تاریخ معتبر انتخاب کنید.';
        }

        if ($filters['search'] !== '') {
            $query->where(function ($subQuery) use ($columns, $filters) {
                foreach ($columns as $column) {
                    $subQuery->orWhere($column, 'like', '%' . $filters['search'] . '%');
                }
            });
        }

        $metricsQuery = clone $query;

        if ($selectedDateColumn) {
            $query->orderByDesc($selectedDateColumn);
        } elseif ($columns->contains('id')) {
            $query->orderByDesc('id');
        }

        /** @var LengthAwarePaginator $paginator */
        $paginator = $query->paginate($filters['per_page'])->withQueryString();

        $collection = $paginator->getCollection()->map(function ($item) use ($columnMetadata, $timezone) {
            return $this->formatRecord((array) $item, $columnMetadata, $timezone);
        });

        $paginator->setCollection($collection);

        $totalRecords = (clone $metricsQuery)->count();
        $quantityColumn = $this->detectColumn($columns, ['quantity', 'qty', 'count', 'tedad', 'number_of_items']);
        $amountColumn = $this->detectColumn($columns, ['total_amount', 'total_price', 'amount', 'price', 'value', 'sum']);
        $supplierColumn = $this->detectColumn($columns, ['supplier', 'vendor', 'provider', 'seller']);

        $totalQuantity = $quantityColumn ? (clone $metricsQuery)->sum($quantityColumn) : null;
        $totalAmount = $amountColumn ? (clone $metricsQuery)->sum($amountColumn) : null;
        $uniqueSuppliers = $supplierColumn ? (clone $metricsQuery)->distinct()->count($supplierColumn) : null;

        $latestRecordValue = null;
        if ($selectedDateColumn) {
            $latestRecordValue = (clone $metricsQuery)->orderByDesc($selectedDateColumn)->value($selectedDateColumn);

            if ($latestRecordValue) {
                try {
                    $latestCarbon = Carbon::parse($latestRecordValue)->setTimezone($timezone);
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

    protected function buildColumnMetadata(Collection $columns): Collection
    {
        return $columns->mapWithKeys(function (string $column) {
            $lower = Str::lower($column);
            $isDate = Str::contains($lower, ['date', 'time']) || Str::endsWith($lower, '_at');
            $isNumeric = ! $isDate && Str::contains($lower, ['quantity', 'qty', 'count', 'amount', 'price', 'total', 'number']);

            return [
                $column => [
                    'key' => $column,
                    'label' => $this->customLabels[$column] ?? $this->makeLabel($column),
                    'is_date' => $isDate,
                    'is_numeric' => $isNumeric,
                ],
            ];
        });
    }

    protected function formatRecord(array $record, Collection $columnMetadata, string $timezone): array
    {
        foreach ($columnMetadata as $column => $meta) {
            if (! array_key_exists($column, $record)) {
                continue;
            }

            if ($meta['is_date'] && ! empty($record[$column])) {
                try {
                    $carbon = Carbon::parse($record[$column])->setTimezone($timezone);
                    $record[$column . '_gregorian'] = $carbon->format('Y-m-d H:i');
                    $record[$column] = Jalalian::fromCarbon($carbon)->format('Y-m-d H:i');
                } catch (\Throwable $exception) {
                    $record[$column . '_gregorian'] = $record[$column];
                }
            }

            if ($meta['is_numeric'] && $record[$column] !== null && $record[$column] !== '') {
                $record[$column] = is_numeric($record[$column])
                    ? $this->formatNumber((float) $record[$column])
                    : $record[$column];
            }
        }

        return $record;
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

    protected function parseDate(?string $jalaliValue, ?string $altValue, string $timezone): ?Carbon
    {
        if ($altValue !== null) {
            $altValue = trim($this->normalizeDigits($altValue));

            if ($altValue !== '') {
                if (is_numeric($altValue)) {
                    $timestamp = strlen($altValue) > 10
                        ? (int) round(((float) $altValue) / 1000)
                        : (int) $altValue;

                    try {
                        return Carbon::createFromTimestamp($timestamp, $timezone);
                    } catch (\Throwable $exception) {
                        // تلاش بعدی با تاریخ جلالی یا متنی
                    }
                }

                try {
                    return Carbon::parse($altValue, $timezone)->setTimezone($timezone);
                } catch (\Throwable $exception) {
                    // مقدار متنی معتبر نبود
                }
            }
        }

        if ($jalaliValue !== null) {
            $jalaliValue = trim($this->normalizeDigits($jalaliValue));

            if ($jalaliValue !== '') {
                foreach (['Y-m-d', 'Y/m/d'] as $format) {
                    try {
                        return Jalalian::fromFormat($format, $jalaliValue)->toCarbon()->setTimezone($timezone);
                    } catch (\Throwable $exception) {
                        continue;
                    }
                }

                try {
                    return Carbon::parse($jalaliValue, $timezone)->setTimezone($timezone);
                } catch (\Throwable $exception) {
                    // مقدار قابل تبدیل نبود
                }
            }
        }

        return null;
    }

    protected function makeLabel(string $column): string
    {
        $label = str_replace(['_', '-'], ' ', $column);
        $label = preg_replace('/\s+/', ' ', $label ?? '');

        return trim($label) !== '' ? trim($label) : $column;
    }

    protected function normalizeDigits(?string $value): string
    {
        if ($value === null) {
            return '';
        }

        $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $arabic = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
        $english = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

        $value = str_replace($persian, $english, $value);

        return str_replace($arabic, $english, $value);
    }
}

