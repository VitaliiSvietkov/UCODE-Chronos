<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalendarAccess extends Model
{
    use HasFactory;

    public $timestamps  = false;
    protected $table    = 'calendar_access';
    protected $fillable = [
        'user_id',
        'calendar_id',
    ];
}
