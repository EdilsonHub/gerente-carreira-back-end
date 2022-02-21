<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateProjetosAddTempos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('projetos', function (Blueprint $table) {
            $table->integer('meses_previstos', false, true)->nullable();
            $table->tinyInteger('dias_previstos', false, true)->nullable();
            $table->tinyInteger('horas_previstas', false, true)->nullable();
            $table->tinyInteger('minutos_previstos', false, true)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropColumns('projetos', [
            'meses_previstos',
            'dias_previstos',
            'horas_previstas',
            'minutos_previstos'
        ]);
    }
}