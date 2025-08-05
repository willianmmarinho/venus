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
        Schema::create('pessoas', function (Blueprint $table) {
            $table->id();
            $table->string('nome_completo', 45)->nullable();
            $table->string('idt', 45)->nullable();
            $table->unsignedBigInteger('uf_idt')->nullable();
            $table->unsignedBigInteger('orgao_expedidor')->nullable();
            $table->date('dt_emissao_idt')->nullable();
            $table->unsignedBigInteger('cpf')->nullable();
            $table->date('dt_nascimento')->nullable();
            $table->unsignedBigInteger('uf_natural')->nullable();
            $table->unsignedBigInteger('naturalidade')->nullable();
            $table->unsignedBigInteger('nacionalidade')->nullable();
            $table->unsignedBigInteger('sexo')->nullable();
            $table->text('email')->nullable();
            $table->unsignedBigInteger('ddd')->nullable();
            $table->unsignedBigInteger('celular')->nullable();
            $table->unsignedBigInteger('status')->nullable();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pessoas');
    }
};
