<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Tipo_grupo extends Model
{
    use HasFactory;
    protected $table = 'tipo_grupo';

    // PAI (envia para)

    // $table->foreign('id_tipo_grupo')->references('id')->on('tipo_grupo');
    public function tipo_grupo(): HasMany
    {
        return $this->hasMany(Tipo_grupo::class, 'id_tipo_grupo');
    }
}
