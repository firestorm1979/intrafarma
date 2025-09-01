<?php

namespace App\Http\Controllers;
use App\Models\Lado;
use Illuminate\Http\Request;

class LadoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $lados = lado::all();
        //return view('inventario')->with('lados', $lados);
        return view('inventario.lado.index',compact('lados'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //$lados = lado::all();
        //return view('inventario')->with('lados', $lados);
        return view('inventario.lado.add');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $lados = new lado();
        
        $lados->lado=$request->get('lado');
        $lados->descripcion=$request->get('descripcion');

        $lados->save();
        return redirect('/lado');

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
        $lado = lado::find($id);
        return view('inventario.lado.edit')->with('lado',$lado);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $lado = lado::find($id);
        
        $lado->lado=$request->get('lado');
        $lado->descripcion=$request->get('descripcion');

        $lado->save();
        return redirect('/lado');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}