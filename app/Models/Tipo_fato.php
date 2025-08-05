<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tipo_fato extends Model
{
    use HasFactory;

    protected $table='tipo_fato';

    protected $fillable =[
        'id',
        'descricao'
    ];
    public $timestamps = false;

    


}
