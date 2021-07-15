<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeiloeirosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leiloeiros', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 255);
            $table->string('cpf', 255);
            $table->string('cnpj', 255)->nullable();
            $table->string('email')->nullable();
            $table->bigInteger('user_id')->unsigned();
            $table->string('creci')->nullable();
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
        Schema::dropIfExists('leiloeiros');
    }
}
