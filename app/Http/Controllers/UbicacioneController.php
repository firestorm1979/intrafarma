<?php

namespace App\Http\Controllers;
use App\Models\Ubicacione;
use App\Models\Rack;
use App\Models\Nivele;
use App\Models\Posicione;
use App\Models\Lado;
use Illuminate\Http\Request;

class UbicacioneController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ubicaciones = ubicacione::all();
        //return view('inventario')->with('ubicaciones', $ubicaciones);
        return view('inventario.ubicaciones.index',compact('ubicaciones'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $racks= Rack::orderBy('numero')->get();
        $niveles= Nivele::orderBy('nivel')->get();
        $lados= lado::orderBy('lado')->get();
        $posiciones= Posicione::orderBy('posicion')->get();
        return view('inventario.ubicaciones.add', compact('posiciones','racks','niveles','lados'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $ubicaciones = new Ubicacione();
        $ubicaciones->nombre=$request->get('nombre');
        $ubicaciones->deposito=$request->get('deposito');
        $ubicaciones->id_racks=$request->get('rack');
        $ubicaciones->id_posiciones=$request->get('posicione');
        $ubicaciones->id_niveles=$request->get('nivele');
        $ubicaciones->id_lado=$request->get('lado');
                
        $ubicaciones->save();
        return redirect('/ubicaciones');

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
        $ubicacione = Ubicacione::find($id);
        $racks= Rack::orderBy('numero')->get();
        $niveles= Nivele::orderBy('nivel')->get();
        $lados= Lado::orderBy('lado')->get();
        $posiciones= Posicione::orderBy('posicion')->get();
        return view('inventario.ubicaciones.edit', compact('posiciones','racks','niveles','lados'))->with('ubicacione',$ubicacione);
        
       
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $ubicacione = ubicacione::find($id);
        $ubicacione->nombre=$request->get('nombre');
        $ubicacione->deposito=$request->get('deposito');
        $ubicacione->save();
        return redirect('/ubicaciones');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}