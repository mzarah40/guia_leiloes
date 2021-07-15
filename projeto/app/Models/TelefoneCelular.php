<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TelefoneCelular extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'tipo',
        'numero',
        'user_id'
    ];
}
