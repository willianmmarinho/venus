<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Sala extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = [
        'nome',
        'numero',
        'nr_lugares',
        'localizacao',
        'projetor',
        'quadro',
        'tela_projetor',
        'ventilador',
        'ar_condicionado',
        'computador',
        'controle',
        'som',
        'luz_azul',
        'bebedouro',
        'armarios',
        'tamanho_sala',
        'status_sala'

    ];

    

        // PAI (envia para)

    // $table->foreign('id_sala')->references('id')->on('salas');
    public function grupo(): HasMany
    {
        return $this->hasMany(Grupo::class, 'id_sala');
    }
}
