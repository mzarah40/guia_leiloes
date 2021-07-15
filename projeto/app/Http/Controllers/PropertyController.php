<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Imovel;

class PropertyController extends Controller
{
    //
    public function index(Request $request)
    {
        $id = $request['id'];
        $id = (int) $id;

        if(!is_numeric($id)){
            return false;
        }

        
        if(!$imovel = Imovel::where('id',$id)->first()):
            return "Este imóvel não existe";
        endif;

        return view('single_property', compact('imovel'));
        
        
    }
}
