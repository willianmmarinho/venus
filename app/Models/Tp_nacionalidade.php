<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tp_nacionalidade extends Model
{
    use HasFactory;
    protected $table = 'tp_nacionalidade';

    // PAI (Envia para)
    // from pessoa
    // $table->foreign('nacionalidade')->references('id')->on('tp_nacionalidade');
    public function pessoa(): HasMany
    {
        return $this->hasMany(Pessoa::class,'nacionalidade');
    }
}
