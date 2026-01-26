<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index()
    {
        return Inertia::render('Users/Index', [
            'users' => User::with('role')->get(),
            'roles' => Role::all(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'email' => 'required|string|email|max:150|unique:users',
            'password' => 'required|string|min:8',
            'role_id' => 'required|exists:roles,id',
        ]);

        $role = Role::find($request->role_id);

        User::create([
            'nombre' => $request->nombre,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'rol' => $role ? $role->nombre : 'usuario', // Fallback
        ]);

        return redirect()->back()->with('message', 'Usuario creado con éxito');
    }

    public function update(Request $request, User $usuario)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'email' => "required|string|email|max:150|unique:users,email,{$usuario->id}",
            'password' => 'nullable|string|min:8',
            'role_id' => 'required|exists:roles,id',
        ]);

        $role = Role::find($request->role_id);

        $data = [
            'nombre' => $request->nombre,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'rol' => $role ? $role->nombre : 'usuario',
        ];

        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        $usuario->update($data);

        return redirect()->back()->with('message', 'Usuario actualizado con éxito');
    }

    public function destroy(User $usuario)
    {
        if ($usuario->id === auth()->id()) {
            return redirect()->back()->with('error', 'No puedes eliminarte a ti mismo');
        }

        $usuario->delete();
        return redirect()->back()->with('message', 'Usuario eliminado con éxito');
    }
}
