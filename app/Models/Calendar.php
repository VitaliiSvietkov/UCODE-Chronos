<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Calendar extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'owner_id',
        'name',
    ];

    /**
     * @return HasOne
     */
    public function userOwner(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'owner_id');
    }
}
