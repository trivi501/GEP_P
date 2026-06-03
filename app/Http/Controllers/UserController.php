<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\User;
use App\Models\Secretaria;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles', 'secretaria')->orderBy('name')->paginate(10);
        return Inertia::render('Users/Index', compact('users'));
    }

    public function create()
    {
        $roles = Role::orderBy('name')->get();
        $secretarias = Secretaria::orderBy('nombre')->get();
        return Inertia::render('Users/Create', compact('roles', 'secretarias'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'secretaria_id' => 'nullable|exists:secretarias,id',
            'roles' => 'nullable|array',
            'roles.*' => 'integer|exists:roles,id',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'secretaria_id' => $validated['secretaria_id'] ?? null,
        ]);

        if (!empty($validated['roles'])) {
            $user->assignRole(Role::whereIn('id', $validated['roles'])->get());
        }

        return redirect()->route('users.index')
            ->with('success', 'Usuario creado exitosamente.');
    }

    public function edit(User $user)
    {
        $user->load('roles', 'secretaria');
        $roles = Role::orderBy('name')->get();
        $secretarias = Secretaria::orderBy('nombre')->get();
        return Inertia::render('Users/Edit', compact('user', 'roles', 'secretarias'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'secretaria_id' => 'nullable|exists:secretarias,id',
            'roles' => 'nullable|array',
            'roles.*' => 'integer|exists:roles,id',
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'secretaria_id' => $validated['secretaria_id'] ?? null,
        ];

        if (!empty($validated['password'])) {
            $data['password'] = bcrypt($validated['password']);
        }

        $user->update($data);

        $user->syncRoles(!empty($validated['roles']) ? Role::whereIn('id', $validated['roles'])->get() : []);

        return redirect()->route('users.index')
            ->with('success', 'Usuario actualizado exitosamente.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'No puedes eliminarte a ti mismo.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Usuario eliminado exitosamente.');
    }
}
