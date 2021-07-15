<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Imovel;

class SearchController extends Controller
{
    //
    private $uf;
    private $tipo;
    private $ordem;

    public function index(Request $request)
    {
    	
        if(!$request['uf'] || $request['uf'] == ""):
            $uf = "SP";
        else:
            $uf = $request['uf'];
        endif;
        //$uf     = $request['uf'];
        $cidade = $request['cidade'];
        $lat    = $request['latitude'];
        $lng    = $request['longitude'];
        $tipo   = $request['ordem'];

        /*
        switch($ordem){
            case 1:
                $imoveis = Imovel::select('*')->where('estado', 'LIKE', "%$uf%")->where('cidade', 'LIKE',"%$cidade%")->orderBy('desconto','DESC')->paginate(50);
                $count   = $imoveis->count();
                break;
            case 2:
                $imoveis = Imovel::select('*')->where('estado', 'LIKE', "%$uf%")->where('cidade', 'LIKE', "%$cidade%")->orderBy('preco','ASC')->paginate(50);
                $count   = $imoveis->count();
                break;
            case 3:
                $imoveis = Imovel::select('*')->where('estado', 'LIKE', "%$uf%")->where('cidade', 'LIKE', "%$cidade%")->orderBy('preco','DESC')->paginate(50);
                $count   = $imoveis->count();
                break;
            case 4:
                $imoveis = Imovel::select('*')->where('estado', 'LIKE', "%$uf%")->where('cidade', 'LIKE', "%$cidade%")->orderBy('created_at','DESC')->paginate(50);
                $count   = $imoveis->count();
                break;
            default:
                $imoveis = Imovel::select('*')->where('estado', 'LIKE', "%$uf%")->where('cidade', 'LIKE',"%$cidade%")->orderBy('desconto','DESC')->paginate(50);
                $count   = $imoveis->count();
                break;
        }
        */

        $sql   = "desconto";
        $order = "DESC";


         if(isset($request['order_by']) && $request['order_by'] != ""){
            
            switch($request['order_by']){
                case "1":
                    $sql   = "desconto";
                    $order = "DESC";
                    break;
                case "2":
                    $sql   = "preco";
                    $order = "ASC";
                    break;
                case "3":
                    $sql   = "preco";
                    $order = "DESC";
                    break;
                case "4":
                    $sql   = "cidade";
                    $order = "ASC";
                    break;
                default:
                    $sql   = "desconto";
                    $order = "DESC";
                    break;
            }
            
        }


        $imoveis = Imovel::select('*')
                        ->where('estado','LIKE',"%$uf%")
                        ->where('cidade','LIKE',"%$cidade%")
                        ->where('tipo_imovel','LIKE',"%$tipo%")
                        ->orderBy($sql,$order)
                        ->paginate(50);
        $count   = $imoveis->count();

        /** ADVANCED SEARCH */ 
        /** TIPO IMOVEL */ 
        $query = "SELECT TRIM(tipo_imovel) AS ti FROM imovels GROUP BY ti ORDER BY ti ASC";
        $tipo_imovel = \DB::select($query);

        $query = "SELECT TRIM(cidade_campo) AS cc FROM imovels WHERE estado LIKE '%" . strtoupper($uf) . "%' GROUP BY cc ORDER BY cc ASC";
        $city = \DB::select($query);

        $query = "SELECT TRIM(bairro) AS cb FROM imovels WHERE estado LIKE '%" . strtoupper($uf) . "%' AND bairro != '' AND bairro IS NOT NULL GROUP BY cb ORDER BY cb ASC";
        $bairros = \DB::select($query);

        $query = "SELECT TRIM(modalidade_venda) AS mv FROM imovels WHERE estado LIKE '%" . strtoupper($uf) . "%' GROUP BY modalidade_venda ORDER BY modalidade_venda ASC";
        $modalidades = \DB::select($query);


        //dd($imoveis);


        return view('template.template_search', [
                'imoveis'     => $imoveis,
                'count'       => $count,
                'uf'          => $uf,
                'tipo_imovel' => $tipo_imovel,
                'city'        => $city,
                'bairros'     => $bairros,
                'modalidades' => $modalidades,
                //'ordem'       => $ordem, 
                'tp'          => $tipo,
                'latitude'    => $lat,
                'longitude'   => $lng,
                'cidade'      => $cidade
            ]);

    }


