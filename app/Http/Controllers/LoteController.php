<?php

namespace App\Http\Controllers;
use App\Models\Lote;
use App\Models\Producto;
use Illuminate\Http\Request;

class LoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $lotes = lote::all();
        //return view('inventario')->with('lotes', $lotes);
        return view('inventario.lote.index',compact('lotes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $productos= Producto::orderBy('nombre')->get();
        return view('inventario.lote.add', compact('productos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $lotes = new lote();
        $lotes->nombre=$request->get('nombre');
        $lotes->id_producto=$request->get('producto');
        
        $lotes->save();
        return redirect('/lote');

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
        $lote = lote::find($id);
        $productos= Producto::orderBy('nombre')->get();
        return view('inventario.lote.edit',compact('productos'))->with('lote',$lote);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $lote = lote::find($id);
        $lote->nombre=$request->get('nombre');
        $lote->id_producto=$request->get('producto');
        $lote->save();
        return redirect('/lote');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}