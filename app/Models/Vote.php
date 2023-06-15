<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'name',
        'remainingCredits',
        'motions',
        "votecode",
        'election_id',

    ];


    protected $casts = [
        'motions' => 'array',
    ];
}
