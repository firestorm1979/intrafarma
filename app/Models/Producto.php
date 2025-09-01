<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;
    public function lotes(){

        //return $this->hasMany(Lote::class,'id');
        //return $this->belongsTo(Lote::class,'id_producto');
    }
}
