@extends('behin-layouts.app')

@section('title', 'گزارش حقوق پرسنل')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header bg-primary text-white">گزارش حقوق پرسنل</div>
            <div class="card-body table-responsive">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <table class="table" id="employee-salaries-table">
                    <thead>
                        <tr>
                            <th>نام کاربر</th>
                            <th>حقوق تأمین اجتماعی</th>
                            <th>حقوق توافقی</th>
                            <th>max سقف مساعده</th>
                            <th>اقدامات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->insurance_salary ? number_format($user->insurance_salary) : '-' }}</td>
                                <td>{{ $user->real_salary ? number_format($user->real_salary) : '-' }}</td>
                                <td>{{ number_format($user->real_salary - $user->insurance_salary) ?? ''}}</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#employeeSalaryModal" data-user-id="{{ $user->id }}"
                                        data-user-name="{{ $user->name }}" data-insurance="{{ $user->insurance_salary }}"
                                        data-real="{{ $user->real_salary }}">
                                        ویرایش
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="employeeSalaryModal" tabindex="-1" aria-labelledby="employeeSalaryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('simpleWorkflowReport.employee-salaries.update') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="employeeSalaryModalLabel">ویرایش حقوق</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="user_id" id="employee-salary-user-id">
                        <div class="mb-3">
                            <label class="form-label">کاربر</label>
                            <div class="form-control" id="employee-salary-user-name"></div>
                        </div>
                        <div class="mb-3">
                            <label for="employee-salary-insurance" class="form-label">حقوق تأمین اجتماعی</label>
                            <input type="number" step="any" name="insurance_salary" class="form-control formated-digit"
                                id="employee-salary-insurance">
                        </div>
                        <div class="mb-3">
                            <label for="employee-salary-real" class="form-label">حقوق توافقی</label>
                            <input type="number" step="any" name="real_salary" class="form-control"
                                id="employee-salary-real">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="closeModal()" data-bs-dismiss="modal">بستن</button>
                        <button type="submit" class="btn btn-primary">ذخیره</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {

            $('#employee-salaries-table').DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.1/i18n/fa.json'
                }
            });

            // Event delegation for dynamic rows
            $(document).on('click', 'button[data-user-id]', function() {

                const modal = new bootstrap.Modal(document.getElementById('employeeSalaryModal'));

                document.getElementById('employee-salary-user-id').value = this.getAttribute(
                'data-user-id');
                document.getElementById('employee-salary-user-name').textContent = this.getAttribute(
                    'data-user-name');
                document.getElementById('employee-salary-insurance').value = this.getAttribute(
                    'data-insurance');
                document.getElementById('employee-salary-real').value = this.getAttribute('data-real');

                modal.show(); // <-- این مدال را باز می‌کند
            });

            function closeModal(){
                const modal = new bootstrap.Modal(document.getElementById('employeeSalaryModal'));
                modal.close(); // <-- این مدال را باز می‌کند
            }

        });
    </script>
@endsection
