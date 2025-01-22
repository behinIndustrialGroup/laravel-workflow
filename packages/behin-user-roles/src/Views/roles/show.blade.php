@extends('behin-layouts.app')

@section('title')
    User Roles
@endsection

@section('content')
    <div class="container table-responsive">
        <div class="d-flex justify-content-end mb-3">
            <a href="{{ route('role.listForm') }}" class="btn btn-outline-info">Back to List</a>
        </div>
        <br>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('role.edit') }}" id="method-form" method="POST">
                    @csrf
                    <input type="hidden" name="role_id" id="" value="{{ $role->id }}">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1"><i class="fa fa-user-tag"></i></span>
                        </div>
                        <input type="text" class="form-control" value="{{ $role->name }}" name="name">
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>نام</th>
                                    <th>دسته بندی</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($methods as $method)
                                    <tr>
                                        <td>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="{{ $method->id }}"
                                                    id="role-{{ $method->id }}" {{ $method->access ? 'checked' : '' }}>
                                                <label class="form-check-label" for="role-{{ $method->id }}">
                                                    {{ $method->name }}
                                                </label>
                                            </div>
                                        </td>
                                        <td>{{ $method->category }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
@endsection

<script></script>
