<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Imovel extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'endereco_imovel',
        'bairro',
        'descricao',
        'tipo_imovel',
        'preco',
        'valor_avaliacao',
        'desconto',
        'modalidade_venda',
        'cidade',
        'estado',
        'id_imovel',
        'matricula_link',
        'edital_link',
        'endereco_campo',
        'bairro_campo',
        'cep_campo',
        'cidade_campo',
        'estado_campo',
        'numero_campo',
        'endereco_google',
        'lat',
        'lng',
        'acao_judicial',
        'matricula',
        'fgts',
        'financiamento',
        'parcelamento',
        'consorcio',
        'situacao_imovel',
        'quartos',
        'garagem',
        'area_total',
        'area_privativa',
        'area_terreno',
        'origem_name',
        'name',
        'address',
        'user_id'
    ];
}
