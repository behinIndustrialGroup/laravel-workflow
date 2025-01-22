@extends('behin-layouts.app')

@section('title')
    گزارش‌های گردش کار
@endsection


@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">فرم های گزارش نقش ها</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>شناسه</th>
                                        <th>فرایند</th>
                                        <th>نقش</th>
                                        <th>فرم</th>
                                        <th>عملیات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($role_forms as $item)
                                        <form action="{{ route('simpleWorkflowReport.role.update', ['role' => $item]) }}"
                                            method="POST">
                                            @csrf
                                            @method('PUT')
                                            <tr>
                                                <td>{{ $item->id }}</td>
                                                <td>
                                                    {{ $item->process()->name }}
                                                    <input type="text" name="process_id" id=""
                                                        value="{{ $item->process_id }}" class="form-control"
                                                        list="processes">
                                                    <datalist id="processes">
                                                        @foreach (getProcesses() as $process)
                                                            <option value="{{ $process->id }}">{{ $process->name }}
                                                            </option>
                                                        @endforeach
                                                </td>
                                                <td>
                                                    {{ $item->role()->name }}
                                                    <input type="text" name="role_id" id=""
                                                        value="{{ $item->role_id }}" class="form-control" list="roles">
                                                    <datalist id="roles">
                                                        @foreach ($roles as $role)
                                                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                                                        @endforeach
                                                </td>
                                                <td>
                                                    {{ getFormInformation($item->summary_form_id)?->name ?? '' }}
                                                    <input type="text" name="summary_form_id" id=""
                                                        class="form-control" list="forms">
                                                    <datalist id="forms">
                                                        @foreach (getProcessForms() as $form)
                                                            <option value="{{ $form->id }}">{{ $form->name }}
                                                            </option>
                                                        @endforeach
                                                    </datalist>
                                                </td>
                                                <td>
                                                    <button
                                                        class="btn btn-sm btn-primary">{{ trans('fields.Update') }}</button>
                                                </td>
                                            </tr>
                                        </form>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <form action="{{ route('simpleWorkflowReport.role.store') }}" method="POST">
                                        @csrf
                                        <tr>
                                            <td></td>
                                            <td>
                                                <input type="text" name="process_id" id="" class="form-control" list="processes">
                                                <datalist id="processes">
                                                    @foreach (getProcesses() as $process)
                                                        <option value="{{ $process->id }}">{{ $process->name }}</option>
                                                    @endforeach
                                            </td>
                                            <td>
                                                <input type="text" name="role_id" id="" class="form-control" list="roles">
                                                <datalist id="roles">
                                                    @foreach ($roles as $role)
                                                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                                                    @endforeach
                                            </td>
                                            <td>
                                                <input type="text" name="summary_form_id" id=""
                                                    class="form-control" list="forms">
                                                <datalist id="forms">
                                                    @foreach (getProcessForms() as $form)
                                                        <option value="{{ $form->id }}">{{ $form->name }}</option>
                                                    @endforeach
                                                </datalist>
                                            </td>
                                            <td>
                                                <button
                                                    class="btn btn-sm btn-primary">{{ trans('fields.Store') }}</button>
                                            </td>
                                        </tr>
                                    </form>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
