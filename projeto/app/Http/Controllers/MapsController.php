<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Imovel;

class MapsController extends Controller
{
    //
    public function index(){
        return view('maps');
    }

    public function ajax(){
        $imoveis = Imovel::all();
        return response()->json($imoveis);
    }



    public function ajaxReload(Request $request){




    	if(is_numeric($request['latitude']) && is_numeric($request['longitude']) && is_numeric($request['zoom'])):

            switch($request['zoom'])
            {
                case 22:
                    $distancia = 40075017 / pow(2, 0.0000610352);
                    break;
                case 21:
                    $distancia = 40075017 / pow(2, 0.0001220704);
                    break;
                case 20:
                    $distancia = 40075017 / pow(2, 0.0002441407);
                    break;
                case 19:
                    $distancia = 40075017 / pow(2, 0.0004882813);
                    break;
                case 18:
                    $distancia = 40075017 / pow(2, 0.0009765625);
                    break;
                case 17:
                    $distancia = 40075017 / pow(2, 0.001953125);
                    break;
                case 16:
                    $distancia = 40075017 / pow(2, 0.00390625);
                    break;
                case 15:
                    $distancia = 40075017 / pow(2, 0.0078125);
                    break;
                case 14:
                    $distancia = 40075017 / pow(2, 0.015625);
                    break;
                case 13:
                    $distancia = 40075017 / pow(2, 0.03125);
                    break;
                case 12:
                    $distancia = 40075017 / pow(2, 0.0625);
                    break;
                case 11:
                    $distancia = 40075017 / pow(2, 0.125);
                    break;
                case 10:
                    $distancia = 40075017 / pow(2, 0.25);
                    break;
                case 9:
                    $distancia = 40075017 / pow(2, 0.5);
                    break;
                case 8:
                    $distancia = 40075017 / pow(2, 1);
                    break;
                case 7:
                    $distancia = 40075017 / pow(2, 2);
                    break;
                case 6:
                    $distancia = 40075017 / pow(2, 4);
                    break;
                case 5:
                    $distancia = 40075017 / pow(2, 8);
                    break;
                default:
                    $distancia = 2;
                    break;
            }

            //$distancia = 591657550.5 / (pow(2, ($request['zoom'] - 1))); 

    		$query = "
    			SELECT *,   
    				( 6371 * acos( cos( radians(".$request['latitude'].") ) *
        			cos( radians( imovels.lat ) ) * cos( radians( imovels.lng ) - radians(".$request['longitude'].") ) +
        			sin( radians(".$request['latitude'].") ) * sin( radians( imovels.lat) ) ) ) AS trigon_distancia
    			FROM 
    				imovels 
    			HAVING 
                    trigon_distancia <=  $distancia    
    			ORDER BY 
    				trigon_distancia ASC
    			LIMIT 
    				300";

    		$imoveisDrag = \DB::select($query);

    		$html = "";


            foreach($imoveisDrag as $idreg):
                

                $area = "";
                if($idreg->area_total != 0){
                    $area = $idreg->area_total;
                }
                if($idreg->area_privativa != 0){
                    $area = $idreg->area_privativa;
                }
                if($idreg->area_terreno != 0){
                    $area = $idreg->area_terreno;
                }
    		
    			$html .= "<div class='item col-lg-12 col-md-6 col-xs-12 people landscapes sale'>
                            <div class='project-single'>
                                <div class='project-inner project-head'>
                                    <div class='homes'></div>
                                </div>
                            </div>
                            <!-- homes content -->
                            <div class='homes-content'>
                                <!-- homes address -->
                                <h3><a href='" . route('single_property', $idreg->id) . "'>". $idreg->tipo_imovel ." de " . $area . "m² em " . $idreg->cidade_campo . "</a></h3>
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
                                        <span>" . ((!$idreg->area_privativa) ? "Não Informado" : ($idreg->area_privativa . "m²")) . "</span>
                                    </li>
                                    <li>
                                        <i class='fas fa-warehouse' aria-hidden='true'></i>
                                        <span>" . ((!$idreg->garagem ) ? "Não Informado" : ($idreg->garagem . "Garagem")) . "</span>
                                    </li>
                                </ul>
                                <!-- Price -->
                                <div class='price-properties'>
                                    <h3 class='title mt-3'>
                                        <a href='" . route('single_property', $idreg->id) . "'>R$ " . number_format($idreg->preco,2,',','.') . "</a>
                                    </h3>
                                </div>
                                <div class='footer'>
                                    <i class='fas fa-gavel'></i> <a href='" . route('single_property', $idreg->id) . "'> Visitar imóvel </a>
                                    &nbsp;&nbsp;&nbsp;&nbsp;
                                    <i class='fas fa-map-marker d-none d-md-inline-block'></i><a style='cursor:pointer' class='d-none d-md-inline-block' onclick='visao(" . $idreg->lat . "," . $idreg->lng . ")'>Ver Street View</a>
                                    <div class='proposta'>
                                        <a href='" . route('single_property', $idreg->id) . "' style='color:#EB5E28;'>FAÇA SUA PROPOSTA</a>
                                    </div>
                                
                                </div>
                            </div>
                        </div>";
            
            endforeach;


    		//echo $html;

    	endif;

    	//return response()->json(["Imóveis não encontrados"], 404);

    }


    public function ajaxReloadBound(Request $request)
    {
       $this->validate($request, [
            'lat_start' => 'numeric',
            'lat_end'   => 'numeric',
            'lng_start' => 'numeric',
            'lng_end'   => 'numeric'
        ]);
        
        
        $sql =  "SELECT * FROM  
                imovels 
            WHERE 
                lat <= " . $request['lat_start'] . " AND 
                lat >= " . $request['lat_end'] . " AND 
                lng <= " . $request['lng_start'] . " AND 
                lng >= " . $request['lng_end'];

        $imoveisDrag = \DB::select($sql);


        $html = "";



        if($imoveisDrag):

            $contador = 0;

            foreach($imoveisDrag as $idreg):
                

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
                                <h3 style='font-size:11px !important'><a href='" . route('single_property', $idreg->id) . "' style='font-size:14px !important'>". $idreg->tipo_imovel .", " . trim($idreg->bairro) . $hifen . $idreg->cidade_campo . "</a></h3>
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
                                        <span>" . ((!$idreg->area_total) ? "Não Informado" : ($idreg->area_total . "m²"))  . "</span>
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
                                    <div class='preco_antigo' style='padding-bottom:20px !important'>
                                        <a><del>R$ " . number_format($idreg->valor_avaliacao,2,".",",") . "</del></a>
                                    </div>
                       
                                </div>
                            </div>
                        </div>";
                $contador++;
            endforeach;

        else:
            $html = "Nenhum imóvel encontrado!!!";
        endif;


        echo $html . "|___|" . $contador;
    }
}
