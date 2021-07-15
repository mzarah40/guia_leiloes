<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Imovel;
use Auth;
use App\Models\User;
use App\Models\Aviseme;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    private $userId = null;
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $imoveis = Imovel::all();
        return view('home', compact('imoveis'));
    }

    public function getButtonCharge()
    {
        return view('charge');
    }

    public function perfil(Request $request){

        $this->userId = Auth::user()->id;

        if($request['id'] != $this->userId):
            dd("Você não tem permissão para acessar este usuário");
        endif;

        $id = (int) $request['id'];

        if(!is_numeric($id))
        {
            dd('Erro: Não é um numero');
        }

 
        if(is_int($id) && $id != null):
            $user = User::find($id)->first();
        else:
            $user = null;
        endif;
        return view('perfil', compact('user'));
    }

    public function update(Request $request){
        $this->validate($request, [
            'email'                 => 'email|min:3|max:191|required',
            'name'                  => 'min:3|max:191|required',
            'cdg'                   => 'integer|min:1|max:20|required'
        ]);

        if($request['email'] != $request['email_old']):
            $result = User::where('email', '=', $request['email'])->first();
            if($result):
                dd("E-mail já existente");
            endif;
        endif;

        $user = User::find($request['cdg']);
        $user->name  = $request['name'];
        $user->email = $request['email'];
        if($request['password'] != "" && $request['password_confirmation'] != ""):
            if($request['password'] == $request['password_confirmation']):
                $user->password = bcrypt($request['password']);
            endif;
        endif;

        $response = $user->save();

        if($response){
            $msg = "Usuário Editado com Sucesso";
        }else{
            $msg = "Erro ao editar o Usuário. Tente mais tarde.";
        }

        return redirect()->route('home')->with('status', $msg);
    }

    public function listaDetalhada(int $id)
    {
        if(!is_numeric($id)){
            echo "Codigo tem que ser numérico";
            return false;
        }

        $imovel = Imovel::find($id);

        return view('lista_detalhada', compact('imovel'));
    }

    public function queuePayload(){

        $query = "SELECT exception FROM failed_jobs";

        $response = \DB::select($query);

        dd($response);
    }

    public function leads(){
        $leads = Aviseme::all();
        return view('content.aviseme', compact('leads'));
    }
}
