<?php

namespace App\Http\Controllers;

use App\Http\Concerns\SanitizesSearchInput;
use App\Http\Requests\StoreMultaRequest;
use App\Models\Afiliado;
use App\Models\Multa;
use App\Services\GestionAguaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MultaController extends Controller
{
    use SanitizesSearchInput;

    public function __construct(private GestionAguaService $gestionAgua) {}

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Multa::class);

        $q = $this->sanitizeSearchInput($request->input('q'));
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
        $this->authorize('create', Multa::class);

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

    public function store(StoreMultaRequest $request): RedirectResponse
    {
        Multa::create([
            ...$request->validated(),
            'usuario_id' => $request->user()->id,
            'estado' => 'pendiente',
        ]);

        return redirect()
            ->route('multas.index')
            ->with('success', 'Multa registrada correctamente.');
    }

    public function marcarPagada(Multa $multa): RedirectResponse
    {
        $this->authorize('marcarPagada', $multa);

        $multa->update(['estado' => 'pagada']);

        return back()->with('success', 'Multa marcada como pagada.');
    }
}
