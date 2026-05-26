<?php

namespace App\Http\Controllers;

use App\Models\Afiliado;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AfiliadoController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Afiliado::query();

        if ($request->filled('busqueda')) {
            $busqueda = $request->string('busqueda')->toString();
            $query->where(function ($q) use ($busqueda) {
                $q->where('nombres', 'like', "%{$busqueda}%")
                    ->orWhere('apellidos', 'like', "%{$busqueda}%")
                    ->orWhere('ci', 'like', "%{$busqueda}%");
            });
        }

        $afiliados = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('Afiliados/Index', [
            'afiliados' => $afiliados,
            'filtros' => $request->only('busqueda'),
            'resumen' => [
                'total' => Afiliado::query()->count(),
                'activos' => Afiliado::query()->where('estado', 'activo')->count(),
                'inactivos' => Afiliado::query()->where('estado', 'inactivo')->count(),
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Afiliados/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ci' => 'required|string|unique:afiliados,ci|max:20',
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:50',
            'direccion' => 'nullable|string',
            'fecha_afiliacion' => 'nullable|date',
            'estado' => 'nullable|string|in:activo,inactivo',
        ], [], [
            'ci' => 'cédula de identidad',
            'nombres' => 'nombres',
            'apellidos' => 'apellidos',
            'telefono' => 'teléfono',
            'direccion' => 'dirección',
            'fecha_afiliacion' => 'fecha de afiliación',
            'estado' => 'estado',
        ]);

        Afiliado::create($validated);

        return redirect()->route('afiliados.index')->with('success', 'Afiliado registrado exitosamente.');
    }

    public function edit(Afiliado $afiliado): Response
    {
        return Inertia::render('Afiliados/Edit', [
            'afiliado' => $afiliado,
        ]);
    }

    public function update(Request $request, Afiliado $afiliado)
    {
        $validated = $request->validate([
            'ci' => 'required|string|max:20|unique:afiliados,ci,'.$afiliado->id,
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:50',
            'direccion' => 'nullable|string',
            'fecha_afiliacion' => 'nullable|date',
            'estado' => 'nullable|string|in:activo,inactivo',
        ], [], [
            'ci' => 'cédula de identidad',
            'nombres' => 'nombres',
            'apellidos' => 'apellidos',
            'telefono' => 'teléfono',
            'direccion' => 'dirección',
            'fecha_afiliacion' => 'fecha de afiliación',
            'estado' => 'estado',
        ]);

        $afiliado->update($validated);

        return redirect()->route('afiliados.index')->with('success', 'Datos del afiliado actualizados exitosamente.');
    }

    public function destroy(Afiliado $afiliado)
    {
        $afiliado->delete();

        return redirect()->route('afiliados.index')->with('success', 'Afiliado eliminado correctamente.');
    }
}
