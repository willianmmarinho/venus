<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Tp_cidade extends Model
{
    use HasFactory;
    protected $table = 'tp_cidade';

    // from Pessoa
    // $table->foreign('naturalidade')->references('id_cidade')->on('tp_cidade');
    public function pessoa(): HasMany
    {
        return $this->hasMany(Pessoa::class,'naturalidade','id_cidade');
    }
}
