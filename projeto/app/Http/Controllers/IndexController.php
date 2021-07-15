<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Imovel;

class IndexController extends Controller
{
    //
    public function index(){
        $destaques = Imovel::select('*')->where('id','!=','')->orderBy('desconto', 'DESC')->limit(6)->get();
        return view('index', compact('destaques'));
    }

    public function cidades(Request $request)
    {
    	$this->validate($request, [
    		'estado'  => 'max:2|required'
    	]);

    	$estado = $request['estado'];


    	$sql = "SELECT DISTINCT(TRIM(cidade)) AS cidade FROM imovels WHERE estado = '" . $estado . "' GROUP BY TRIM(cidade) ORDER BY TRIM(cidade) ASC";

    	$resultSet = \DB::select($sql);//Imovel::select("cidade")->where('estado','=',$estado)->groupBy('cidade')->orderBy('cidade','ASC')->get();
    	$html = "";
    	$vetor = [];
    	foreach($resultSet as $idx => $value):
    		if(in_array($value->cidade, $vetor)):
    			continue;
    		else:
    			$vetor[] = trim($value->cidade);
    			$html .= "<li data-value='" . trim($value->cidade) . "' class='option tipo'>" . ucwords($value->cidade) . "</li>";
    		endif;
    	endforeach;

    	echo $html;

    }
}
