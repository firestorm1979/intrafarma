<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermisosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
{
    // examples:
    //$this->middleware(['role:manager','permission:publish articles|edit articles']);
     //$this->middleware(['permission:Ver administración Sistema'])->only('create');
    // or with specific guard
    //$this->middleware(['role_or_permission:manager|edit articles,api']);
}
    public function index()
    {
        //
        $permisos = Permission::all();
        return view('admin.permisos.index', compact('permisos'));

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
        if ($request->input('nombre'))
       $permiso = Permission::create(['name' => $request->input('nombre')]);
        
        return back();
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
