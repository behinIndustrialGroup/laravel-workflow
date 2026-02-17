@extends('behin-layouts.app')

@section('title', 'دفتر معین')

@php
    use Behin\SimpleWorkflow\Controllers\Core\ViewModelController;
    $viewModelId = '7735ccdf-d5f2-4c38-8d02-ab7c139e5015';
    $viewModel = ViewModelController::getById($viewModelId);
    $viewModelUpdateForm = $viewModel->update_form;
    $viewModelApikey = $viewModel->api_key;
    $viewModelCreateNewForm = $viewModel->create_form;

    $addTasvieViewModelId = '6f34acb3-b60e-4a4a-99a5-4d3f8467ca6a';
    $addTasvieViewModel = ViewModelController::getById($addTasvieViewModelId);
    $addTasvieViewModelUpdateForm = $addTasvieViewModel->update_form;
    $addTasvieViewModelApikey = $addTasvieViewModel->api_key;
    $addTasvieViewModelCreateNewForm = $addTasvieViewModel->create_form;
@endphp


@section('content')
    @if (session()->has('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    @if (isset($errors) && $errors->any())
        @foreach ($errors->all() as $error)
            <div class="alert alert-danger">
                {{ $error }}
            </div>
        @endforeach
    @endif
    <div class="card">
        <div class="card-header">
            راهنما<br>
            افزودن طلبکاری: یعنی مشتری از مدار پرداز طلب دارد. مانند مشتری به مدار پرداز کالایی فروخته، خدماتی ارائه داده یا مبلغی به مدار پرداز پرداخت کرده<br>
            افزودن بدهکاری: یعنی مشتری به مدار پرداز بدهکار است. مانند مشتری از مدارپرداز خدماتی دریافت کرده یا کالایی از مدار پرداز خریده یا مدارپرداز مبلغی به مشتری پرداخت کرده است.
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-header">
            <button class="btn btn-sm btn-success" onclick="showAddNewCredit()">افزودن
                طلبکار <br>(مدارپرداز به مشتری بدهکار است)
            </button>
            <button class="btn btn-sm btn-warning" onclick="showAddNewDebit()">افزودن
                بدهکاری <br>(مدارپرداز از مشتری طلب دارد)
            </button>
            <a href="{{ route('simpleWorkflowReport.counter-party.create') }}" class="btn btn-sm btn-primary">
                افزودن طرف حساب جدید
            </a>


            {{-- <button class="btn btn-sm btn-primary"
                onclick="open_view_model_create_new_form(`{{ $addTasvieViewModelCreateNewForm }}`, `{{ $addTasvieViewModelId }}`, `{{ $addTasvieViewModelApikey }}`)">افزودن
                سند</button> --}}
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-body">
            <form action="{{ route('simpleWorkflowReport.financial-transactions.index') }}" method="GET"
                class="row align-items-end">
                <div class="col-sm-6 col-md-4 mb-2">
                    <label class="form-label">جستجو بر اساس شماره پرونده</label>
                    <input type="text" class="form-control" name="case_number" value="{{ $caseNumber ?? '' }}"
                        placeholder="شماره پرونده را وارد کنید">
                </div>
                <input type="hidden" name="filter" value="{{ $filter }}">
                <div class="col-auto mb-2">
                    <button type="submit" class="btn btn-primary">جستجو</button>
                </div>
                @if (filled($caseNumber ?? null))
                    <div class="col-auto mb-2">
                        <a href="{{ route('simpleWorkflowReport.financial-transactions.index', ['filter' => $filter]) }}"
                            class="btn btn-outline-secondary">حذف جستجو</a>
                    </div>
                @endif
            </form>
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-body d-flex gap-2 flex-wrap">
            @php
                $caseNumberQuery = [];
                if (filled($caseNumber ?? null)) {
                    $caseNumberQuery['case_number'] = $caseNumber;
                }
            @endphp
            <a href="{{ route('simpleWorkflowReport.financial-transactions.index', array_merge(['filter' => 'negative'], $caseNumberQuery)) }}"
                class="btn btn-sm {{ ($filter ?? 'negative') === 'negative' ? 'btn-primary' : 'btn-outline-primary' }}">
                نمایش بدهکاران
            </a>
            <a href="{{ route('simpleWorkflowReport.financial-transactions.index', array_merge(['filter' => 'all'], $caseNumberQuery)) }}"
                class="btn btn-sm {{ ($filter ?? 'negative') === 'all' ? 'btn-primary' : 'btn-outline-primary' }}">
                نمایش همه طرف حساب‌ها
            </a>
            <a href="{{ route('simpleWorkflowReport.financial-transactions.index', array_merge(['filter' => 'positive'], $caseNumberQuery)) }}"
                class="btn btn-sm {{ ($filter ?? 'negative') === 'positive' ? 'btn-primary' : 'btn-outline-primary' }}">
                نمایش طلبکاران
            </a>
        </div>
    </div>
    @if (filled($caseNumber ?? null))
        <div class="alert alert-info">
            نمایش نتایج برای پرونده شماره <span class="font-weight-bold">{{ $caseNumber }}</span>
        </div>
    @endif
    <div class="card table-responsive">
        <div class="card-header bg-secondary text-center">
            <h3 class="card-title">دفتر معین</h3>
        </div>
        <div class="card-body">
            <table class="table table-bordered" id="cheque-list">
                <thead>
                    <tr>
                        <th>طرف حساب</th>
                        <th>مانده حساب</th>
                        <th>اقدامات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($creditors as $creditor)
                        <tr @if ($creditor->total_amount < 0) style="background: #f56c6c" @endif
                            @if ($creditor->total_amount > 0) class="bg-success" @endif>
                            <td>{{ $creditor->counterparty()->name ?? '' }}</td>
                            <td dir="ltr">{{ number_format($creditor->total_amount) }}</td>
                            <td>
                                <button class="btn btn-sm btn-primary"
                                    onclick="showDetails(`{{ $creditor->counterparty_id }}`)">جزئیات بیشتر</button>
                                <button class="btn btn-sm btn-success"
                                    onclick="showAddCredit(`{{ $creditor->counterparty_id }}`)">افزودن طلبکاری
                                    <button class="btn btn-sm btn-warning"
                                        onclick="showAddDebit(`{{ $creditor->counterparty_id }}`)">افزودن بدهکاری</button>
                                    @if ($creditor->counterparty_id)
                                        <a href="{{ route('simpleWorkflowReport.financial-transactions.export', $creditor->counterparty_id) }}"
                                            class="btn btn-sm btn-info"
                                            download="گزارش تراکنش های {{ $creditor->counterparty()->name ?? '' }}.xlsx">اکسل</a>
                                    @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th style="text-align:right">جمع:</th>
                        <th></th> <!-- اینجا جمع ستون مانده حساب -->
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="card-body">
            مجموع کل تراکنش های بدهکاری: {{ number_format($balance->total_debit) }} <br>
            مجموع کل تراکنش های بستانکاری: {{ number_format($balance->total_credit) }} <br>
            بدهکاری واقعی
            (مبلغی که مدارپرداز از طرف حساب های خود طلب دارد):
            : {{ number_format($totalDebit) }} <br>
            بستانکاری واقعی
            (مبلغی که مدارپرداز به طرف حساب های خود بدهکار است):
            : {{ number_format($totalCredit) }} <br>
            مانده کل: {{ number_format($balance->total_amount) }}
            @if ($balance->total_amount < 0)
                <span class="text-danger"> ریال مدار پرداز طلب دارد</span>
            @elseif($balance->total_amount > 0)
                <span class="text-success">ریال مدار پرداز بدهکار است</span>
            @endif
        </div>
    </div>
@endsection

@section('script')
    <script>
        $('#cheque-list').DataTable({
            pageLength: 25,
            language: {
                url: "https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Persian.json"
            },
            footerCallback: function(row, data, start, end, display) {

                var api = this.api();

                // تبدیل مقدار به عدد
                var intVal = function(i) {
                    return typeof i === 'string' ?
                        i.replace(/[\$,٬,]/g, '') * 1 :
                        typeof i === 'number' ?
                        i :
                        0;
                };

                /** =========================
                 *  داده‌ها
                 *  ========================= */
                var totalDebit = 0;
                var totalCredit = 0;
                var pageDebit = 0;
                var pageCredit = 0;

                // کل جدول (با فیلتر)
                api.column(1, {
                    search: 'applied'
                }).data().each(function(value) {
                    var amount = intVal(value);
                    if (amount < 0) {
                        totalDebit += Math.abs(amount);
                    } else {
                        totalCredit += amount;
                    }
                });

                // صفحه جاری
                api.column(1, {
                    page: 'current'
                }).data().each(function(value) {
                    var amount = intVal(value);
                    if (amount < 0) {
                        pageDebit += Math.abs(amount);
                    } else {
                        pageCredit += amount;
                    }
                });

                var pageBalance = pageCredit - pageDebit;
                var totalBalance = totalCredit - totalDebit;

                /** =========================
                 *  نمایش در فوتر
                 *  ========================= */
                $(api.column(1).footer()).html(`
                    <div style="line-height:1.8">
                        <strong>جمع صفحه</strong><br>
                        بدهکاری: <span class="text-danger">${pageDebit.toLocaleString()}</span><br>
                        بستانکاری: <span class="text-success">${pageCredit.toLocaleString()}</span><br>
                        مانده: <strong>${pageBalance.toLocaleString()}</strong>
                        
                    </div>
                `);
            }

        });



        function showDetails(counterparty) {
            var fd = new FormData();
            fd.append('counterparty', counterparty);
            var url = "{{ route('simpleWorkflowReport.financial-transactions.show', 'counterparty') }}";
            url = url.replace('counterparty', counterparty);
            open_admin_modal(url, 'جزئیات بیشتر',true);
        }

        function showAddCredit(counterparty) {
            var fd = new FormData();
            fd.append('counterparty', counterparty);
            var url = "{{ route('simpleWorkflowReport.financial-transactions.showAddCredit', 'counterparty') }}";
            url = url.replace('counterparty', counterparty);
            open_admin_modal(url, 'افزودن بستانکاری');
        }

        function showAddNewCredit(counterparty = null) {
            var fd = new FormData();
            fd.append('counterparty', counterparty);
            var url = "{{ route('simpleWorkflowReport.financial-transactions.showAddCredit', 'counterparty') }}";
            url = url.replace('counterparty', counterparty);
            open_admin_modal(url, 'افزودن بستانکاری');
        }

        function showAddDebit(counterparty) {
            var fd = new FormData();
            fd.append('counterparty', counterparty);
            var url = "{{ route('simpleWorkflowReport.financial-transactions.showAddDebit', 'counterparty') }}";
            url = url.replace('counterparty', counterparty);
            open_admin_modal(url, 'افزودن بدهکاری');
        }

        function showAddNewDebit(counterparty = null) {
            var fd = new FormData();
            fd.append('counterparty', counterparty);
            var url = "{{ route('simpleWorkflowReport.financial-transactions.showAddDebit', 'counterparty') }}";
            url = url.replace('counterparty', counterparty);
            open_admin_modal(url, 'افزودن بدهکاری');
        }
    </script>
@endsection
