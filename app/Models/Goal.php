<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Goal extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'budget',
        'start',
        'end',
        'user_id',
    ];
    protected $casts = [
        'period'=>'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}


