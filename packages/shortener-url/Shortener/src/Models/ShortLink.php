<?php

namespace ShortenerUrl\Shortener\Models;

use Illuminate\Database\Eloquent\Model;

class ShortLink extends Model
{
    protected $fillable = ['code', 'original_url'];
}
