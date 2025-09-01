<?php

namespace App\Http\Controllers;
use App\Models\Estado;
use Illuminate\Http\Request;

class EstadoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $estados = estado::all();
        //return view('inventario')->with('estados', $estados);
        return view('inventario.estado.index',compact('estados'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //$estados = estado::all();
        //return view('inventario')->with('estados', $estados);
        return view('inventario.estado.add');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $estados = new estado();
        
        $estados->nombre=$request->get('nombre');
        
        $estados->save();
        return redirect('/estado');

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
        $estado = estado::find($id);
        return view('inventario.estado.edit')->with('estado',$estado);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $estado = estado::find($id);
        
        $estado->nombre=$request->get('nombre');
        
        $estado->save();
        return redirect('/estado');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}