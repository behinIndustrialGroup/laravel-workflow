@extends('behin-layouts.app')

@section('title')
    کاربران
@endsection

@section('content')
    <div class="container">
        <div class="card ">
            <div class="card-header">
                <a href="{{route('department.index')}}">
                    <button>
                        بازگشت
                    </button>
                </a>
            </div>

            <div class="card-body">
                <form method="post" action="{{route('department.store')}}">
                    @csrf
                    <div class="form-group">
                        <label for="">نام</label>
                        <input type="text" name="name" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="">مدیر</label>
                        <select name="manager" id="" class="form-control">
                            <option value=""></option>
                            @foreach ($managers as $manager)
                                <option value="{{$manager->id}}">{{$manager->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">والد</label>
                        <select name="parent_id" id="" class="form-control">
                            <option value=""></option>
                            @foreach ($departments as $department)
                                <option value="{{$department->id}}">{{$department->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <button class="btn btn-primary">ثبت</button>
                </form>
            </div>
        </div>
    </div>
@endsection

