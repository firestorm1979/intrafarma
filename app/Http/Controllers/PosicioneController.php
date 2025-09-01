<?php

namespace App\Http\Controllers;
use App\Models\Posicione;

use Illuminate\Http\Request;

class PosicioneController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posiciones = Posicione::all();
        //return view('inventario')->with('posiciones', $posiciones);
        return view('inventario.posicion.index',compact('posiciones'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //$posiciones = posicione::all();
        //return view('inventario')->with('posiciones', $posiciones);
        return view('inventario.posicion.add');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $posiciones = new posicione();
        
        $posiciones->posicion=$request->get('posicion');
        $posiciones->descripcion=$request->get('descripcion');

        $posiciones->save();
        return redirect('/posicion');

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
        $posicione = posicione::find($id);
        return view('inventario.posicion.edit')->with('posicione',$posicione);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $posicione = posicione::find($id);
        
        $posicione->posicion=$request->get('posicion');
        $posicione->descripcion=$request->get('descripcion');

        $posicione->save();
        return redirect('/posicion');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}