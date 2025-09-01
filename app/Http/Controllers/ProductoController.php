<?php

namespace App\Http\Controllers;
use App\Models\Producto;

use Illuminate\Http\Request;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $productos = producto::all();
        //return view('inventario')->with('productos', $productos);
        return view('inventario.producto.index',compact('productos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //$productos = producto::all();
        //return view('inventario')->with('productos', $productos);
        return view('inventario.producto.add');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $productos = new producto();
        $productos->nombre=$request->get('nombre');
        $productos->codigo=$request->get('codigo');
        $productos->save();
        return redirect('/producto');

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
        $producto = producto::find($id);
        return view('inventario.producto.edit')->with('producto',$producto);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $producto = producto::find($id);
        $producto->nombre=$request->get('nombre');
        $producto->codigo=$request->get('codigo');
        $producto->save();
        return redirect('/producto');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}