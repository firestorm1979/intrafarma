<?php

namespace App\Models;

use App\Models\Producto;
use App\Models\Lote;
use App\Models\Estado;
use App\Models\ubicacione;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ocupacione extends Model
{
    use HasFactory;
    public function producto(){

        //return $this->belongsTo(Producto::class,'id');
        return $this->belongsTo(Producto::class,'id_productos');
    }
    public function ubicacione(){

        //return $this->belongsTo(Producto::class,'id');
        return $this->belongsTo(Ubicacione::class,'id_ubicaciones');
    }
    public function lote(){

        //return $this->belongsTo(Producto::class,'id');
        return $this->belongsTo(Lote::class,'id_lotes');
    }
    public function estado(){

        //return $this->belongsTo(Producto::class,'id');
        return $this->belongsTo(Estado::class,'id_estados');
    }
}
