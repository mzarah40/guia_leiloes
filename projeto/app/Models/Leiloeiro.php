<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leiloeiro extends Model
{
    use HasFactory;
    protected $fillable = [
        'nome',
        'cpf',
        'cnpj',
        'email',
        'user_id',
        'creci'
    ];
}
