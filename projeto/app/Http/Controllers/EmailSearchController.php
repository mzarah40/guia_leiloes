<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Aviseme;

class EmailSearchController extends Controller
{
    private $imoveisAviso = [];
    //
    public function enviar()
    {

        $tabAviseMe = Aviseme::where('status','=','0')->get();

        
        if($tabAviseMe):

            // para cada registro dentro da tabela Aviseme que o status = 0 (não enviado)
            foreach($tabAviseMe as $aviso):


                // criando uma classe vazia para conter os campos do aviso acima
                $cliAviso        = new \stdClass();
                // colocando o e-mail do aviso na stdClass
                $cliAviso->email = $aviso->email;

                // fazendo a consulta com a SQL armazenada pela consulta do aviso
                if($aviso->consulta == "SELECT * FROM imovels WHERE id != ''"):
                    continue;
                endif;

                // consulta do aviso
                $resAviso        = \DB::select($aviso->consulta);


                // vetAviso vetor de imoveis(stdClass)
                $vetAviso[] = null;

                // se tiver aviso
                if($resAviso):
                    

                    // para cada aviso, puxar a row e inserir no vetor $vetAviso a classe $cliAviso
                    // que por sua vez recebe informações sobre seus imoveis
                    foreach($resAviso as $imovel):  
                        
                        $imovelAviso = new \stdClass();
                        $imovelAviso->endereco_imovel = $imovel->endereco_imovel;
                        $imovelAviso->id              = $imovel->id;

                        $vetAviso[] = $imovelAviso;

                    endforeach;

                endif;

                
                //$dispachante = new \App\Jobs\EmailSearchJob($cliAviso, $vetAviso);
                //$dispachante->dispatch($cliAviso, $vetAviso);
                
                \Illuminate\Support\Facades\Mail::queue(new \App\Mail\MailGeral($cliAviso, $vetAviso));  
                

            endforeach;


        else:

            echo "Nada na tabela avisime";
        
        endif;



        
    }
}
