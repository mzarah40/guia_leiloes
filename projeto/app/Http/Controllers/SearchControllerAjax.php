<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Imovel;
use App\Models\Aviseme;

class SearchControllerAjax extends Controller
{
    private $primeiroRegistro = 0;
    private $pg = 1;
    //
    public function searchAjax(Request $request)
    {
        $count = "SELECT COUNT('*') AS total FROM imovels WHERE id != '' ";
        $query = "SELECT * FROM imovels WHERE id != '' ";
        if(isset($request['preco_min']) && $request['preco_min'] != "")
        {
            $query .= " AND preco >= " . $this->formataDecimal($request['preco_min']);
            $count .= " AND preco >= " . $this->formataDecimal($request['preco_min']);
        }
        if(isset($request['preco_max']) && $request['preco_max'] != "")
        {
            $query .= " AND preco <= " . $this->formataDecimal($request['preco_max']);
            $count .= " AND preco <= " . $this->formataDecimal($request['preco_max']);
        }
        if(isset($request['tipo_imovel']) && $request['tipo_imovel'] != "")
        {
            $query .= " AND tipo_imovel LIKE '%" . $request['tipo_imovel'] . "%'";
            $count .= " AND tipo_imovel LIKE '%" . $request['tipo_imovel'] . "%'";
        }
        if(isset($request['cidade']) && $request['cidade'] != "")
        {
            $query .= " AND cidade LIKE '%" . trim(urldecode($request['cidade'])) . "%'";
            $count .= " AND cidade LIKE '%" . trim(urldecode($request['cidade'])) . "%'";
        }
        if(isset($request['financiamento']) && $request['financiamento'] != "")
        {
            $query .= " AND financiamento LIKE '" . $request['financiamento'] . "%'";
            $count .= " AND financiamento LIKE '" . $request['financiamento'] . "%'";
        }
        if(isset($request['fgts']) && $request['fgts'] != "")
        {
            $query .= " AND fgts LIKE '" . $request['fgts'] . "%'";
            $count .= " AND fgts LIKE '" . $request['fgts'] . "%'";
        }
        if(isset($request['desconto_min']) && $request['desconto_min'] != "")
        {
            $query .= " AND desconto >= " . $this->formataDecimal($request['desconto_min']);
            $count .= " AND desconto >= " . $this->formataDecimal($request['desconto_min']);
        }
        if(isset($request['desconto_max']) && $request['desconto_max'] != "")
        {
            $query .= " AND desconto <= " . $this->formataDecimal($request['desconto_max']);
            $count .= " AND desconto <= " . $this->formataDecimal($request['desconto_max']);
        }
        if(isset($request['area_total_min']) && $request['area_total_min'] != "")
        {
            $query .= " AND area_total >= " . $this->formataDecimal($request['area_total_min']);
            $count .= " AND area_total >= " . $this->formataDecimal($request['area_total_min']);
        }
        if(isset($request['area_terreno_min']) && $request['area_terreno_min'] != "")
        {
            $query .= " AND area_terreno >= " . $this->formataDecimal($request['area_terreno_min']);
            $count .= " AND area_terreno >= " . $this->formataDecimal($request['area_terreno_min']);
        }
        if(isset($request['bairro']) && $request['bairro'] != "")
        {
            $query .= " AND (bairro LIKE '%" . trim(urldecode($request['bairro'])) . "%' OR bairro_campo LIKE '%" . urldecode($request['bairro']) . "%')";
            $count .= " AND (bairro LIKE '%" . trim(urldecode($request['bairro'])) . "%' OR bairro_campo LIKE '%" . urldecode($request['bairro']) . "%')";
        }
        if(isset($request['estado']) && $request['estado'] != "")
        {
            $query .= " AND (estado LIKE '%" . trim(urldecode($request['estado'])) . "%' OR estado_campo LIKE '%" . urldecode($request['estado']) . "%')";
            $count .= " AND (estado LIKE '%" . trim(urldecode($request['estado'])) . "%' OR estado_campo LIKE '%" . urldecode($request['estado']) . "%')";            
        }
        if(isset($request['modalidade']) && $request['modalidade'] != "")
        {
            $query .= " AND modalidade_venda LIKE '" . trim($request['modalidade']) . "%'";
            $count .= " AND modalidade_venda LIKE '" . trim($request['modalidade']) . "%'";
        }
        if(isset($request['situacao']) && $request['situacao'] != "")
        {
            $query .= " AND situacao_imovel LIKE '" . $request['situacao'] . "%'";
            $count .= " AND situacao_imovel LIKE '" . $request['situacao'] . "%'";
        }
        if(isset($request['parcelamento']) && $request['parcelamento'] != "")
        {
            $query .= " AND parcelamento LIKE '" . $request['parcelamento'] . "%'";
            $count .= " AND parcelamento LIKE '" . $request['parcelamento'] . "%'";
        }
        if(isset($request['consorcio']) && $request['consorcio'] != "")
        {
            $query .= " AND consorcio LIKE '" . $request['consorcio'] . "%'";
            $count .= " AND consorcio LIKE '" . $request['consorcio'] . "%'";
        }
        if(isset($request['judicial']) && $request['judicial'] != "")
        {
            $query .= " AND acao_judicial LIKE '" . $request['judicial'] . "%'";
            $count .= " AND acao_judicial LIKE '" . $request['judicial'] . "%'";
        }
        if(isset($request['area_privativa_min']) && $request['area_privativa_min'] != "")
        {
            $query .= " AND area_privativa >= '" . $this->formataDecimal($request['area_privativa_min']) . "%'";
            $count .= " AND area_privativa >= '" . $this->formataDecimal($request['area_privativa_min']) . "%'";
        }
        if(isset($request['area_privativa_max']) && $request['area_privativa_max'] != "")
        {
            $query .= " AND area_privativa <= '" . $this->formataDecimal($request['area_privativa_max']) . "%'";
            $count .= " AND area_privativa <= '" . $this->formataDecimal($request['area_privativa_max']) . "%'";
        }
        if(isset($request['order_by']) && $request['order_by'] != ""){
            
            switch($request['order_by']){
                case "1":
                    $query .= " ORDER BY desconto DESC ";
                    break;
                case "2":
                    $query .= " ORDER BY preco ASC ";
                    break;
                case "3":
                    $query .= " ORDER BY preco DESC ";
                    break;
                case "4":
                    $query .= " ORDER BY cidade ASC ";
                    break;
                default:
                    $query .= " ORDER BY desconto DESC ";
                    break;
            }

        }
        if(isset($request['pg']) && $request['pg'] != "" && $request['pg'] > 0)
        {
            $this->primeiroRegistro = ($request['pg'] * 50) - 50;
        }else{
            $this->primeiroRegistro = 0;
        }



        $query .= " LIMIT " . $this->primeiroRegistro . ", 50";

        $resultSet = \DB::select($query);

        $countResultSet = \DB::select($count);

        foreach($countResultSet as $res):
            $countRegistros = $res->total;
        endforeach;


        if(!$countRegistros){
            $countRegistros = 0;
        }



        if($countRegistros > ($this->primeiroRegistro + 50)){
            $btn_plus = "block";
        }else{
            $btn_plus = "none";
        }

        $html = "";


        if($resultSet):

            $idButton = 0;
            foreach($resultSet as $idreg):
                
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
                                <h3 style='font-size:11px !important'><a href='" . route('single_property', $idreg->id) . "'  style='font-size:14px !important'>". $idreg->tipo_imovel ." , " . trim($idreg->bairro) . $hifen . $idreg->cidade_campo . "</a></h3>
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
                                    <div class='preco_antigo' style='padding-bottom:20px !important'>
                                        <a><del>R$ " . number_format($idreg->valor_avaliacao,2,".",",") . "</del></a>
                                    </div>
                                </div>
                            </div>
                        </div>";
                    $idButton++;
            endforeach;

            $html .= "|___|" . $countRegistros . "|___|" . $btn_plus;
        else:

            $html = "Não foram encontrados imóveis com o seu filtro.|___|0";

        endif;

        echo $html;

    }

