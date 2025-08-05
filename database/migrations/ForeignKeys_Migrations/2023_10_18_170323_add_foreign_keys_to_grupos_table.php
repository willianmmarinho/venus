<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('grupos', function (Blueprint $table) {
            //
            $table->foreign('id_dia_semana')->references('id')->on('tipo_dia');
            $table->foreign('id_tipo_grupo')->references('id')->on('tipo_grupo');
            $table->foreign('id_sala')->references('id')->on('salas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('grupos', function (Blueprint $table) {
            //
            $table->dropForeign(['id_dia_semana']);
            $table->dropForeign(['id_tipo_grupo']);
            $table->dropForeign(['id_sala']);
        });
    }
};
