<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ubicacione extends Model
{
    use HasFactory;
    
    public function rack(){

        //return $this->belongsTo(Producto::class,'id');
        return $this->belongsTo(Rack::class,'id_racks');
    }
    public function Posicione(){

        //return $this->belongsTo(Producto::class,'id');
        return $this->belongsTo(Posicione::class,'id_posiciones');
    }
    public function nivele(){

        //return $this->belongsTo(Producto::class,'id');
        return $this->belongsTo(nivele::class,'id_niveles');
    }
    //public function lado(){

        //return $this->belongsTo(Producto::class,'id');
        //return $this->belongsTo(Lado::class,'id_lado');
    //}
}
