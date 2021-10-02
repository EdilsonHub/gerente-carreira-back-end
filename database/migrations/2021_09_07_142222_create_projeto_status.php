<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjetoStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projeto_status', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_projeto');
            $table->enum('status',['START','STOP','PAUSE']);
            $table->dateTime('data')->default(date('Y-m-d h:i:s'));
            $table->foreign('id_projeto')->references('id')->on('projetos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('projeto_status');
    }
}
