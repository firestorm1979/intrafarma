<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrdenProduccion extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)

    {
        // dd(request(key: 'date_range')); // selecciona hoy si no esta seteado el rango
        if ((request(key: 'date_range')) == null) {
            //$fechainicial = Carbon::now()->startOfMonth();"09/09/2024 - 09/16/2024"
            $dateRange = Carbon::now() . ' - ' . Carbon::now();
        } else {
            $dateRange = request(key: 'date_range');
        }


        list($startDate, $endDate) = explode(' - ', $dateRange); //parsea fechas

        $startDate = Carbon::parse($startDate)->format('Y-d-m'); // formatea fechas segun sql
        $endDate = Carbon::parse($endDate)->format('Y-d-m');



        //$fdesde=request(key: 'desde');
        // $fhasta=request(key:'hasta');

        $ordenes = DB::connection('gp')->select('SET NOCOUNT ON ; exec GR_INTRANET_OPS \'' . $startDate . '\', \'' . $endDate . '\';');
        // dd($dateRange,$startDate, $endDate,$ordenes);
        return view('consultasgp/op/ordenproduccion', compact('ordenes','dateRange' ));

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
    public function show(string $id)
    {
        $orden = request(key: 'orden');
        
        $encabezado = DB::connection('gp')->select('SET NOCOUNT ON ; exec GR_INTRANET_OPS_DETALLE \'' . $orden . '\';');
        //
        dd($orden, $encabezado);
        return view('consultasgp/op/ordendetalle', compact('encabezado'));
    
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
