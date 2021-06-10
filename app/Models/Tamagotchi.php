<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tamagotchi extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'coins',
        'health',
        'level',
        'boredom',
        'alive',
    ];
}
