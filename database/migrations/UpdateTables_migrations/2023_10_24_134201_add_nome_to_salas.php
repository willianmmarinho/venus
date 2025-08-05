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
        Schema::table('salas', function (Blueprint $table) {
            //
            $table->text('nome')->nullable();
            $table->unsignedBigInteger('numero')->nullable();
            $table->unsignedBigInteger('nr_lugares')->nullable();
            $table->text('localizacao')->nullable();
            $table->boolean('projetor')->nullable();
            $table->boolean('pc')->nullable();
            $table->boolean('quadro')->nullable();




        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salas', function (Blueprint $table) {
            //
            $table->dropColumn('nome');
            $table->dropColumn('localizacao');
            $table->dropColumn('nr_lugares');
            $table->dropColumn('numero');
        });
    }
};
