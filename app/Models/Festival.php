<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Festival extends Model
{
    // Every field a caller is allowed to send. Anything not listed here is ignored on create/update.
    protected $fillable = [
        'name',
        'city',
        'month',
        'duration_days',
        'theme',
    ];
}
