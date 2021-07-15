<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aviseme extends Model
{
    use HasFactory;

    protected $fillable = [
    	"consulta",
    	'email',
    	'status',
    	'created_at',
    	'updated_at'
    ];
}