    public function paginateAjax(Request $request)
    {
        $pagina = (int) $request['pagina_pg'];
        if(!is_numeric($pagina) || $pagina < 2):
            dd("Página inexistente");
        endif;

        
        $pg     = $request['pagina_pg'];
        $uf     = $request['uf_pg'];
        $tipo   = $request['tipo_pg'];
        $cidade = $request['cidade_pg']; //$ordem  = $request['ordem_pg'];


        $pr = ($pg * 50) - 50;


        $sql   = "desconto";
        $order = "DESC";

        if(isset($request['order_by']) && $request['order_by'] != ""){
            
            switch($request['order_by']){
                case "1":
                    $sql   = "desconto";
                    $order = "DESC";
                    break;
                case "2":
                    $sql   = "preco";
                    $order = "ASC";
                    break;
                case "3":
                    $sql   = "preco";
                    $order = "DESC";
                    break;
                case "4":
                    $sql   = "cidade";
                    $order = "ASC";
                    break;
                default:
                    $sql   = "desconto";
                    $order = "DESC";
                    break;
            }
            
        }


        $imoveis = Imovel::select('*')->where('estado','LIKE',"%$uf%")
                        ->where('cidade','LIKE',"%$cidade%")
                        ->where('tipo_imovel','LIKE',"%$tipo%")
                        ->orderBy($sql,$order)
                        ->skip($pr)
                        ->take(50)
                        ->get();
        $count   = $imoveis->count();

        /*
        switch($ordem){
            case 1:
                $imoveis = Imovel::select('*')->where('estado', 'LIKE', "%$uf%")->where('tipo_imovel', 'LIKE',"%$tipo%")->orderBy('desconto','DESC')->skip($pr)->take(50)->get();
                $count   = $imoveis->count();
                break;
            case 2:
                $imoveis = Imovel::select('*')->where('estado', 'LIKE', "%$uf%")->where('tipo_imovel', 'LIKE', "%$tipo%")->orderBy('preco','ASC')->skip($pr)->take(50)->get();
                $count   = $imoveis->count();
                break;
            case 3:
                $imoveis = Imovel::select('*')->where('estado', 'LIKE', "%$uf%")->where('tipo_imovel', 'LIKE', "%$tipo%")->orderBy('preco','DESC')->skip($pr)->take(50)->get();
                $count   = $imoveis->count();
                break;
            case 4:
                $imoveis = Imovel::select('*')->where('estado', 'LIKE', "%$uf%")->where('tipo_imovel', 'LIKE', "%$tipo%")->orderBy('created_at','DESC')->skip($pr)->take(50)->get();
                $count   = $imoveis->count();
                break;
            default:
                $imoveis = Imovel::select('*')->where('estado', 'LIKE', "%$uf%")->where('tipo_imovel', 'LIKE',"%$tipo%")->orderBy('desconto','DESC')->skip($pr)->take(50)->get();
                $count   = $imoveis->count();
                break;
        }*/

        if($count > ($pr + 50)){
            $btn_plus = "block";
        }else{
            $btn_plus = "none";
        }



        $html = "";


        foreach($imoveis as $idreg):


                if(trim(urldecode($idreg->bairro))):
                    $hifen = " - ";
                else:
                    $hifen = "";
                endif;

                    
            $html .= "<div class='item col-lg-12 col-md-6 col-xs-12 people landscapes sale'>
                    <div class='project-single'>
                        <div class='project-inner project-head'>
                            <div class='homes'></div>
                        </div>
                    </div>
                    <!-- homes content -->
                    <div class='homes-content'>
                        <!-- homes address -->
                        <h3 style='font-size:11px !important'><a href='" . route('single_property', $idreg->id) . "' style='font-size:14px !important'>". $idreg->tipo_imovel ." - " . trim($idreg->bairro) . $hifen . $idreg->cidade_campo . "</a></h3>
                        <p class='homes-address mb-3'>
                            <a href='" . route('single_property', $idreg->id) . "'>
                                <i class='fa fa-map-marker'></i><span>" . $idreg->endereco_imovel . "</span>
                            </a>
                        </p>
                        <!-- homes List -->
                        <ul class='homes-list clearfix'>
                            <li>
                                <i class='fa fa-bed' aria-hidden='true'></i>
                                <span>" . ((!$idreg->quartos) ? "Não Informado" : ($idreg->quartos . "Quartos")) . "</span>
                            </li>
                            <li>
                                <i class='fa fa-object-group' aria-hidden='true'></i>
                                <span>" . ((!$idreg->area_privativa) ? "Não Informado" : ($idreg->area_privativa . "m²"))  . "</span>
                            </li>
                            <li>
                                <i class='fas fa-warehouse' aria-hidden='true'></i>
                                <span>" . ((!$idreg->garagem ) ? "Não Informado" : ($idreg->garagem . "Garagem")) . "</span>
                            </li>
                        </ul>
                        <!-- Price -->
                        <div class='price-properties'>
                            <h3 class='title mt-4'>
                                <a href='" . route('single_property', $idreg->id) . "'>R$ " . number_format($idreg->preco,2,',','.') . "</a>
                            </h3>
                        </div>
                        <div class='footer'>
                            <i class='fas fa-gavel'></i> <a href='" . route('single_property', $idreg->id) . "'> Visitar imóvel </a>
                            &nbsp;&nbsp;&nbsp;&nbsp;
                            <i class='fas fa-map-marker d-none d-md-inline-block'></i><a style='cursor:pointer' class='d-none d-md-inline-block' onclick='visao(" . $idreg->lat . "," . $idreg->lng . ")'>Ver Street View</a>
                            <div class='proposta'>
                                <a href='https://venda-imoveis.caixa.gov.br/sistema/detalhe-imovel.asp?hdnOrigem=index&hdnimovel=" . $idreg->id_imovel . "' target='_blank' style='color:#EB5E28;'>FAÇA SUA PROPOSTA</a>
                            </div>
                            <div class='porcentagem'>
                                <a>"  . number_format($idreg->desconto,0,"","") . "%</a>
                            </div>
                            <div class='preco_antigo' style='padding-bottom:20px !important;'>
                                <a><del>R$ " . number_format($idreg->valor_avaliacao,2,".",",") . "</del></a>
                            </div>
                        </div>
                    </div>
                </div>";
        endforeach;

        $html .= "|___|" . $btn_plus;
        
        echo $html;


    }

