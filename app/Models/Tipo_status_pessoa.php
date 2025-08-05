<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Tipo_status_pessoa extends Model
{
    use HasFactory;
    protected $table = 'tipo_status_pessoa';

    // from pessoas:
    // $table->foreign('status')->references('id')->on('tipo_status_pessoa');
    public function pessoa(): HasMany
    {
        return $this->hasMany(Pessoa::class,'status');
    }

    //from atendente
    // $table->foreign('status_atendente')->references('id')->on('tipo_status_pessoa');
    public function atendente(): HasMany
    {
        return $this->HasMany(Atendente::class,'status_atendente')->withDefault();
    }


}
