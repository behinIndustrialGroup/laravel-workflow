@extends('behin-layouts.app')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header">ایجاد پرونده</div>
            <div class="card-body">
                <form action="{{ route('simpleWorkflowReport.counter-party.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">نام</label>
                                <input type="text" name="name" id="name" class="form-control" autofocus focus>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="account_number">شماره حساب</label>
                                <input type="text" name="account_number" id="account_number" class="form-control">
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">ایجاد</button>
                </form>
            </div>
        </div>
    </div>
@endsection
