<?php

namespace BehinProcessMaker\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PMVacation extends Model
{
    use HasFactory;
    public $table = 'pmt_vacation_requests';
    protected $connection = 'pm_mysql';
    
}
