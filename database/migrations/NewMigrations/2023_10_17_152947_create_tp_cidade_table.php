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
        Schema::create('tp_cidade', function (Blueprint $table) {
            $table->id('id_cidade'); // actualy ->         $table->id('id_cidade');
            $table->string('descricao', 100)->nullable();
            $table->unsignedBigInteger('id_uf')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tp_cidade');
    }
};
