<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Tp_orgao_exp extends Model
{
    use HasFactory;
    protected $table = 'tp_orgao_exp';

    // from pessoa
    // $table->foreign('orgao_expedidor')->references('id')->on('tp_orgao_exp');
    public function pessoa(): HasMany
    {
        return $this->hasMany(Pessoa::class,'orgao_expedidor');
    }
}
