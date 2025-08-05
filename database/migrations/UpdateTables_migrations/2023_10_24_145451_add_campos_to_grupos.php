<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{


    public function up(): void
    {
        Schema::table('grupos', function (Blueprint $table) {
            //
            $table->unsignedBigInteger('nr_mediuns')->nullable();
            $table->text('dirigente')->nullable();

        });
    }


    public function down(): void
    {
        Schema::table('grupos', function (Blueprint $table) {
            //
            $table->dropColumn('nr_mediuns');
            $table->dropColumn('dirigente');
            
        });
    }
};
