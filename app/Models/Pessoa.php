<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pessoa extends Model
{
    use HasFactory;

    //PAI! (Paga/Envia)

    // from atendentes
    // $table->foreign('id_pessoa')->references('id')->on('pessoas');
    public function atendente(): HasOne
    {
        return $this->hasOne(Atendente::class,'id_pessoa')->withDefault();
    }

    // Filho de...(recebe)
    // $table->foreign('nacionalidade')->references('id')->on('tp_nacionalidade');
    public function tp_nacionalidade(): BelongsTo
    {
        return $this->belongsTo(Tp_nacionalidade::class, 'nacionalidade')->withDefault();
    }

    // $table->foreign('naturalidade')->references('id_cidade')->on('tp_cidade');
    public function tp_cidade(): BelongsTo
    {
        return $this->belongsTo(Tp_cidade::class, 'naturalidade', 'id_cidade')->withDefault();
    }

    // $table->foreign('orgao_expedidor')->references('id')->on('tp_orgao_exp');
    public function tp_orgao_exp(): BelongsTo
    {
        return $this->belongsTo(Tp_orgao_exp::class, 'orgao_expedidor')->withDefault();
    }

    // $table->foreign('status')->references('id')->on('tipo_status_pessoa');
    public function tipo_status_pessoa(): BelongsTo
    {
        return $this->belongsTo(Tipo_status_pessoa::class,'status')->withDefault();
    }

    // $table->foreign('uf_idt')->references('id')->on('tp_uf');
    public function tp_uf(): BelongsTo
    {
        return $this->belongsTo(Tp_uf::class,'uf_idt')->withDefault();
    }


    // $table->foreign('uf_natural')->references('id')->on('tp_uf');"
    /*
    public function tp_uf(): BelongsTo
    {
        return $this->belongsTo(Tp_uf::class, 'uf_natural')->withDefault();
    }
    */

}
