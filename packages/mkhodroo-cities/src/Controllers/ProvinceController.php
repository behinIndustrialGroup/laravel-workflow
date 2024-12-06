<?php

namespace Mkhodroo\Cities\Controllers;

use App\Http\Controllers\Controller;
use Mkhodroo\Cities\Models\NewProvince;
use Mkhodroo\Cities\Models\Province;

class ProvinceController extends Controller
{
    public static function all(){
        return NewProvince::get();
    }

    public static function getById($id){
        return NewProvince::find($id);
    }

    public static function getByName($province){
        return NewProvince::where('name', $province)->firstOrCreate([
            'name' => $province
        ]);
    }


    public static function create($province_name){
        return NewProvince::updateOrCreate(
            [
                'name' => $province_name
            ],
            [

            ]
        );
    }
}
