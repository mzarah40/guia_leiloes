<?php

namespace App\Classes;

use GuzzleHttp;
use GuzzleHttp\Client;
use App\Models\Imovel;

class GetImoveisRaspagem {
    //put your code here
    
    private $jaTemNoBanco = false;
    private $arrayBanco = [];
    private $arrayCef = [];
   


    public function __construct(){
        $res = Imovel::all();
        foreach($res as $r):
            $this->arrayBanco[] = $r['id_imovel'];
        endforeach;
    }  
    
    
    public function getImoveisArray($uf="SP",$codigo="222842546"){
        
         
        
        $fileFonte = "https://venda-imoveis.caixa.gov.br/listaweb/Lista_imoveis_{$uf}.htm?{$codigo}";

        echo $fileFonte;
           
        
        //$fileCrawler = file_get_contents($fileFonte);


        $ch0 = curl_init($fileFonte);
        curl_setopt($ch0, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch0, CURLOPT_BINARYTRANSFER, true);
        $fileCrawler = curl_exec($ch0);
        curl_close($ch0);


        /*if(!$fileCrawler):
            exit;
        endif;*/
         
        libxml_use_internal_errors(true);

        /* CURL */
        ini_set('allow_url_fopen',1);
        
        $All = [];

        $dom = new \DOMDocument();
        $dom->preserveWhiteSpace = false; 
        $dom->strictErrorChecking = false; 
        $dom->recover = true;
        $dom->loadHTML($fileCrawler);
        
        $tables = $dom->getElementsByTagName('table');
        $tr     = $dom->getElementsByTagName('tr'); 

  
        $i = 0;
        //$kk = 0;
        foreach($tr as $element1):        

            echo $i . "<br>";
            
           if($i >= 1):

                /* DADOS DA TABELA  */


                $ARRAY['endereco_imovel']  = $element1->getElementsByTagName('td')->item(1)->textContent;                  // To fetch name

                $ARRAY['bairro']           = trim($element1->getElementsByTagName('td')->item(2)->textContent);                  // To fetch height
                $ARRAY['descricao']        = trim($element1->getElementsByTagName('td')->item(3)->textContent);                  // To fetch weight
                
                $tipo_imovel               = trim($element1->getElementsByTagName('td')->item(3)->textContent);
                $tipo_imovel               = explode(",", $tipo_imovel);
                $ARRAY['tipo_imovel']      = trim($tipo_imovel[0]);
                
                
                $preco                     = trim($element1->getElementsByTagName('td')->item(4)->textContent);                  // To fetch date
                $preco                     = str_replace(".", "", $preco);
                $preco                     = str_replace(",", ".", $preco);
                $ARRAY['preco']            = $preco;
                $valor_avaliacao           = trim($element1->getElementsByTagName('td')->item(5)->textContent); 
                $valor_avaliacao           = str_replace(".", "", $valor_avaliacao);                                       // To fetch infos
                $valor_avaliacao           = str_replace(",", ".", $valor_avaliacao);
                $ARRAY['valor_avaliacao']  = trim($valor_avaliacao);                                                             // To fetch info
                $desconto                  = trim($element1->getElementsByTagName('td')->item(6)->textContent); 
                //$desconto                  = str_replace(".", "", $desconto);
                //$desconto                  = str_replace(",", ".", $desconto);
                $ARRAY['desconto']         = $desconto;
                
                
                $ARRAY['modalidade_venda'] = trim($element1->getElementsByTagName('td')->item(7)->textContent);
                $ARRAY['cidade']           = trim($element1->getElementsByTagName('td')->item(9)->textContent);
                $ARRAY['estado']           = trim(strtoupper($element1->getElementsByTagName('td')->item(10)->textContent));

                
                echo "Endere??o do Imovel: " . $ARRAY['endereco_imovel'] . "<br>";
                echo "Bairro do Imovel: " . $ARRAY['bairro'] . "<br>";
                echo "Descri????o do Imovel: " . $ARRAY['descricao'] . "<br>";
                echo "Pre??o do Imovel: " . $ARRAY['preco'] . "<br>";
                echo "Valor do Imovel: " . $ARRAY['valor_avaliacao'] . "<br>";
                echo "Desconto: " . $ARRAY['desconto'] . "<br>";
                echo "Modalidade de Venda: " . $ARRAY['modalidade_venda'] . "<br>";
                echo "Cidade: " . $ARRAY['cidade'] . "<br>";
                echo "Estado: " . $ARRAY['estado'] . "<br>";
                echo "<hr>";

                

                $link = $element1->getElementsByTagName('td')->item(0)->getElementsByTagName('a');   // To fetch user link


                //$kk = 0;

                foreach($link as $l):


                    /*  ACESSANDO A P??GINA DE DESCRI????O, DADOS DO LINK DA TABELA */


                    $url = $l->getAttribute('href');
                    //$url = str_replace('&amp;', '%26', $url);
                    $url = str_replace("amp;", "", $url);
                    $url = html_entity_decode($url);
                    
                    

                    
                    /* PEGANDO O ID DO IM??VEL */

                    $idImovel = explode("=", $url);
                    $idImovel = trim($idImovel[2]);

                    if(in_array($idImovel,$this->arrayBanco)):
                        $this->jaTemNoBanco = true;
                        echo "<p>J?? tem no banco</p>";
                        continue;
                    else:
                        $this->jaTemNoBanco = false;
                        echo "<p>N??o tem no banco</p>";
                    endif;


                    
                    $url = html_entity_decode("https://venda-imoveis.caixa.gov.br/sistema/detalhe-imovel.asp?hdnOrigem=index&hdnimovel=" . $idImovel);

                    
                    echo $url . "<br><br>";
                    
                    /* ARRAY CEF PARA FAZER O DIFF NO FINAL E DELETAR OS ARQUIVOS QUE J?? N??O EST??O MAIS NA CEF 
                     * ESTE ARRAY CONTEM OS IDs DOS IMOVEIS QUE TEM NA CAIXA 
                     *
                     */
                    $this->arrayCef[] = $idImovel;
                    
                    
                    /* SE IMOVEL J?? CONSTAR NO BANCO N??O INSERE */
                   // $result = Imovel::where('id_imovel', $idImovel)->where('origem_name', 'cef')->get();
                      
                               
                    /* SE IMOVEL CONSTAR NO BANCO SAI DO LOOP E COME??A NOVAMENTE */
                    /*
                    if($result):
                        $this->jaTemNoBanco = true;
                        continue;
                    else:
                        $this->jaTemNoBanco = false;
                    endif;
                    */
                    
                    

                    $ARRAY['id_imovel'] = trim($idImovel);

                    /*  PEGANDO A P??GINA DE DESCRI????O */
                    //$html = file_get_contents($url);

                    $ch = curl_init($url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
                    $html = curl_exec($ch);
                    curl_close($ch);
                    


                    

                    $dom2 = new \DOMDocument;
                    $dom2->preserveWhiteSpace = false; 
                    $dom2->strictErrorChecking = false; 
                    $dom2->recover = true;
                    $dom2->loadHTML($html);

                    $element2  = $dom2->getElementById("dadosImovel");

                    if($element2):
                        $links_doc = $element2->getElementsByTagName("a");
                    else:
                        $links_doc = [];
                    endif;

                    //echo "<hr>";
                    /*  LINKS PARA EDITAIS E MATRICULA */



                    $ARRAY['matricula_link'] = "";
                    $ARRAY['edital_link'] = "";
                    $h=0;
                    foreach($links_doc as $val):
                        $LINKS = $val->getAttribute("onclick");
                        if(strpos($LINKS, "'")):
                            $LINKS = explode("'", $LINKS);
                            $LINKS = $LINKS[1];
                        endif;


                        if($LINKS && strpos($LINKS, 'editais') && !strpos($LINKS, 'matricula')):
                            $ARRAY['edital_link'] = "https://venda-imoveis.caixa.gov.br" . $LINKS;
                        endif;

                        if($LINKS && strpos($LINKS, 'matricula') && strpos($LINKS, 'editais')):
                            $ARRAY['matricula_link'] = "https://venda-imoveis.caixa.gov.br" . $LINKS;
                        endif;


                        /*
                        if($LINKS && strpos($LINKS, 'editais') && strpos($LINKS, 'matricula')):
                            $ARRAY['matricula_link'] = "https://venda-imoveis.caixa.gov.br" . $LINKS;
                        else:
                            $ARRAY['matricula_link'] = "";
                        endif;
                        if($LINKS && strpos($LINKS, 'editais') && !strpos($LINKS, 'matricula')):
                            $ARRAY['edital_link'] = "https://venda-imoveis.caixa.gov.br" . $LINKS;
                        else:
                            $ARRAY['edital_link'] = "";
                        endif;
                        //echo "<br>";
                        */
                        $h++;
                    endforeach;


                    $finder = new \DomXPath($dom2);
                    // buscando os paragrafos dentro da div de classe related-box
                    $dom_related   = $finder->query('//div[@class="related-box"]/p');


                    /*  ENDERE??O PARA GOOGLE MAPS */

                    if(isset($dom_related->item(0)->nodeValue)):
                        $textoEndereco = $dom_related->item(0)->nodeValue;

                        /* ENDERE??O PARA BANCO DE DADOS */

                        $endereco0 = explode(":", $textoEndereco);


                        $logradouro0 = explode(",", $endereco0[1]);
                        if(isset($logradouro0[0]) && isset($logradouro0[1])):
                            $logradouroBanco = $logradouro0[0] . ", " . $logradouro0[1];
                        else:
                            $logradouroBanco = $ARRAY['endereco_imovel'];
                        endif;

                        $ARRAY['endereco_campo'] = trim($logradouroBanco);

                        //$bairro0 = explode(":", $textoEndereco);
                        //$bairro0 = explode(",", $bairro0[2]);
                        //$bairro0 = explode("-", $bairro0[1]);
                        //$bairroBanco = $bairro0[0];

                        $ARRAY['bairro_campo'] = $ARRAY['bairro'];

                        $cep0 = explode(",", $endereco0[2]);
                        $cepBanco = $cep0[0];
                        $ARRAY['cep_campo'] = trim($cepBanco);

                        if(isset($cep0[1])):
                            $cidade0 = explode("-", $cep0[1]);
                            $cidadeBanco = $cidade0[0];
                        else:
                            $cidadeBanco = $ARRAY['cidade'];
                        endif;
                        $ARRAY['cidade_campo'] = trim($cidadeBanco);

                        $estadoBanco = strtoupper($uf);
                        $ARRAY['estado_campo']  = trim($estadoBanco);



                        //echo $textoEndereco . "<br>";

                        /* ENDERE??O PARA O GOOGLE MAPS */


                        $textoEndereco = explode(":", $textoEndereco);
                        $parte1 = explode(",", $textoEndereco[1]);

                        /* Pegando o endere??o */
                        $logradouro    = $parte1[0];

                        $numero = "";

                        /* Pegando o numero */
                        if(isset($parte1[1])):
                            $numero = explode(" ", $parte1[1]);
                            if(isset($numero[1])):
                                $numero = trim($numero[1]);
                            else:
                                $numero = "";
                            endif;
                        else:
                            $numero = "";
                        endif;


                        /* Pegando o CEP */
                        $cep = explode(",", $textoEndereco[2]);
                        $cep = $cep[0];

                        $numero = preg_replace("/[^0-9]/", "", $numero);
                        $inteiro = (int) $numero;

                        if(!is_integer($inteiro)):
                            $numero = "";
                        endif;
                        
                        $ARRAY['numero_campo'] = trim($numero);

                        //echo "Endere??o:" . $logradouro . ", " . $numero . " - " . $cep . "<br>";

                        if($numero != ""):
                            $enderecoGoogleMaps = $logradouro . ", " . $numero . ", " . $ARRAY['bairro_campo']. " - " . $cep . ", " . $cidadeBanco . " - " . $estadoBanco . ", Brasil";
                        else:
                            $enderecoGoogleMaps = $logradouro . ", " . $ARRAY['bairro_campo'] . " - " . $cep . ", " . $cidadeBanco . " - " . $estadoBanco . ", Brasil";
                        endif;
                        //$enderecoGoogleMaps = urlencode($enderecoGoogleMaps);

                        $ARRAY['endereco_google'] = $enderecoGoogleMaps;

                        $enderecoGoogleMaps = urlencode($enderecoGoogleMaps);

                        //$enderecoGoogleMaps = urlencode($enderecoGoogleMaps);
                        
                        
                        //dd($enderecoGoogleMaps);

                        /* SE TEM NO BANCO N??O INSERE NOVAMENTE */

                        if($this->jaTemNoBanco == false):
                        
                           $CLIENTEGUZZLE = new \GuzzleHttp\Client();
                            
                           //  <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDqCZkI1_Fla820ama0Pz6Mk33XEf_omAE&callback=initMap&libraries=&v=weekly"></script>
                        
                            // loft - aten????o trocar esta chave quando for para o cliente
                            //$RESULTADO = (string) $CLIENTEGUZZLE->get("https://maps.googleapis.com/maps/api/geocode/json?address={$enderecoGoogleMaps}&key=AIzaSyCARa0RvtRVxpc2YU3b2G8Uh0XC9bDHcxM&sensor=false")->getBody();
                        
                            $RESULTADO =(string) $CLIENTEGUZZLE->get("https://maps.googleapis.com/maps/api/geocode/json?sensor=false&address=$enderecoGoogleMaps&key=AIzaSyDqCZkI1_Fla820ama0Pz6Mk33XEf_omAE")->getBody();
                            //$RESULTADO = (string) $CLIENTEGUZZLE->get("https://maps.googleapis.com/maps/api/geocode/json?sensor=false&address=$enderecoGoogleMaps&key=AIzaSyBoaF4xryHDriMAXeDjjVHjKgdQzzLXUKE")->getBody();
                            //$RESULTADO = (string) $CLIENTEGUZZLE->post("https://googleapis.com/geolocation/v1/son?address=$enderecoGoogleMaps",  [ "form_params" => ["key"=>"AIzaSyDqCZkI1_Fla820ama0Pz6Mk33XEf_omAE"]])->getBody();

                               
                            // $RESULTADO = (string) @file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address={$enderecoGoogleMaps}&key=AIzaSyDqCZkI1_Fla820ama0Pz6Mk33XEf_omAE&sensor=false")  ;                                                  

                            $json =json_decode($RESULTADO);

                            
                            
                            
                            try{
                                $LATITUDE  = $json->results[0]->geometry->location->lat;
                                $LONGITUDE = $json->results[0]->geometry->location->lng;
                            }catch(\Exception $e){
                                $LATITUDE = 0;
                                $LONGITUDE = 0;
                            }

                        

                            //echo "Latitude:" . $LATITUDE . "<br>";
                            //echo "Longitude:" . $LONGITUDE . "<br>";
                        




                            $ARRAY['lat'] = $LATITUDE;
                            $ARRAY['lng'] = $LONGITUDE;
                           

                            /*break;*/
                            
                         endif;
                        
                        //echo "Latitude: " . $ARRAY['lat'] . "<br>";
                        //echo "Longitude: " . $ARRAY['lng'] . "<br><hr>";

                    endif;

                    /* A????O JUDICIAL */

                    $ARRAY['acao_judicial'] = "N??O";

                    if(isset($dom_related->item(1)->nodeValue)):
                       $textoAcao = $dom_related->item(1)->nodeValue;
                       if(strpos($textoAcao, "A????O JUDICIAL") || strpos($textoAcao, "A????o Judicial") || strpos($textoAcao, "a????o judicial") || strpos($textoAcao, "COM A????O JUDICIAL") || strpos($textoAcao, "CONSTA A????O JUDICIAL")){
                            //$textoAcaoJudicial = "SIM";
                            //echo "A????o Judicial: " . $textoAcaoJudicial . "<br>";
                            $ARRAY['acao_judicial'] = "SIM";
                        }else{
                            $textoAcaoJudicial = "N??O";
                            $ARRAY['acao_judicial'] = "N??O";
                        }
                    else:
                        $ARRAY['acao_judicial'] = "N??O";
                    endif;

                    /*  MATRICULA */


                    if(isset($dom_related->item(1)->nodeValue)):
                        $textoMatricula = $dom_related->item(1)->nodeValue;
                        $ARRAY['matricula'] = $textoMatricula;
                    endif;


                    /* FGTS, FINANCIAMENTO, PARCELAMENTO, CONSORCIO*/

                    $ARRAY['fgts'] = "";
                    $ARRAY['financiamento'] = "";
                    $ARRAY['parcelamento'] = "";
                    $ARRAY['consorcio'] = "";
                    





                   // $Teste = $dom_related->item(2)->nodeValue;
                   // echo "<hr>$Teste</hr>";









                    if(isset($dom_related->item(2)->nodeValue)):
                        $textoFgts = $dom_related->item(2)->nodeValue;
                        $txtFgts = explode(".", $textoFgts);
                        // $fgts come??a em 1 pois tem a palavra Descri????o:. antes
                        for($w=0;$w<=3;$w++):
                            if($w == 0):
                                if(isset($txtFgts[$w])):
                                    if(strpos($txtFgts[$w], "N??O")):
                                        $FGTS = "N??O";
                                        $ARRAY['fgts'] = trim($FGTS);
                                    else:
                                        $FGTS = "SIM";
                                        $ARRAY['fgts'] = trim($FGTS);
                                    endif;
                                else:
                                    $ARRAY['fgts'] = "";
                                endif;
                            endif;
                            if($w == 1):
                                if(isset($txtFgts[$w])):
                                    if(strpos($txtFgts[$w], "N??O")):
                                        $FINANCIAMENTO = "N??O";
                                        $ARRAY['financiamento'] = $FINANCIAMENTO;
                                    else:
                                        $FINANCIAMENTO = "SIM";
                                        $ARRAY['financiamento'] = $FINANCIAMENTO;
                                    endif;
                                else:
                                    $ARRAY['financiamento'] = "";
                                endif;
                            endif;
                            if($w == 2):
                                if(isset($txtFgts[$w])):
                                    if(strpos($txtFgts[$w], "N??O")):
                                        $PARCELAMENTO = "N??O";
                                        $ARRAY['parcelamento'] = $PARCELAMENTO;
                                    else:
                                        $PARCELAMENTO = "SIM";
                                        $ARRAY['parcelamento'] = $PARCELAMENTO;
                                    endif;
                                else:
                                    $ARRAY['parcelamento'] = "";
                                endif;
                            endif;
                            if($w == 3):
                                if(isset($txtFgts[$w])):
                                    if(strpos($txtFgts[$w], "N??O")):
                                        $CONSORCIO = "N??O";
                                        $ARRAY['consorcio'] = $CONSORCIO;
                                    else:
                                        $CONSORCIO = "SIM";
                                        $ARRAY['consorcio'] = $CONSORCIO;
                                    endif;
                                else:
                                    $ARRAY['consorcio'] = "";
                                endif;
                            endif;

                        endfor;
                    endif;




                    $dom3 = new \DOMDocument;
                    $dom3->preserveWhiteSpace = false; 
                    $dom3->strictErrorChecking = false; 
                    $dom3->recover = true;
                    $dom3->loadHTML($html);

                    $finder2 = new \DomXPath($dom3);

                    // buscando o que tem dentro da div classe content
                    $dom_node_list = $finder2->query('//div[@class="content"]');


                    //$texto = (string) $finder->query('//div[@class="content"]');
                    if($dom_node_list):
                        echo "<pre>";
                        var_dump($dom_node_list);
                        echo "</pre>";
                    else:
                        echo "<p>N??o est?? buscando a div content.</p>";
                    endif;
                   

                    $j = 0;
                    foreach($dom_node_list as $value):

                        $texto = (string) strip_tags($dom_node_list->item($j)->nodeValue);

                        //$texto = (string) $dom_node_list->item($j)->nodeValue;
                        //echo "<p>".$texto."</p>";
                        //echo "<br>";

                        
                        $txt = explode("Tipo de im??vel", $texto);

                        $vetTxt = explode(":", $txt[1]);
                        
                        /*
                        // tipo de im??vel 
                        //echo $vetTxt[1];
                        //echo "<br>";
                        $tipo_imovel = trim(str_replace("Situa????o", "", $vetTxt[1]));
                        //echo $tipo_imovel;
                        $ARRAY['tipo_imovel'] = $tipo_imovel;
                        */

                        // se estiver ocupado ou desocupado [status do im??vel]

                        $situacao_imovel = "N??o Informado";

                        if(strpos( $texto,"Ocupado")):
                            $situacao_imovel = "Ocupado";
                        endif;

                        if(strpos($texto,"Desocupado")):
                            $situacao_imovel = "Desocupado";
                        endif;
                        


                        $ARRAY['situacao_imovel'] = $situacao_imovel;

                        


                        if(strpos($vetTxt[2], "Quartos")):
                            $quartos = preg_replace("/[^0-9]/", "", $vetTxt[3]);
                        else:
                            $quartos = 0;
                        endif;

                        $ARRAY['quartos'] = trim($quartos);

                        if(strpos($vetTxt[2], "Garagem")):
                            $garagem = preg_replace("/[^0-9]/", "", $vetTxt[3]);
                            $temGaragemAqui = true;
                        else:
                            $garagem = 0;
                            $temGaragemAqui = false;
                        endif;



                        if(isset($vetTxt[3]) && !$temGaragemAqui):

                            if(strpos($vetTxt[3], "Garagem")):
                                $garagem = preg_replace("/[^0-9]/", "", $vetTxt[4]);
                            else:
                                $garagem = 0;
                            endif;
                        else:
                            $garagem = 0;
                        endif;

                        $ARRAY['garagem'] = trim($garagem);


                        /*  AREA TOTAL , AREA PRIVATIVA, AREA DO TERRENO */

                        if(strpos($texto, "??rea total")):
                            $parts = explode("??rea total", $texto);
                            $parts = explode("m2", $parts[1]);
                            $txtAreaTotal = str_replace("=", "", $parts[0]);
                            $txtAreaTotal = str_replace(".", "", $txtAreaTotal);
                            $txtAreaTotal = str_replace(",", ".", $txtAreaTotal);
                            //$txtAreaTotal = trim(str_replace(",", "", $txtAreaTotal));
                            $txtAreaTotal = trim(str_replace("de ??rea total", "", $txtAreaTotal));
                            $ARRAY['area_total']  =  $txtAreaTotal;//preg_replace("/[^0-9]/", "", $txtAreaTotal);
                        else:
                            $txtAreaTotal = "0";
                            $ARRAY['area_total']  = $txtAreaTotal;
                        endif;


                        if(strpos($texto, "??rea privativa")):
                            $parts = explode("??rea privativa", $texto);
                            $parts = explode("m2", $parts[1]);
                            $txtAreaPrivativa = str_replace("=","",$parts[0]);
                            $txtAreaPrivativa = str_replace(".", "", $txtAreaPrivativa);
                            $txtAreaPrivativa = str_replace(",", ".", $txtAreaPrivativa);
                            //$txtAreaPrivativa = trim(str_replace(",", ".", $txtAreaPrivativa));
                            $txtAreaPrivativa = trim(str_replace("de ??rea privativa", "", $txtAreaPrivativa));
                            $ARRAY['area_privativa'] = $txtAreaPrivativa;//preg_replace("/[^0-9]/", "", $txtAreaPrivativa);
                        else:
                            $txtAreaPrivativa = "0";
                            $ARRAY['area_privativa'] = $txtAreaPrivativa;
                        endif;

                        if(strpos($texto, "??rea do terreno")):
                            $parts = explode("??rea do terreno", $texto);
                            $parts = explode("m2", $parts[1]);
                            $txtAreaDoTerreno = str_replace("=","",$parts[0]);
                            $txtAreaDoTerreno = str_replace(".","",$txtAreaDoTerreno);
                            $txtAreaDoTerreno = str_replace(",", ".", $txtAreaDoTerreno);
                            //$txtAreaDoTerreno = trim(str_replace(",", ".", $txtAreaDoTerreno));
                            $txtAreaDoTerreno = trim(str_replace("de ??rea do terreno", "", $txtAreaDoTerreno));
                            $ARRAY['area_terreno'] =  $txtAreaDoTerreno;//preg_replace("/[^0-9]/", "", $txtAreaDoTerreno);
                        else:
                            $ARRAY['area_terreno'] = 0;
                        endif;

                        

                    



                        
                  
                        //print_r($arraySave);
                        //echo "<hr>";
                  
                        
                        











                        $j++;
                    endforeach;


                    

                    /*

                    if($kk > 2):
                       break;
                   endif;

                    $kk++;
                    */





                endforeach; // FIM DO LINK


                
                /*
                $name       = $element1->getElementsByTagName('td')->item(0)->textContent;                  // To fetch name
                $height     = $element1->getElementsByTagName('td')->item(1)->textContent;                  // To fetch height
                $weight     = $element1->getElementsByTagName('td')->item(2)->textContent;                  // To fetch weight
                $date       = $element1->getElementsByTagName('td')->item(3)->textContent;                  // To fetch date
                $info       = $element1->getElementsByTagName('td')->item(4)->textContent;                  // To fetch info
                $country    = $element1->getElementsByTagName('td')->item(5)->textContent;                  // To fetch country

                array_push($All, array(
                    "user_link" => $link,
                    "name"      => $name,
                    "height"    => $height,
                    "weight"    => $weight,
                    "date"      => $date,
                    "info"      => $info,
                    "country"   => $country
                ));
                 * 
                 */
                //var_dump($ARRAY);
                //echo "<hr>";
                
                /* SE N??O TIVER NO BANCO */
                
                if($this->jaTemNoBanco == false):
                    $All[] = $ARRAY;


                    $ARRAY['origem_name'] = "cef";
                    $ARRAY['name']        = "Arrematador";
                    $ARRAY['user_id']     = "1";


                    $res = Imovel::create($ARRAY);
                    if($res):
                        echo "<p>Gravado com sucesso</p><hr>";
                    endif;
                endif;





                echo "<pre>";
                var_dump($ARRAY);
                echo "</pre>";
                echo "<hr>";
                
            endif; // fim de se $i >= 1 // tira o cabe??alho da tabelas

            /*
            if($kk > 30){
                break;
            } 
            $kk++;
            */
            
            $i++;
           
            // pulando para a proxima linha
            
          
           
        endforeach; // FIM DA TABELA DE CADA TR
        
        
        //return json_encode($All, JSON_PRETTY_PRINT);
        
        // retornando o array $All que cont??m outro array com os campos do banco de dados
        return $All;

    }
    
    
    public function fazDiff(){
        /*  O QUE TEM NO BANCO QUE N??O TEM NA CEF ?? PARA DELETAR */
        $naoTemMaisNaCef = array_diff($this->arrayBanco, $this->arrayCef);
        if(!empty($naoTemMaisNaCef)):
            foreach($naoTemMaisNaCef as $ID_IMOVEL):
                Imovel::where('id_imovel', $ID_IMOVEL)->where('origem_name','cef')->delete();
            endforeach;
        endif;

    }
}
