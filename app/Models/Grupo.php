<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Grupo extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'id_dia_semana',
        'hr_inicio',
        'hr_fim',
        'ativo',
        'nr_vagas',
        'id_tipo_grupo',
        'nr_trabalhadores',
        'id_sala'
    ];


    //Pai (envia para)
    // $table->foreign('id_grupo')->references('id')->on('grupos');
    public function atendente(): HasMany
    {
        return $this->hasMany(Atendente::class,'id_grupo');
    }


    // filho de (recebe)

    // $table->foreign('id_dia_semana')->references('id')->on('tipo_dia');
    public function tipo_dia(): BelongsTo
    {
        return $this->belongsTo(Tipo_dia::class,'id_dia_semana')->withDefault();
    }

    // $table->foreign('id_tipo_grupo')->references('id')->on('tipo_grupo');
    public function tipo_grupo(): BelongsTo
    {
        return $this->belongsTo(Tipo_grupo::class,'id_tipo_grupo')->withDefault();
    }

    // $table->foreign('id_sala')->references('id')->on('salas');
    public function sala(): BelongsTo
    {
        return $this->belongsTo(Sala::class,'id_sala')->withDefault();
    }
}
