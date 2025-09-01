<?php

namespace App\Http\Controllers;
use App\Models\Rack;
use Illuminate\Http\Request;

class RackController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $racks = Rack::all();
        //return view('inventario')->with('racks', $racks);
        return view('inventario.rack.index',compact('racks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //$racks = Rack::all();
        //return view('inventario')->with('racks', $racks);
        return view('inventario.rack.add');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $racks = new Rack();
        
        $racks->numero=$request->get('numero');
        $racks->descripcion=$request->get('descripcion');

        $racks->save();
        return redirect('/rack');

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
        $rack = Rack::find($id);
        return view('inventario.rack.edit')->with('rack',$rack);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $rack = Rack::find($id);
        
        $rack->numero=$request->get('numero');
        $rack->descripcion=$request->get('descripcion');

        $rack->save();
        return redirect('/rack');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
