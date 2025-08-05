<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
//use Illuminate\Database\Eloquent\SoftDeletes;

class Atendente extends Model
{
    use HasFactory;
    // use SoftDeletes;


    // Filho (recebe de):
    // $table->foreign('id_pessoa')->references('id')->on('pessoas');
    public function pessoa(): BelongsTo
    {
        return $this->belongsTo(Pessoa::class,'id_pessoa')->withDefault(
            [
                'nome_completo' => 'Pessoa não encontrada'
            ]
        );
    }

    // $table->foreign('status_atendente')->references('id')->on('tipo_status_pessoa');
    public function tipo_status_pessoa(): BelongsTo
    {
        return $this->belongsTo(Tipo_status_pessoa::class, 'status_atendente')->withDefault();
    }





}
