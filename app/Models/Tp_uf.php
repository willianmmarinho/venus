<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Tp_uf extends Model
{
    use HasFactory;

    protected $table = 'tp_uf';

    // from pessoa
    // $table->foreign('uf_idt')->references('id')->on('tp_uf');
    public function pessoa(): HasMany
    {
        return $this->hasMany(Pessoa::class, 'uf_idt');
    }

    // from pessoa
    // $table->foreign('uf_natural')->references('id')->on('tp_uf');"
    // public function pessoa(): HasMany
    // {
    //     return $this->hasMany(Pessoa::class, 'uf_natural');
    // }



}
