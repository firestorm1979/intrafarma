<?php

namespace App\Http\Controllers;

//use App\Models\User;
use App\Models\Producto;
use App\Models\Ocupacione;
use App\Models\Rack;
use App\Models\Ubicacione;
use App\Models\Lote;
use App\Models\Estado;

use Illuminate\Http\Request;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;


class InventarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       /* */
    $rack_id=request(key: 'racks');
       $deposito_id=request(key:'depositos');

       if(!isset($rack_id)){
        $rack_id = '1';
   }
   
   if(!isset($deposito_id)){
    $deposito_id = '1';
}
      $ocupaciones = Ocupacione::where(function (Builder $query) {
        $query->select('ubicaciones.deposito')
            ->from('ubicaciones')
            ->whereColumn('ubicaciones.id', 'ocupaciones.id_ubicaciones')
            ->limit(1)
            ->orderBy('ubicaciones.id_niveles', 'desc')
            ->orderBy('ubicaciones.id_posiciones', 'asc')
            ;
        }, $deposito_id)->where(function (Builder $query) {
            $query->select('id_racks')
                ->from('ubicaciones')
                ->whereColumn('ubicaciones.id', 'ocupaciones.id_ubicaciones')
                ->limit(1)
                ->orderBy('ubicaciones.id_niveles', 'desc')
                ->orderBy('ubicaciones.id_posiciones', 'asc')
                ;
            }, $rack_id)->get();
           
        $ocupaciones2= Ocupacione::with('ubicacione');
        
        /*$ocupaciones2->sortBy('ocupaciones.id_niveles');*/
        
        /*$ocupaciones = DB::table('ocupaciones')
            ->leftJoin('ubicaciones', 'ocupaciones.id_ubicaciones', '=', 'ubicaciones.id')
            ->get();*/
        
        $racks = Rack::all();
        $racksel= $racks->find($rack_id);
       //return view('inventario')->with('racks', $racks);
             
      return view('inventario.inventario.index',compact('ocupaciones', 'racks', 'racksel', 'ocupaciones2'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function autocomplete(Request $request)
    {
        $res = Producto::select("id","nombre")
                ->where("nombre","LIKE","%{$request->term}%")
                ->get();
    
        return response()->json($res);
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

        return view('inventario.inventario.edit', compact('ubicaciones','productos','lotes','estados'))->with('ocupacione',$ocupacione);
        
       
    }

    
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $ocupacione = ocupacione::find($id);
        $ubicacion= ubicacione::find($ocupacione->id_ubicaciones);
        $id_deposito= $ocupacione->ubicacione->deposito;
        $rack = $ubicacion->id_racks;
        $ocupacione->id_productos=$request->get('productos');
        $ocupacione->id_lotes=$request->get('lotes');
        $ocupacione->id_estados=$request->get('estados');
        $ocupacione->npallet=$request->get('npallet');
        $ocupacione->save();
        return redirect('/inventario?racks='. $rack .'&depositos='. $id_deposito);
    }

   
   
   public function vaciar(string $id)
   {
        //Actualiza registros para dejar en blanco
        $ocupacione = ocupacione::find($id);
        $ubicacion= ubicacione::find($ocupacione->id_ubicaciones);
        $rack = $ubicacion->id_racks;
        $ocupacione->id_productos=1;
        $ocupacione->id_lotes=1;
        $ocupacione->id_estados=1;
        $ocupacione->npallet=null;
        $ocupacione->save();
        return redirect()->back()->with('eliminar','ok');
        //return redirect('/inventario?racks='. $rack );
    }

    public function destroy(string $id)
    {
        //Actualiza registros para dejar en blanco
        $ocupacione = ocupacione::find($id);
        $ocupacione->id_productos=1;
        $ocupacione->id_lotes=1;
        $ocupacione->id_estados=1;
        $ocupacione->save();
        
    }
}
