<?php

namespace App\Http\Controllers;

use App\Models\Afiliado;
use App\Models\Multa;
use App\Services\GestionAguaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MultaController extends Controller
{
    public function __construct(private GestionAguaService $gestionAgua) {}

    public function index(Request $request): Response
    {
        $q = $request->input('q');
        $estado = $request->input('estado');

        $multas = Multa::query()
            ->with(['afiliado:id,ci,nombres,apellidos'])
            ->when($q, fn ($query) => $query->whereHas('afiliado', fn ($a) => $a
                ->where('ci', 'like', "%{$q}%")
                ->orWhere('nombres', 'like', "%{$q}%")
                ->orWhere('apellidos', 'like', "%{$q}%")))
            ->when($estado, fn ($query) => $query->where('estado', $estado))
            ->orderByDesc('fecha_aplicacion')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Multas/Index', [
            'multas' => $multas,
            'filtros' => $request->only('q', 'estado'),
        ]);
    }

    public function create(Request $request): Response
    {
        $afiliado = $request->input('afiliado_id')
            ? Afiliado::find($request->input('afiliado_id'))
            : null;

        $sugerencia = $afiliado ? $this->gestionAgua->sugerirMulta($afiliado) : null;
        $deuda = $afiliado ? $this->gestionAgua->calcularDeudaAfiliado($afiliado) : null;

        return Inertia::render('Multas/Form', [
            'afiliado' => $afiliado,
            'sugerencia' => $sugerencia,
            'deuda' => $deuda,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'afiliado_id' => 'required|exists:afiliados,id',
            'tipo' => 'required|in:trimestral,anual,otro',
            'monto' => 'required|numeric|min:0.01',
            'meses_mora' => 'nullable|integer|min:0',
            'fecha_aplicacion' => 'required|date',
            'observaciones' => 'nullable|string',
        ]);

        Multa::create([
            ...$data,
            'usuario_id' => $request->user()->id,
            'estado' => 'pendiente',
        ]);

        return redirect()
            ->route('multas.index')
            ->with('success', 'Multa registrada correctamente.');
    }

    public function marcarPagada(Multa $multa): RedirectResponse
    {
        $multa->update(['estado' => 'pagada']);

        return back()->with('success', 'Multa marcada como pagada.');
    }
}
