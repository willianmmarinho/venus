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
        Schema::table('pessoas', function (Blueprint $table) {
            //
            $table->foreign('nacionalidade')->references('id')->on('tp_nacionalidade');
            $table->foreign('naturalidade')->references('id_cidade')->on('tp_cidade');
            $table->foreign('orgao_expedidor')->references('id')->on('tp_orgao_exp');
            $table->foreign('status')->references('id')->on('tipo_status_pessoa');
            $table->foreign('uf_idt')->references('id')->on('tp_uf');
            $table->foreign('uf_natural')->references('id')->on('tp_uf');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pessoas', function (Blueprint $table) {
            //
            $table->dropForeign('nacionalidade');
            $table->dropForeign('naturalidade');
            $table->dropForeign('orgao_expedidor');
            $table->dropForeign('status');
            $table->dropForeign('uf_idt');
            $table->dropForeign('nacionalidade');
        });
    }
};
