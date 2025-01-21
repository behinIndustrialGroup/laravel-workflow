@extends('behin-layouts.app')

@section('title')
گزارش‌های گردش کار
@endsection


@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">گزارش‌های گردش کار</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>شناسه</th>
                                        <th>نقش</th>
                                        <th>فرم</th>
                                        <th>عملیات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($roles as $role)
                                    <form action="{{ route('simpleWorkflowReport.role.update', [ 'role' => $role ]) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <tr>
                                            <td>{{ $role->id }}</td>
                                            <td>{{ $role->name }}</td>
                                            <td>
                                                {{ getFormInformation($role->summary_report_form_id)?->name ?? '' }}
                                                <input type="text" name="summary_report_form_id" id="" class="form-control" list="forms">
                                                <datalist id="forms">
                                                    @foreach (getProcessForms() as $form)
                                                        <option value="{{ $form->id }}">{{ $form->name }}</option>
                                                    @endforeach
                                                </datalist>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-primary">{{ trans('fields.Submit') }}</button>
                                            </td>
                                        </tr>
                                    </form>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
