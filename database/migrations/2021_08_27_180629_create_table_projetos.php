<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableProjetos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projetos', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('nome');
            $table->string('descricao')->nullable();
            $table->unsignedBigInteger('id_projeto_pai')->nullable();
            $table->integer('nivel_projeto')->default(0);
            $table->dateTime('data_criacao')->default(date('Y-m-d h:i:s'));
            $table->bigInteger('custo_previsto')->default(0);
            $table->integer('local_de_realizacao_previsto')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('projetos');
    }
}