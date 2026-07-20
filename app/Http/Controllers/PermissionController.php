<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['name', 'nombre_mostrar', 'categoria', 'guard_name']);

        $permissions = Permission::query()
            ->when($request->filled('name'), fn($q) => $q->where('name', 'like', '%' . $request->name . '%'))
            ->when($request->filled('nombre_mostrar'), fn($q) => $q->where('nombre_mostrar', 'like', '%' . $request->nombre_mostrar . '%'))
            ->when($request->filled('categoria'), fn($q) => $q->where('categoria', 'like', '%' . $request->categoria . '%'))
            ->when($request->filled('guard_name'), fn($q) => $q->where('guard_name', 'like', '%' . $request->guard_name . '%'))
            ->orderBy('categoria')
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Permissions/Index', compact('permissions', 'filters'));
    }

    public function create()
    {
        $categorias = Permission::whereNotNull('categoria')->distinct()->orderBy('categoria')->pluck('categoria');
        return Inertia::render('Permissions/Create', compact('categorias'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
            'guard_name' => 'nullable|string|in:web,api',
            'nombre_mostrar' => 'nullable|string|max:255',
            'categoria' => 'nullable|string|max:255',
        ]);

        Permission::create([
            'name' => $validated['name'],
            'guard_name' => $validated['guard_name'] ?? 'web',
            'nombre_mostrar' => $validated['nombre_mostrar'],
            'categoria' => $validated['categoria'],
        ]);

        return redirect()->route('permissions.index')
            ->with('success', 'Permiso creado exitosamente.');
    }

    public function show(Permission $permission)
    {
        return Inertia::render('Permissions/Show', compact('permission'));
    }

    public function edit(Permission $permission)
    {
        $categorias = Permission::whereNotNull('categoria')->distinct()->orderBy('categoria')->pluck('categoria');
        return Inertia::render('Permissions/Edit', compact('permission', 'categorias'));
    }

    public function update(Request $request, Permission $permission)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,' . $permission->id,
            'guard_name' => 'nullable|string|in:web,api',
            'nombre_mostrar' => 'nullable|string|max:255',
            'categoria' => 'nullable|string|max:255',
        ]);

        $permission->update([
            'name' => $validated['name'],
            'guard_name' => $validated['guard_name'] ?? 'web',
            'nombre_mostrar' => $validated['nombre_mostrar'],
            'categoria' => $validated['categoria'],
        ]);

        return redirect()->route('permissions.index')
            ->with('success', 'Permiso actualizado exitosamente.');
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();

        return redirect()->route('permissions.index')
            ->with('success', 'Permiso eliminado exitosamente.');
    }
}
