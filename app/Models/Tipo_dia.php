<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tipo_dia extends Model
{
    use HasFactory;
    protected $table = 'tipo_dia';

    // PAI (envia para)

    // $table->foreign('id_dia_semana')->references('id')->on('tipo_dia');
    public function grupo(): HasMany
    {
        return $this->hasMany(Grupo::class, 'id_dia_semana');
    }


}
