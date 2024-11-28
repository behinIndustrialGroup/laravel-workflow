<?php

namespace BehinProcessMaker\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PMUsers extends Model
{
    use HasFactory;
    public $table = 'rbac_users';
    protected $connection = 'pm_mysql';
    
}
