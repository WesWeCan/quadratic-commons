<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Election extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'name',
        'description',
        'credits',
        'motions',
        'options',
    ];

    protected $casts = [
        'motions' => 'array',
        'options' => 'array',
    ];

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }


}
