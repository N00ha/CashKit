<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'money_spending',
        'home_status',
        'dept',
        'marital_status',
        'children_number',
        'income',
        'income_period',
        'financial_goals',
        'saving',
        'user_id'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
