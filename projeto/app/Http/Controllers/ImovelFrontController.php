<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Imovel;
class ImovelFrontController extends Controller
{
    private $uf = [];
    private $uf_front = [];
    private $arrayEstados = ['SP','MG','ES','RJ','AC','AL','AP','AM','BA','CE','GO','MA','MT','MS','PA','PB','PR','PE','PI','RN','RS','RO','RR','SC','SE','TO','DF'];
    //
    public function index(Request $estado=null){
        $menu = $this->menuEsquerdo();
        return view('index', compact('menu'));
    }
    
    private function menuEsquerdo(){
        
        foreach($this->arrayEstados as $est):
            $this->uf[strtolower($est)] = Imovel::where('estado', 'LIKE', '%'.$est.'%')->get();
            $this->uf_front[strtolower($est)] = $this->uf[strtolower($est)]->count();
        endforeach;
        
        return $this->uf_front;
    }
}
