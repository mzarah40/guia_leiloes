<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Classes\GetImoveisRaspagem;
use App\Models\Imovel;
use App\Jobs\LerArquivoCefJob;
class GetImoveisController extends Controller
{

    public function gravaJobQueue(){
        $this->dispatch(new LerArquivoCefJob());
        return redirect()->route('home')->with('status', 'Tarefa agendada com Sucesso! Às 0:00 horas será inicializada o carregamento.');
    }

    // executado quando o robo passar
    public function index($uf="SP",$codigo="222842546"){
        
          ini_set('max_execution_time', 860000000000000000); // 156000 segundos

          $arrayEstados = ['SP','MG','ES','RJ','AC','AL','AP','AM','BA','CE','GO','MA','MT','MS','PA','PB','PR','PE','PI','RN','RS','RO','RR','SC','SE','TO','DF'];
        
          $arrayEstados = ['SP'];
          
          /* INICIANDO O OBJETO QUE NO CONSTRUCT COLOCA NUM ARRAY TODOS OS ID_IMOVEIS DO BANCO*/
          $Raspagem = new GetImoveisRaspagem();
          
          /* PEGANDO OS IMOVEIS POR ESTADO*/
          $imoveisNoEstado = 0;
          foreach($arrayEstados as $idx => $val):
            $response[$idx][$imoveisNoEstado] = $Raspagem->getImoveisArray($val);
            $imoveisNoEstado++;
          endforeach;
          
          
          /* SALVANDO CADA IMOVEL*/
          //$arraySave = [];
          









          /*




          $k = 0;
          
          for($i=0; $i<count($arrayEstados); $i++):
              
              

              $vetor = $response[$i][$k];
              foreach($vetor as $index => $value):
                  
                 foreach($value as $indice => $v):
                
                    //echo $indice . " = " . $v . "<br>";
                    $arraySave[$indice] = $v;
                    if($indice == 'endereco_google'):
                        $arraySave['address'] = $arraySave[$indice];
                    endif;
                 endforeach;

                
   
                //echo "<hr>";
                
                $arraySave['origem_name'] = "cef";
                $arraySave['name']        = "Arrematador";
                $arraySave['user_id']     = "1";
              
                //print_r($arraySave);
                //echo "<hr>";
              
               
               // Imovel::create($arraySave);

               
                
             // endforeach;
              

            //  $k++;
              
                    
          //endfor;
          */
          
          $Raspagem->fazDiff();
          
          
          unset($Raspagem);

          
          
           
    }
    
    public function getByUf($uf="SP"){
        $uf = $request->query('uf');
        $ufs = Imovel::where('estado_campo', '=', $uf)->get();
    }
}
