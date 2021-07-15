<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImovelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('imovels', function (Blueprint $table) {
            $table->id();
            $table->longText('endereco_imovel');
            $table->longText('bairro');
            $table->longText('descricao');
            $table->string('tipo_imovel');
            $table->decimal('preco',20,2);
            $table->decimal('valor_avaliacao',20,2);
            $table->decimal('desconto',20,2);
            $table->string('modalidade_venda')->nullable();
            $table->longText('cidade');
            $table->string('estado');
            $table->string('id_imovel')->nullable();
            $table->longText('matricula_link')->nullable();
            $table->longText('edital_link')->nullable();
            $table->longText('endereco_campo');
            $table->longText('bairro_campo');
            $table->string('cep_campo');
            $table->longText('cidade_campo');
            $table->string('estado_campo');
            $table->string('numero_campo')->nullable();
            $table->longText('endereco_google');
            $table->string('lat',100);
            $table->string('lng',100);
            $table->string('acao_judicial');
            $table->longText('matricula');
            $table->string('fgts');
            $table->string('financiamento');
            $table->string('parcelamento');
            $table->string('consorcio');
            $table->string('situacao_imovel');
            $table->integer('quartos')->nullable();
            $table->integer('garagem')->nullable();
            $table->double('area_total')->nullable();
            $table->double('area_privativa')->nullable();
            $table->double('area_terreno')->nullable();
            $table->string('origem_name')->nullable();
            $table->string('name')->nullable();
            $table->longText('address')->nullable();
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('imovels');
    }
}
