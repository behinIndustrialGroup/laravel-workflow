@extends('behin-layouts.app')

@section('title')
    User Roles
@endsection

@section('content')
<div class="container table-responsive">
    <div class="card">
        <h3>
            {{ $role->name }}
        </h3>
        <form action="{{ route('role.edit') }}" id="method-form" method="POST">
            @csrf
            <input type="hidden" name="role_id" id="" value="{{ $role->id }}">
            <table class="table table-stripped">
                <thead>
                    <tr>
                        <th>نام</th>
                        <th>دسته بندی</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($methods as $method)
                        <tr>
                            <td><input type="checkbox" name="{{ $method->id }}" id="" {{ $method->access ? 'checked' : '' }}>{{ $method->name }}</td>
                            <td>{{ $method->category }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <button class="btn btn-primary" >Submit</button>

        </form>


    </div>
</div>

@endsection

<script>
</script>
