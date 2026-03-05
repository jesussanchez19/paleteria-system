<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class VendedorController extends Controller
{
    public function edit(User $user)
    {
        if ($user->role !== 'vendedor') {
            return back()->with('error', 'Solo se pueden editar vendedores.');
        }
        return view('panel.vendedores-edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        if ($user->role !== 'vendedor') {
            return back()->with('error', 'Solo se pueden actualizar vendedores.');
        }
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:180', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'string', 'min:6'],
        ]);
        $user->name = $data['name'];
        $user->email = $data['email'];
        if (!empty($data['password'])) {
              $user->password = \Illuminate\Support\Facades\Hash::make($data['password']);
        }
        $user->save();
        return redirect()->route('vendedores.index')->with('success', 'Vendedor actualizado correctamente.');
    }

    public function index()
    {
        $vendedores = User::where('role', 'vendedor')
            ->orderBy('id', 'desc')
            ->get();

        return view('panel.vendedores', compact('vendedores'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:180', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'vendedor',
        ]);

        audit_log('seller.created', 'users', $user, [
            'nombre' => $user->name,
            'email' => $user->email,
            'rol' => 'vendedor',
        ]);

        return back()->with('success', 'Vendedor creado correctamente.');
    }

    public function destroy(User $user)
    {
        // Solo se permite eliminar vendedores
        if ($user->role !== 'vendedor') {
            return back()->with('error', 'No permitido: solo se pueden eliminar vendedores.');
        }

        // Por seguridad (evita eliminar tu propio usuario)
        if (Auth::id() === $user->id) {
            return back()->with('error', 'No puedes eliminar tu propio usuario.');
        }

        $user->delete();

        return back()->with('success', 'Vendedor eliminado correctamente.');
    }
    public function toggle(User $user)
    {
        if ($user->role !== 'vendedor') {
            return back()->with('error', 'Solo se pueden activar/desactivar vendedores.');
        }

        if (Auth::id() === $user->id) {
            return back()->with('error', 'No puedes desactivarte a ti mismo.');
        }

        $user->is_active = ! $user->is_active;
        $user->save();

        audit_log('seller.toggled', 'users', $user, [
            'vendedor' => $user->name,
            'estado' => $user->is_active ? 'Activado' : 'Desactivado',
        ]);

        return back()->with('success', 'Estado del vendedor actualizado.');
    }
}
