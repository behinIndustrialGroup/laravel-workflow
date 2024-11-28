<?php

namespace Behin\PMCaseNumbering\Controllers;

use App\Http\Controllers\Controller;
use Behin\PMCaseNumbering\Models\PMCaseNumbering;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CaseNumberingController extends Controller
{
    public static function getAll()
    {
        return PMCaseNumbering::get();
    }

    public static function form(){
        return view('CaseNumberingView::index')->with([
            'rows' => self::getAll()
        ]);
    }

    public static function getOrCreate($pro_id){
        $row = PMCaseNumbering::where('process_id', $pro_id)->first();
        if($row){
            return $row;
        }
        return PMCaseNumbering::create([
            'process_id' => $pro_id,
            'api_key' => Str::random(32)
        ]);
    }

}
