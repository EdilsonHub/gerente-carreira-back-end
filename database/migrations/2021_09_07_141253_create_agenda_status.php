<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgendaStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agenda_status', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_agenda');
            $table->enum('status',['START','STOP','PAUSE']);
            $table->dateTime('data')->default(date('Y-m-d h:i:s'));
            $table->foreign('id_agenda')->references('id')->on('agenda_realizacao_projeto');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('agenda_status');
    }
}
