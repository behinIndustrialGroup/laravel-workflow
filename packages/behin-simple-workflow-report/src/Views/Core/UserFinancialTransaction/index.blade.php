@extends('behin-layouts.app')

@section('title', 'مساعده ها پرسنل')

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
    <div class="card mb-3">
        <div class="card-header">
            {{-- <button class="btn btn-sm btn-success" onclick="showAddNewCredit()">افزودن
                طلبکار <br>(مدارپرداز به مشتری بدهکار است)
            </button> --}}
            <a href="{{ route('simpleWorkflowReport.financial-transactions.user.export', ['filter' => $filter ?? null, 'case_number' => $caseNumber ?? null]) }}"
                class="btn btn-sm btn-success">خروجی اکسل</a>

            <button class="btn btn-sm btn-primary" type="button" id="open-user-salary-advances">
                بازکردن حساب مساعده جدید
            </button>

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

    @if (filled($caseNumber ?? null))
        <div class="alert alert-info">
            نمایش نتایج برای پرونده شماره <span class="font-weight-bold">{{ $caseNumber }}</span>
        </div>
    @endif
    <div class="card table-responsive">
        <div class="card-header bg-secondary text-center">
            <h3 class="card-title">مساعده ها پرسنل</h3>
        </div>
        <div class="card-body">
            <table class="table table-bordered" id="cheque-list">
                <thead>
                    <tr>
                        <th style="width: 40px">
                            <input type="checkbox" id="select-all-counterparties">
                        </th>
                        <th>پرسنل</th>
                        <th>مانده حساب</th>
                        <th>اقدامات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($creditors as $creditor)
                        <tr @if ($creditor->total_amount < 0) style="background: #f56c6c" @endif
                            @if ($creditor->total_amount > 0) class="bg-success" @endif>
                            <td class="text-center">
                                <input type="checkbox" class="counterparty-checkbox"
                                    value="{{ $creditor->counterparty_id }}">
                            </td>
                            <td>{{ $creditor->name ?? '' }}</td>
                            <td dir="ltr">{{ number_format($creditor->total_amount) }}</td>
                            <td>
                                <button class="btn btn-sm btn-primary"
                                    onclick="showDetails(`{{ $creditor->counterparty_id }}`)">جزئیات بیشتر</button>
                                <button class="btn btn-sm btn-success"
                                    onclick="showAddCredit(`{{ $creditor->counterparty_id }}`)">افزودن سند دریافتنی
                                <button class="btn btn-sm btn-warning"
                                    onclick="showAddDebit(`{{ $creditor->counterparty_id }}`)">افزودن بدهکاری</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th></th>
                        <th style="text-align:right">جمع:</th>
                        <th></th> <!-- اینجا جمع ستون مانده حساب -->
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $('#cheque-list').DataTable({
            "pageLength": 25,
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Persian.json"
            },
            "footerCallback": function(row, data, start, end, display) {
                var api = this.api();

                // تابع برای حذف جداکننده هزارگان یا علامت‌های غیرعددی
                var intVal = function(i) {
                    return typeof i === 'string' ?
                        i.replace(/[\$,٬,]/g, '') * 1 :
                        typeof i === 'number' ?
                        i : 0;
                };

                // جمع کل جدول (ستون سوم = index 2)
                var total = api
                    .column(2, {
                        search: 'applied'
                    })
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                // جمع صفحه جاری
                var pageTotal = api
                    .column(2, {
                        page: 'current'
                    })
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                // نمایش در فوتر
                $(api.column(1).footer()).html(
                    'صفحه: ' + pageTotal.toLocaleString() + '<br>کل: ' + total.toLocaleString()
                );
            }
        });

        const salaryAdvanceUrlTemplate = "{{ route('simpleWorkflowReport.financial-transactions.openUserSalaryAdvances', ['counterparty' => '__counterparty__']) }}";

        function getSelectedCounterparties() {
            return Array.from(document.querySelectorAll('.counterparty-checkbox:checked')).map((checkbox) => checkbox.value);
        }

        function openSalaryAdvancesForSelected() {
            const selected = getSelectedCounterparties();
            if (!selected.length) {
                alert('هیچ ردیفی انتخاب نشده است.');
                return;
            }

            selected.forEach((counterpartyId) => {
                const url = salaryAdvanceUrlTemplate.replace('__counterparty__', counterpartyId);
                window.open(url, '_blank');
            });
        }

        document.getElementById('open-user-salary-advances').addEventListener('click', openSalaryAdvancesForSelected);

        const selectAllCheckbox = document.getElementById('select-all-counterparties');
        selectAllCheckbox.addEventListener('change', function() {
            document.querySelectorAll('.counterparty-checkbox').forEach((checkbox) => {
                checkbox.checked = selectAllCheckbox.checked;
            });
        });

        document.querySelectorAll('.counterparty-checkbox').forEach((checkbox) => {
            checkbox.addEventListener('change', function() {
                if (!this.checked) {
                    selectAllCheckbox.checked = false;
                    return;
                }

                const allSelected = Array.from(document.querySelectorAll('.counterparty-checkbox')).every(
                    (item) => item.checked);
                selectAllCheckbox.checked = allSelected;
            });
        });


        function showDetails(counterparty) {
            var fd = new FormData();
            fd.append('counterparty', counterparty);
            var url = "{{ route('simpleWorkflowReport.financial-transactions.show', 'counterparty') }}";
            url = url.replace('counterparty', counterparty);
            open_admin_modal(url, 'جزئیات بیشتر');
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

        function showAddNewDebit(counterparty = null, onlyAssignedUsers = true) {
            var fd = new FormData();
            fd.append('counterparty', counterparty);
            fd.append('onlyAssignedUsers', onlyAssignedUsers);
            var url =
                "{{ route('simpleWorkflowReport.financial-transactions.showAddDebit', ['counterparty', 'onlyAssignedUsers']) }}";
            url = url.replace('counterparty', counterparty);
            url = url.replace('onlyAssignedUsers', onlyAssignedUsers);
            open_admin_modal(url, 'افزودن بدهکاری');
        }
    </script>
@endsection
