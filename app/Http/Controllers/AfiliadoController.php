<?php

namespace App\Http\Controllers;

use App\Http\Concerns\SanitizesSearchInput;
use App\Http\Requests\StoreAfiliadoRequest;
use App\Http\Requests\UpdateAfiliadoRequest;
use App\Models\Afiliado;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AfiliadoController extends Controller
{
    use SanitizesSearchInput;

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Afiliado::class);

        $query = Afiliado::query();

        $busqueda = $this->sanitizeSearchInput($request->input('busqueda'));

        if ($busqueda !== null) {
            $query->where(function ($q) use ($busqueda) {
                $q->where('nombres', 'like', "%{$busqueda}%")
                    ->orWhere('apellidos', 'like', "%{$busqueda}%")
                    ->orWhere('ci', 'like', "%{$busqueda}%");
            });
        }

        $afiliados = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('Afiliados/Index', [
            'afiliados' => $afiliados,
            'filtros' => ['busqueda' => $busqueda],
            'resumen' => [
                'total' => Afiliado::query()->count(),
                'activos' => Afiliado::query()->where('estado', 'activo')->count(),
                'inactivos' => Afiliado::query()->where('estado', 'inactivo')->count(),
            ],
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Afiliado::class);

        return Inertia::render('Afiliados/Create');
    }

    public function store(StoreAfiliadoRequest $request)
    {
        Afiliado::create($request->validated());

        return redirect()->route('afiliados.index')->with('success', 'Afiliado registrado exitosamente.');
    }

    public function edit(Afiliado $afiliado): Response
    {
        $this->authorize('update', $afiliado);

        return Inertia::render('Afiliados/Edit', [
            'afiliado' => $afiliado,
        ]);
    }

    public function update(UpdateAfiliadoRequest $request, Afiliado $afiliado)
    {
        $afiliado->update($request->validated());

        return redirect()->route('afiliados.index')->with('success', 'Datos del afiliado actualizados exitosamente.');
    }

    public function destroy(Afiliado $afiliado)
    {
        $this->authorize('delete', $afiliado);

        $afiliado->delete();

        return redirect()->route('afiliados.index')->with('success', 'Afiliado eliminado correctamente.');
    }
}