    private function formataDecimal($numero)
    {
        $numero = str_replace('.', '', $numero);
        $numero = str_replace(',', '.', $numero);

        return $numero;
    }



    // GRAVA NO BANCO A PESQUISA DE AVISE-ME

    public function saveSearch(Request $request)
    {

        $this->validate($request, [
            'email' => 'email|min:3|max:191|required'
        ]);

        $query = "SELECT * FROM imovels WHERE id != ''";

        if(isset($request['preco_min']) && $request['preco_min'] != "")
        {
            $query .= " AND preco >= " . $this->formataDecimal($request['preco_min']);
            
        }
        if(isset($request['preco_max']) && $request['preco_max'] != "")
        {
            $query .= " AND preco <= " . $this->formataDecimal($request['preco_max']);
            
        }
        if(isset($request['tipo_imovel']) && $request['tipo_imovel'] != "")
        {
            $query .= " AND tipo_imovel LIKE '%" . $request['tipo_imovel'] . "%'";
            
        }
        if(isset($request['cidade']) && $request['cidade'] != "")
        {
            $query .= " AND cidade LIKE '%" . trim(urldecode($request['cidade'])) . "%'";
            
        }
        if(isset($request['financiamento']) && $request['financiamento'] != "")
        {
            $query .= " AND financiamento LIKE '" . $request['financiamento'] . "%'";
            
        }
        if(isset($request['fgts']) && $request['fgts'] != "")
        {
            $query .= " AND fgts LIKE '" . $request['fgts'] . "%'";
            
        }
        if(isset($request['desconto_min']) && $request['desconto_min'] != "")
        {
            $query .= " AND desconto >= " . $this->formataDecimal($request['desconto_min']);
            
        }
        if(isset($request['desconto_max']) && $request['desconto_max'] != "")
        {
            $query .= " AND desconto <= " . $this->formataDecimal($request['desconto_max']);
            
        }
        if(isset($request['area_total_min']) && $request['area_total_min'] != "")
        {
            $query .= " AND area_total >= " . $this->formataDecimal($request['area_total_min']);
            
        }
        if(isset($request['area_terreno_min']) && $request['area_terreno_min'] != "")
        {
            $query .= " AND area_terreno >= " . $this->formataDecimal($request['area_terreno_min']);
            
        }
        if(isset($request['bairro']) && $request['bairro'] != "")
        {
            $query .= " AND (bairro LIKE '%" . trim(urldecode($request['bairro'])) . "%' OR bairro_campo LIKE '%" . urldecode($request['bairro']) . "%')";
            
        }
        if(isset($request['estado']) && $request['estado'] != "")
        {
            $query .= " AND (estado LIKE '%" . trim(urldecode($request['estado'])) . "%' OR estado_campo LIKE '%" . urldecode($request['estado']) . "%')";          
        
        }
        if(isset($request['modalidade']) && $request['modalidade'] != "")
        {
            $query .= " AND modalidade_venda LIKE '" . trim($request['modalidade']) . "%'";
            
        }
        if(isset($request['situacao']) && $request['situacao'] != "")
        {
            $query .= " AND situacao_imovel LIKE '" . $request['situacao'] . "%'";
            
        }
        if(isset($request['parcelamento']) && $request['parcelamento'] != "")
        {
            $query .= " AND parcelamento LIKE '" . $request['parcelamento'] . "%'";
            
        }
        if(isset($request['consorcio']) && $request['consorcio'] != "")
        {
            $query .= " AND consorcio LIKE '" . $request['consorcio'] . "%'";
            
        }
        if(isset($request['judicial']) && $request['judicial'] != "")
        {
            $query .= " AND acao_judicial LIKE '" . $request['judicial'] . "%'";
            
        }
        if(isset($request['area_privativa_min']) && $request['area_privativa_min'] != "")
        {
            $query .= " AND area_privativa >= '" . $this->formataDecimal($request['area_privativa_min']) . "%'";
        
        }
        if(isset($request['area_privativa_max']) && $request['area_privativa_max'] != "")
        {
            $query .= " AND area_privativa <= '" . $this->formataDecimal($request['area_privativa_max']) . "%'";

        }
        
        if(isset($request['order_by']) && $request['order_by'] != ""){
            
            switch($request['order_by']){
                case "1":
                    $query .= " ORDER BY desconto DESC ";
                    break;
                case "2":
                    $query .= " ORDER BY preco ASC ";
                    break;
                case "3":
                    $query .= " ORDER BY preco DESC ";
                    break;
                case "4":
                    $query .= " ORDER BY cidade ASC ";
                    break;
                default:
                    $query .= " ORDER BY desconto DESC ";
                    break;
            }
            
        }

        $email = $request['email'];

        $aviseMe           = new Aviseme();
        $aviseMe->consulta = $query;
        $aviseMe->email    = $email;
        // status 1 = enviado, 0 = não enviado
        $aviseMe->status   = "0";
        $res               = $aviseMe->save();

        if($res):
            return response()->json(['status' => '1', 'msg' => 'Cadastrado com Sucesso.']);
        else:
            return response()->json(['status' => '0', 'msg' => 'Erro ao cadastrar. Tente novamente mais tarde.']);
        endif;
    }
}
