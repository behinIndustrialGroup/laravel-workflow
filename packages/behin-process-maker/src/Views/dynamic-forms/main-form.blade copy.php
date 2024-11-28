@extends('PMViews::dyna-form-layout')
@section('content')
    <div class="row form-group">
        <div class="col-sm-3">{{__('agency_manager_name')}}</div>
        <div class="col-sm-9">
            <input type="text" name="agency_manager_name" value="{{$variable_values->agency_manager_name  ?? ''}}" class="form-control" id="">
        </div>
    </div>
    <div class="row form-group">
        <div class="col-sm-3">{{__('created_by')}}</div>
        <div class="col-sm-9">
            <input type="text" name="created_by" value="{{$variable_values->created_by  ?? ''}}" class="form-control" id="">
        </div>
    </div>
    <div class="row form-group">
        <div class="col-sm-3">{{__('agency_name')}}</div>
        <div class="col-sm-9">
            <input type="text" name="agency_name" value="{{$variable_values->agency_name  ?? ''}}" class="form-control" id="">
        </div>
    </div>
    <div class="row form-group">
        <div class="col-sm-3">{{__('warning_number')}}</div>
        <div class="col-sm-9">
            <input type="text" name="warning_number" value="{{$variable_values->warning_number  ?? ''}}" class="form-control" id="">
        </div>
    </div>
    <div class="row form-group">
        <div class="col-sm-3">{{__('توضیحات')}}</div>
        <div class="col-sm-9">
            <input type="text" name="description" value="{{$variable_values->description  ?? ''}}" class="form-control" id="">
        </div>
    </div>
    <div class="row form-group">
        <div class="col-sm-3">{{__('warning_registrar')}}</div>
        <div class="col-sm-9">
            <input type="text" name="warning_registrar" value="{{$variable_values->warning_registrar  ?? ''}}" class="form-control" id="">
        </div>
    </div>
    <div class="row form-group">
        <div class="col-sm-3">{{__('agency_address')}}</div>
        <div class="col-sm-9">
            <input type="text" name="agency_address" value="{{$variable_values->agency_address  ?? ''}}" class="form-control" id="">
        </div>
    </div>
    <div class="row form-group">
        <div class="col-sm-3">{{__('agency_mobile')}}</div>
        <div class="col-sm-9">
            <input type="text" name="agency_mobile" value="{{$variable_values->agency_mobile  ?? ''}}" class="form-control" id="">
        </div>
    </div>
    <div class="row form-group">
        <div class="col-sm-3">{{__('warning_register_date')}}</div>
        <div class="col-sm-9">
            <input type="text" name="warning_register_date" value="{{$variable_values->warning_register_date  ?? ''}}" class="form-control" id="">
        </div>
    </div>
    <div class="row form-group">
        <div class="col-sm-3">{{__('warning_receiver')}}</div>
        <div class="col-sm-9">
            <input type="text" name="warning_receiver" value="{{$variable_values->warning_receiver  ?? ''}}" class="form-control" id="">
        </div>
    </div>
    <div class="row form-group">
        <div class="col-sm-3">{{__('warning_deadline')}}</div>
        <div class="col-sm-9">
            <input type="text" name="warning_deadline" value="{{$variable_values->warning_deadline  ?? ''}}" class="form-control" id="">
        </div>
    </div>
    <div class="row form-group">
        <div class="col-sm-3">{{__('agency_manager_national_id')}}</div>
        <div class="col-sm-9">
            <input type="text" name="agency_manager_national_id" value="{{$variable_values->agency_manager_national_id  ?? ''}}" class="form-control" id="">
        </div>
    </div>
    <div class="row form-group">
        <div class="col-sm-3">{{__('referral')}}</div>
        <div class="col-sm-9">
            <select name="referral" id="">
                @php
                    $referral = json_decode(json_encode($vars->where('var_title', 'referral')->first()->accepted_value));
                @endphp
                {{$referral}}
            </select>
            <input type="text" name="referral" value="{{$variable_values->referral  ?? ''}}" class="form-control" id="">
        </div>
    </div>
    <div class="row form-group">
        <div class="col-sm-3">{{__('referral_and_notice_sms')}}</div>
        <div class="col-sm-9">
            <input type="text" name="referral_and_notice_sms" value="{{$variable_values->referral_and_notice_sms  ?? ''}}" class="form-control" id="">
        </div>
    </div>
    <div class="row form-group">
        <div class="col-sm-3">{{__('inactivity_commitment')}}</div>
        <div class="col-sm-9">
            <input type="text" name="inactivity_commitment" value="{{$variable_values->inactivity_commitment  ?? ''}}" class="form-control" id="">
        </div>
    </div>
@endsection


{{-- @foreach ($vars as $var)
    @php
        $var_name = $var->var_title;
    @endphp
    <div class="row form-group">
        <div class="col-sm-3">{{ __($var_name) }}</div>
        <div class="col-sm-9">
            <input type="text" name="{{ $var_name }}" value="{{ $variable_values->$var_name ?? '' }}"
                class="form-control" id="">
        </div>
    </div>
@endforeach --}}
