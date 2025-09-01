<?php

namespace App\Http\Controllers;
use App\Models\Nivele;

use Illuminate\Http\Request;

class NiveleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $niveles = nivele::all();
        //return view('inventario')->with('niveles', $niveles);
        return view('inventario.nivel.index',compact('niveles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //$niveles = nivele::all();
        //return view('inventario')->with('niveles', $niveles);
        return view('inventario.nivel.add');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $niveles = new nivele();
        
        $niveles->nivel=$request->get('nivel');
        $niveles->descripcion=$request->get('descripcion');

        $niveles->save();
        return redirect('/nivel');

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
        $nivele = nivele::find($id);
        return view('inventario.nivel.edit')->with('nivele',$nivele);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $nivele = nivele::find($id);
        
        $nivele->nivel=$request->get('nivel');
        $nivele->descripcion=$request->get('descripcion');

        $nivele->save();
        return redirect('/nivel');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}