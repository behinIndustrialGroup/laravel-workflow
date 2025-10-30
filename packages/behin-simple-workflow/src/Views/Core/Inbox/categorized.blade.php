@extends('behin-layouts.app')

@section('style')
    <style>
        .categorized-inbox-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 12px 32px rgba(25, 118, 210, 0.08);
        }

        .categorized-inbox-card .card-body {
            padding: 2rem;
        }

        .task-category-wrapper {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .task-category-scroll {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .category-chip {
            border: none;
            border-radius: 999px;
            padding: 0.6rem 1.2rem;
            background: #e3f2fd;
            color: #1976d2;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
        }

        .category-chip .chip-count {
            background-color: rgba(25, 118, 210, 0.15);
            padding: 0.1rem 0.65rem;
            border-radius: 999px;
            font-size: 0.85rem;
        }

        .category-chip:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(25, 118, 210, 0.25);
        }

        .category-chip.active {
            background: linear-gradient(135deg, #1976d2, #42a5f5);
            color: #fff;
        }

        .category-chip.active .chip-count {
            background-color: rgba(255, 255, 255, 0.25);
            color: #fff;
        }

        .task-category-select {
            max-width: 260px;
        }

        .active-filter-card {
            background: #e8f5e9;
            border: none;
            border-radius: 12px;
            padding: 1rem 1.25rem;
        }

        .active-filter-card strong {
            color: #2e7d32;
        }

        .active-filter-card button {
            border: none;
            background: transparent;
            color: #2e7d32;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            cursor: pointer;
        }

        .advanced-filter-card {
            background: #f5f9ff;
            border: 1px solid #dbeafe;
            border-radius: 16px;
            padding: 1.5rem;
        }

        .advanced-filter-condition.input-group {
            border: 1px solid #c7d2fe;
            border-radius: 999px;
            background-color: #fff;
            padding: 0.25rem 0.5rem;
            gap: 0.5rem;
        }

        .advanced-filter-condition .form-select,
        .advanced-filter-condition .form-control {
            border: none;
            box-shadow: none;
            background: transparent;
        }

        .advanced-filter-condition .form-select:focus,
        .advanced-filter-condition .form-control:focus {
            box-shadow: none;
        }

        .advanced-filter-condition .input-group-text {
            border: none;
            background: transparent;
            color: #1d4ed8;
        }

        .advanced-filter-condition .btn-outline-danger {
            border: none;
        }

        .advanced-filter-condition .btn-outline-danger:hover {
            background: rgba(239, 68, 68, 0.08);
        }

        #advanced-filter-active {
            border-radius: 12px;
        }

        #advanced-filter-active strong {
            color: #1d4ed8;
        }

        .table-modern {
            border-radius: 14px;
            overflow: hidden;
            background: #fff;
        }

        .table-modern thead {
            background: #f1f5f9;
            color: #374151;
        }

        .table-modern tbody tr {
            transition: background-color 0.2s ease, transform 0.2s ease;
        }

        .table-modern tbody tr:hover {
            background-color: #f8fafc;
            transform: translateY(-1px);
        }

        .table-modern td,
        .table-modern th {
            vertical-align: middle;
        }

        .status-badge {
            padding: 0.4rem 0.85rem;
            border-radius: 999px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .status-new {
            background: rgba(25, 118, 210, 0.15);
            color: #0d47a1;
        }

        .status-in-progress {
            background: rgba(255, 193, 7, 0.2);
            color: #ff8f00;
        }

        .status-draft {
            background: rgba(3, 169, 244, 0.15);
            color: #0277bd;
        }

        .status-canceled {
            background: rgba(244, 67, 54, 0.18);
            color: #c62828;
        }

        .status-done {
            background: rgba(76, 175, 80, 0.18);
            color: #2e7d32;
        }

        @media (max-width: 768px) {
            .categorized-inbox-card .card-body {
                padding: 1.5rem;
            }

            .task-category-select {
                width: 100%;
                max-width: none;
            }

            .advanced-filter-card {
                padding: 1rem;
            }
        }
    </style>
@endsection

@section('content')
    <div class="container py-3">
        <div class="card categorized-inbox-card">
            <div class="card-body">
                <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
                    <div>
                        <h2 class="h4 fw-bold text-dark mb-1">{{ trans('fields.Categorized Inbox') }}</h2>
                        <p class="text-muted mb-0 small">{{ trans('fields.Categorized Inbox Hint') }}</p>
                    </div>
                    
                </div>

                @if ($taskCategories->isNotEmpty())
                    <div class="task-category-wrapper mb-4">
                        <div class="task-category-scroll">
                            <button type="button" class="category-chip {{ $selectedTaskId === null ? 'active' : '' }}"
                                data-task-filter="">
                                <span class="chip-label">{{ trans('fields.All Tasks') }}</span>
                                <span class="chip-count">{{ $totalCount }}</span>
                            </button>
                            @foreach ($taskCategories as $category)
                                <button type="button"
                                    class="category-chip {{ $selectedTaskId === $category['id'] ? 'active' : '' }}"
                                    data-task-filter="{{ $category['id'] }}">
                                    <span class="chip-label">{{ $category['label'] }}</span>
                                    <span class="chip-count">{{ $category['count'] }}</span>
                                </button>
                            @endforeach
                        </div>
                        <div class="task-category-select">
                            <label for="task-filter" class="form-label text-muted small mb-1">{{ trans('fields.Switch Task') }}</label>
                            <select id="task-filter" class="form-select rounded-pill">
                                <option value="" {{ $selectedTaskId === null ? 'selected' : '' }}>{{ trans('fields.All Tasks') }}</option>
                                @foreach ($taskCategories as $category)
                                    <option value="{{ $category['id'] }}"
                                        {{ $selectedTaskId === $category['id'] ? 'selected' : '' }}>
                                        {{ $category['label'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @else
                    <div class="alert alert-info mb-0">{{ trans('fields.You have no items in your inbox') }}</div>
                @endif

                <div class="advanced-filter-card mb-4">
                    <div
                        class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-3">
                        <div>
                            <h3 class="h6 fw-bold mb-1">{{ trans('fields.Advanced Filter') }}</h3>
                            <p class="text-muted small mb-0">{{ trans('fields.Advanced Filter Hint') }}</p>
                        </div>
                        <div class="d-flex flex-column flex-md-row align-items-md-center gap-2">
                            <div class="d-flex align-items-center gap-2">
                                <label for="advanced-filter-mode" class="form-label text-muted small mb-0">
                                    {{ trans('fields.Match Mode') }}
                                </label>
                                <select id="advanced-filter-mode" class="form-select form-select-sm rounded-pill">
                                    <option value="and">{{ trans('fields.Match All Conditions') }}</option>
                                    <option value="or">{{ trans('fields.Match Any Conditions') }}</option>
                                </select>
                            </div>
                            <button type="button"
                                class="btn btn-outline-primary btn-sm rounded-pill d-flex align-items-center gap-1"
                                id="add-advanced-condition" {{ empty($availableVariables) ? 'disabled' : '' }}>
                                <i class="material-icons fs-6">add</i>
                                <span>{{ trans('fields.Add Condition') }}</span>
                            </button>
                        </div>
                    </div>

                    @if (empty($availableVariables))
                        <div class="alert alert-light border text-muted mb-0">
                            {{ trans('fields.No Variables Available') }}
                        </div>
                    @else
                        <div id="advanced-conditions" class="d-flex flex-column gap-2"></div>
                        <div class="d-flex justify-content-end gap-2 mt-3">
                            <button type="button" class="btn btn-light btn-sm rounded-pill" id="clear-advanced-filter">
                                {{ trans('fields.Clear Filter') }}
                            </button>
                            <button type="button" class="btn btn-primary btn-sm rounded-pill"
                                id="apply-advanced-filter">
                                {{ trans('fields.Apply Filter') }}
                            </button>
                        </div>
                        <div id="advanced-filter-active" class="alert alert-info d-none mt-3 small"></div>
                    @endif
                </div>

                @if ($selectedTaskLabel)
                    <div class="active-filter-card d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                        <div class="d-flex flex-column">
                            <span class="text-muted small">{{ trans('fields.Selected Task') }}</span>
                            <strong>{{ $selectedTaskLabel }}</strong>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <div class="text-muted small">{{ trans('fields.Items Count') }}: {{ $rows->count() }}</div>
                            <button type="button" data-task-filter="">
                                <i class="material-icons">close</i>
                                {{ trans('fields.Clear Filter') }}
                            </button>
                        </div>
                    </div>
                @endif

                @if ($rows->isEmpty())
                    <div class="alert alert-light border text-muted">{{ trans('fields.You have no items in your inbox') }}</div>
                @else
                    <div class="table-responsive table-modern">
                        <table class="table align-middle mb-0" id="categorized-inbox-table">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>#</th>
                                    <th>{{ trans('fields.Case Title') }}</th>
                                    <th>{{ trans('fields.Process Title') }}</th>
                                    <th>{{ trans('fields.Case Number') }}</th>
                                    <th>{{ trans('fields.Status') }}</th>
                                    <th>{{ trans('fields.Received At') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rows as $index => $row)
                                    @php
                                        $rowVariables = $caseVariables[$row->case_id] ?? [];
                                    @endphp
                                    <tr ondblclick="window.location.href = '{{ route('simpleWorkflow.inbox.view', $row->id) }}'"
                                        data-case-id="{{ $row->case_id }}"
                                        data-variables='@json($rowVariables, JSON_UNESCAPED_UNICODE)'>
                                        <td class="text-nowrap">
                                            <a href="{{ route('simpleWorkflow.inbox.view', $row->id) }}" class="text-primary me-2">
                                                <i class="material-icons">open_in_new</i>
                                            </a>
                                            @if ($row->task && $row->task->allow_cancel)
                                                <a href="{{ route('simpleWorkflow.inbox.cancel', $row->id) }}"
                                                    title="{{ trans('fields.Cancel') }}"
                                                    onclick="return confirm('آیا از لغو درخواست مطمئن هستید؟')"
                                                    class="text-danger">
                                                    <i class="material-icons">cancel</i>
                                                </a>
                                            @endif
                                        </td>
                                        <td>{{ str_pad($index + 1, 3, '0', STR_PAD_LEFT) }}</td>
                                        <td>
                                            <div class="fw-semibold text-dark">{{ $row->case_name ?? '-' }}</div>
                                        </td>
                                        <td>{{ optional($row->task->process ?? null)->name ?? '-' }}</td>
                                        <td>{{ optional($row->case ?? null)->number ?? '-' }}</td>
                                        <td>
                                            @php
                                                $status = $row->status;
                                            @endphp
                                            @if ($status === 'new')
                                                <span class="status-badge status-new">{{ trans('fields.New') }}</span>
                                            @elseif($status === 'in_progress' || $status === 'inProgress')
                                                <span class="status-badge status-in-progress">{{ trans('fields.In Progress') }}</span>
                                            @elseif($status === 'draft')
                                                <span class="status-badge status-draft">{{ trans('fields.Draft') }}</span>
                                            @elseif($status === 'canceled')
                                                <span class="status-badge status-canceled">{{ trans('fields.Canceled') }}</span>
                                            @else
                                                <span class="status-badge status-done">{{ trans('fields.Completed') }}</span>
                                            @endif
                                        </td>
                                        <td dir="ltr" class="text-muted">
                                            {{ toJalali($row->created_at)->format('Y-m-d') }}
                                            <span class="d-block small">{{ toJalali($row->created_at)->format('H:i') }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        const variableOptions = @json($availableVariables ?? []);
        const operatorOptions = [{
                value: 'equals',
                label: '{{ trans('fields.Filter Operator Equals') }}'
            },
            {
                value: 'not_equals',
                label: '{{ trans('fields.Filter Operator Not Equals') }}'
            },
            {
                value: 'contains',
                label: '{{ trans('fields.Filter Operator Contains') }}'
            },
            {
                value: 'not_contains',
                label: '{{ trans('fields.Filter Operator Not Contains') }}'
            },
            {
                value: 'starts_with',
                label: '{{ trans('fields.Filter Operator Starts With') }}'
            },
            {
                value: 'ends_with',
                label: '{{ trans('fields.Filter Operator Ends With') }}'
            },
            {
                value: 'gt',
                label: '{{ trans('fields.Filter Operator Greater Than') }}'
            },
            {
                value: 'lt',
                label: '{{ trans('fields.Filter Operator Less Than') }}'
            },
            {
                value: 'is_empty',
                label: '{{ trans('fields.Filter Operator Is Empty') }}'
            },
            {
                value: 'is_not_empty',
                label: '{{ trans('fields.Filter Operator Is Not Empty') }}'
            },
        ];

        const advancedFilterState = {
            conditions: [],
            mode: 'and'
        };

        let categorizedTableInstance = null;
        let conditionsContainer = null;
        let addConditionButton = null;
        let applyFilterButton = null;
        let clearFilterButton = null;
        let matchModeSelect = null;

        function normalizeValue(value) {
            if (value === undefined || value === null) {
                return '';
            }
            if (Array.isArray(value)) {
                return value.join(', ');
            }
            if (typeof value === 'object') {
                try {
                    return JSON.stringify(value);
                } catch (error) {
                    return '';
                }
            }
            return String(value);
        }

        function isNumeric(value) {
            if (value === undefined || value === null) {
                return false;
            }
            const normalized = String(value).replace(/\s+/g, '').replace(/,/g, '.');
            return normalized !== '' && !isNaN(Number(normalized));
        }

        function compareValues(value, target, comparator) {
            if (isNumeric(value) && isNumeric(target)) {
                const numericValue = Number(String(value).replace(/\s+/g, '').replace(/,/g, '.'));
                const numericTarget = Number(String(target).replace(/\s+/g, '').replace(/,/g, '.'));
                return comparator(numericValue, numericTarget);
            }

            const valueString = normalizeValue(value).toLowerCase();
            const targetString = normalizeValue(target).toLowerCase();
            return comparator(valueString, targetString);
        }

        function evaluateCondition(rawValue, condition) {
            const valueString = normalizeValue(rawValue);
            const searchValue = normalizeValue(condition.value);
            const lowerValue = valueString.toLowerCase();
            const lowerSearch = searchValue.toLowerCase();

            switch (condition.operator) {
                case 'equals':
                    return lowerValue === lowerSearch;
                case 'not_equals':
                    return lowerValue !== lowerSearch;
                case 'contains':
                    return lowerSearch === '' ? true : lowerValue.includes(lowerSearch);
                case 'not_contains':
                    return lowerSearch === '' ? true : !lowerValue.includes(lowerSearch);
                case 'starts_with':
                    return lowerSearch === '' ? true : lowerValue.startsWith(lowerSearch);
                case 'ends_with':
                    return lowerSearch === '' ? true : lowerValue.endsWith(lowerSearch);
                case 'gt':
                    return compareValues(valueString, searchValue, (a, b) => a > b);
                case 'lt':
                    return compareValues(valueString, searchValue, (a, b) => a < b);
                case 'is_empty':
                    return valueString.trim() === '';
                case 'is_not_empty':
                    return valueString.trim() !== '';
                default:
                    return true;
            }
        }

        function evaluateAdvancedFilters(variables) {
            if (!advancedFilterState.conditions.length) {
                return true;
            }

            const results = advancedFilterState.conditions.map((condition) => {
                const value = variables && Object.prototype.hasOwnProperty.call(variables, condition.field) ?
                    variables[condition.field] : '';
                return evaluateCondition(value, condition);
            });

            if (advancedFilterState.mode === 'or') {
                return results.some(Boolean);
            }

            return results.every(Boolean);
        }

        function toggleValueInput(rowElement, operator) {
            const valueInput = rowElement.querySelector('.value-input');
            if (!valueInput) {
                return;
            }

            const requiresValue = !['is_empty', 'is_not_empty'].includes(operator);
            valueInput.disabled = !requiresValue;

            if (!requiresValue) {
                valueInput.value = '';
            }
        }

        function createConditionRow() {
            if (!conditionsContainer) {
                return;
            }

            const row = document.createElement('div');
            row.className = 'advanced-filter-condition input-group input-group-sm flex-nowrap';

            const variableOptionsMarkup = variableOptions.map((option) =>
                `<option value="${option.key}">${option.label}</option>`
            ).join('');

            const operatorOptionsMarkup = operatorOptions.map((option) =>
                `<option value="${option.value}">${option.label}</option>`
            ).join('');

            row.innerHTML = `
                <span class="input-group-text material-icons">tune</span>
                <select class="form-select form-select-sm variable-select">
                    <option value="">{{ trans('fields.Select') }}</option>
                    ${variableOptionsMarkup}
                </select>
                <select class="form-select form-select-sm operator-select">
                    ${operatorOptionsMarkup}
                </select>
                <input type="text" class="form-control form-control-sm value-input" placeholder="{{ trans('fields.Value') }}">
                <button type="button" class="btn btn-outline-danger btn-sm remove-condition" title="{{ trans('fields.Remove') }}">
                    <i class="material-icons fs-6">close</i>
                </button>
            `;

            const operatorSelect = row.querySelector('.operator-select');
            if (operatorSelect) {
                toggleValueInput(row, operatorSelect.value);
                operatorSelect.addEventListener('change', (event) => {
                    toggleValueInput(row, event.target.value);
                });
            }

            const removeButton = row.querySelector('.remove-condition');
            if (removeButton) {
                removeButton.addEventListener('click', () => {
                    row.remove();
                });
            }

            conditionsContainer.appendChild(row);
        }

        function updateActiveFilterAlert() {
            const alertElement = document.getElementById('advanced-filter-active');
            if (!alertElement) {
                return;
            }

            if (!advancedFilterState.conditions.length) {
                alertElement.classList.add('d-none');
                alertElement.innerHTML = '';
                return;
            }

            const modeText = advancedFilterState.mode === 'and'
                ? '{{ trans('fields.Match All Conditions') }}'
                : '{{ trans('fields.Match Any Conditions') }}';

            const summary = advancedFilterState.conditions.map((condition) => {
                const variable = variableOptions.find((option) => option.key === condition.field);
                const operator = operatorOptions.find((option) => option.value === condition.operator);
                const variableLabel = variable ? variable.label : condition.field;
                const operatorLabel = operator ? operator.label : condition.operator;
                if (['is_empty', 'is_not_empty'].includes(condition.operator)) {
                    return `${variableLabel} ${operatorLabel}`;
                }
                return `${variableLabel} ${operatorLabel} «${condition.value}»`;
            });

            alertElement.classList.remove('d-none');
            alertElement.innerHTML = `
                <div class="fw-semibold mb-1">{{ trans('fields.Active Filters') }}</div>
                <div>${summary.join('، ')}</div>
                <div class="text-muted mt-2">{{ trans('fields.Match Mode') }}: ${modeText}</div>
            `;
        }

        function applyAdvancedFilter() {
            if (!conditionsContainer) {
                return;
            }

            const rawConditions = Array.from(conditionsContainer.querySelectorAll('.advanced-filter-condition')).map(
                (row) => {
                    const field = row.querySelector('.variable-select')?.value || '';
                    const operator = row.querySelector('.operator-select')?.value || '';
                    const valueInput = row.querySelector('.value-input');
                    const value = valueInput && !valueInput.disabled ? valueInput.value.trim() : '';
                    return {
                        field,
                        operator,
                        value
                    };
                }
            ).filter((condition) => {
                if (!condition.field || !condition.operator) {
                    return false;
                }
                if (['is_empty', 'is_not_empty'].includes(condition.operator)) {
                    return true;
                }
                return condition.value !== '';
            });

            advancedFilterState.conditions = rawConditions;
            advancedFilterState.mode = matchModeSelect ? matchModeSelect.value : 'and';

            updateActiveFilterAlert();

            if (categorizedTableInstance) {
                categorizedTableInstance.draw();
            }
        }

        function clearAdvancedFilter() {
            advancedFilterState.conditions = [];
            advancedFilterState.mode = 'and';

            if (conditionsContainer) {
                conditionsContainer.innerHTML = '';
            }

            if (matchModeSelect) {
                matchModeSelect.value = 'and';
            }

            updateActiveFilterAlert();

            if (categorizedTableInstance) {
                categorizedTableInstance.draw();
            }
        }

        function updateTaskFilter(taskId) {
            const url = new URL(window.location.href);
            if (taskId) {
                url.searchParams.set('task', taskId);
            } else {
                url.searchParams.delete('task');
            }
            window.location.href = url.toString();
        }

        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('[data-task-filter]').forEach((button) => {
                button.addEventListener('click', () => updateTaskFilter(button.dataset.taskFilter));
            });

            const taskSelect = document.getElementById('task-filter');
            if (taskSelect) {
                taskSelect.addEventListener('change', () => updateTaskFilter(taskSelect.value));
            }

            conditionsContainer = document.getElementById('advanced-conditions');
            addConditionButton = document.getElementById('add-advanced-condition');
            applyFilterButton = document.getElementById('apply-advanced-filter');
            clearFilterButton = document.getElementById('clear-advanced-filter');
            matchModeSelect = document.getElementById('advanced-filter-mode');

            if (!conditionsContainer) {
                return;
            }

            if (addConditionButton && variableOptions.length) {
                addConditionButton.addEventListener('click', () => {
                    createConditionRow();
                });
            }

            if (applyFilterButton) {
                applyFilterButton.addEventListener('click', () => {
                    applyAdvancedFilter();
                });
            }

            if (clearFilterButton) {
                clearFilterButton.addEventListener('click', () => {
                    clearAdvancedFilter();
                });
            }

            if (matchModeSelect) {
                matchModeSelect.addEventListener('change', () => {
                    if (advancedFilterState.conditions.length) {
                        advancedFilterState.mode = matchModeSelect.value;
                        updateActiveFilterAlert();
                        if (categorizedTableInstance) {
                            categorizedTableInstance.draw();
                        }
                    }
                });
            }
        });

        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            if (!settings.nTable || settings.nTable.id !== 'categorized-inbox-table') {
                return true;
            }

            if (!advancedFilterState.conditions.length) {
                return true;
            }

            const rowData = settings.aoData[dataIndex];
            if (!rowData || !rowData.nTr) {
                return true;
            }

            let variables = $(rowData.nTr).data('variables');
            if (typeof variables === 'string') {
                try {
                    variables = JSON.parse(variables);
                } catch (error) {
                    variables = {};
                }
            }

            variables = variables || {};

            return evaluateAdvancedFilters(variables);
        });

        $(document).ready(function() {
            if ($('#categorized-inbox-table').length) {
                categorizedTableInstance = $('#categorized-inbox-table').DataTable({
                    language: {
                        url: 'https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Persian.json'
                    },
                    order: [
                        [6, 'desc']
                    ],
                    pageLength: 15,
                    lengthChange: false,
                    responsive: true
                });
            }
        });
    </script>
@endsection
