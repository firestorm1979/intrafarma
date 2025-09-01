<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ConsultaRecuperopController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(){
        
        $fechainicial=request(key: 'start_date');
        $fechafinal=request(key:'end_date');


        $start = Carbon::parse($fechainicial);
        $end = Carbon::parse($fechafinal);
       
        if(!isset($fechainicial)){
        $fechainicial = Carbon::now()->startOfMonth();
            }
   
   if(!isset($fechafinal)){
    $fechafinal = Carbon::now();
}
    
        //
        //request(key: '$dateRange');
         //Se ejecuta el procedimiento almacenado
         $datos = DB::connection('gp')->select('SET NOCOUNT ON ; exec bom_OP ?, ?;', array($fechainicial, $fechafinal)); 
         // ; //Retornamos el resultado.
        //dd(request(key: '$dateRange'));
        return view('consultasgp.recuperos.consultarecupero',compact('datos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        
    }

    /**
     * Display the specified resource.
     */
    public function show( request $lote, $producto )
    {
        //
       
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
