<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContatoController extends Controller
{
    //
    public function index(){
        return view('contato');
    }

    public function sender(Request $request){
        $this->validate($request, [
            'email'    =>  "email|min:3|max:191",
            'name'     =>  "min:3|max:191",
            'lastname' =>  "min:3|max:191",
            'message'  =>  "min:3|max:500"
        ]);

        \Illuminate\Support\Facades\Mail::send('contato.index', [
            'email'    => $request['email'],
            'name'     => $request['name'],
            'lastname' => $request['lastname'],
            'msg'      => $request['message']
        ], function($m){
            $m->from('contato@guialeiloes.com.br', 'GuiaLeiloes');
            $m->to('contato@guialeiloes.com.br');
        });

        return view('contato')->with('status','E-mail enviado com Sucesso.');
    }
}