    // template_search.blade.php
    public function carregaCidades(Request $request){
        $this->validate($request, ['uf' => 'required|max:2']);

        $uf = $request['uf'];

        $imoveis = Imovel::select('cidade')->where("estado","LIKE","%$uf%")->groupBy('cidade')->orderBy('cidade', 'ASC')->get();

        $html = "<option selected='' value=''>Selecione Uma Opção</option>";
        foreach($imoveis as $imovel){
            $html .= "<option value='". $imovel->cidade ."'>" . $imovel->cidade . "</option>";
        }

        echo $html;
    }

    // template_search.blade.php 
    public function carregaBairros(Request $request){
        $this->validate($request, ['cidade' => 'required|max:255']);

        $cidade = $request['cidade'];

        $imoveis = Imovel::select('bairro')->where('cidade','LIKE',"%$cidade%")->groupBy('bairro')->orderBy('bairro','ASC')->get();

        $html = "<option selected='' value=''>Selecione Uma Opção</option>";

        foreach($imoveis as $imovel){
            if(trim(str_replace("\n","",urldecode($imovel->bairro))) != ""):
                $html .= "<option value='". trim(str_replace("\n", "", urldecode($imovel->bairro))) ."'>" . trim(str_replace("\n", "", urldecode($imovel->bairro))) . "</option>";
            endif;
        }

        echo $html;
    }

}
