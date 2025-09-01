<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;


use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = user::all();
        
        return view('admin.users.index',compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('admin.users.add');
    }

    public function store(Request $request)//: RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'profile_photo_path' => ['image|mimes:jpeg,png,jpg,gif,svg|max:2048'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'profile_photo_path' =>$request->profile_photo_path,
        ]);

        //event(new Registered($user));

        //Auth::login($user);
        return redirect('/users');
        
    }
    /**
     * Store a newly created resource in storage.
     */
    /*public function store(Request $request)
    {
        //
    }
    */
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
        $users = user::find($id);
        return view('admin.users.edit')->with('user',$users);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        
        $user = user::find($id);

        $user->name = $request->get('name');
        $user->email = $request->get('email');
          
        //$user->password = Hash::make($request->get('password'));
        if ($request->filled('password')) {
            $user->password = Hash::make($request->get('password'));
        }
        
        //$user->profile_photo_path =$request->get('photo');
                
        //dd($request);
        
        //
        //$user = Auth::user();
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('photo', 'public');
            $user->profile_photo_path = $path;
            //$user->save();
        }
        $user->save();
        return redirect('/users');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
