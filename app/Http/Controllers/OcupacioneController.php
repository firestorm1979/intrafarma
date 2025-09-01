<?php

namespace App\Http\Controllers;
use App\Models\Ocupacione;
use App\Models\Ubicacione;
use App\Models\Producto;
use App\Models\Lote;
use App\Models\Estado;

use Illuminate\Http\Request;

class OcupacioneController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ocupaciones = Ocupacione::all();
        //return view('inventario')->with('ocupaciones', $ocupaciones);
        return view('inventario.ocupacion.index',compact('ocupaciones'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $ubicaciones= Ubicacione::orderBy('nombre')->get();
        $productos= Producto::orderBy('nombre')->get();
        $lotes= Lote::orderBy('nombre')->get();
        $estados= Estado::orderBy('nombre')->get();
        return view('inventario.ocupacion.add', compact('ubicaciones','productos','lotes','estados'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $ocupaciones = new ocupacione();
        $ocupaciones->id_estados=$request->get('estados');
        $ocupaciones->id_productos=$request->get('productos');
        $ocupaciones->id_lotes=$request->get('lotes');
        $ocupaciones->id_ubicaciones=$request->get('ubicaciones');
                  
        $ocupaciones->save();
        return redirect('/ocupaciones');

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $ocupacione = ocupacione::find($id);
        $ubicaciones= Ubicacione::orderBy('nombre')->get();
        $productos= Producto::orderBy('nombre')->get();
        $lotes= Lote::orderBy('nombre')->get();
        $estados= Estado::orderBy('nombre')->get();
    
        return view('inventario.ocupacion.edit', compact('ubicaciones','productos','lotes','estados'))->with('ocupacione',$ocupacione);
        
       
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $ocupacione = ocupacione::find($id);
        $ocupacione->id_productos=$request->get('productos');
        $ocupacione->id_lotes=$request->get('lotes');
        $ocupacione->id_estados=$request->get('estados');
        $ocupacione->npallet = $request->get('npallet');
        $ocupacione->save();
        return redirect('/ocupaciones');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}