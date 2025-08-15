<?php

namespace UserProfile\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'national_id', 'user_id', 'header_background', 'sidebar_background'
    ];

    public function User()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
