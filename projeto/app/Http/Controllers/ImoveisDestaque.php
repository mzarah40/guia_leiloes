<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Imovel;

class ImoveisDestaque extends Controller
{
    //
    public function index(){
        $resultSet = Imovel::select('*')->where('id','!=','')->orderBy('desconto', 'DESC')->limit(50)->get();

        return view("destaque", ['destaques' => $resultSet]);
    }
}
