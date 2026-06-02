<?php

namespace App\Http\Controllers;

use App\Http\Concerns\SanitizesSearchInput;
use App\Http\Requests\StorePagoRequest;
use App\Models\Afiliado;
use App\Models\Pago;
use App\Services\GestionAguaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PagoController extends Controller
{
    use SanitizesSearchInput;

    public function __construct(private GestionAguaService $gestionAgua) {}

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Pago::class);

        $q = $this->sanitizeSearchInput($request->input('q'));
        $estado = $request->input('estado');
        $mes = $request->input('mes');
        $anio = $request->input('anio');

        $pagos = Pago::query()
            ->with(['afiliado:id,ci,nombres,apellidos', 'usuario:id,name'])
            ->when($q, fn ($query) => $query->where(function ($w) use ($q) {
                $w->where('numero_recibo', 'like', "%{$q}%")
                    ->orWhereHas('afiliado', fn ($a) => $a
                        ->where('ci', 'like', "%{$q}%")
                        ->orWhere('nombres', 'like', "%{$q}%")
                        ->orWhere('apellidos', 'like', "%{$q}%"));
            }))
            ->when($estado, fn ($query) => $query->where('estado', $estado))
            ->when($mes, fn ($query) => $query->where('mes', $mes))
            ->when($anio, fn ($query) => $query->where('anio', $anio))
            ->orderByDesc('anio')
            ->orderByDesc('mes')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Pagos/Index', [
            'pagos' => $pagos,
            'filtros' => $request->only('q', 'estado', 'mes', 'anio'),
        ]);
    }

    public function create(Request $request): Response
    {
        $this->authorize('create', Pago::class);

        $afiliadoId = $request->input('afiliado_id');
        $afiliado = $afiliadoId ? Afiliado::find($afiliadoId) : null;

        $pendientes = $afiliado
            ? $afiliado->pagos()
                ->where('estado', Pago::ESTADO_PENDIENTE)
                ->orderBy('anio')
                ->orderBy('mes')
                ->get()
            : [];

        return Inertia::render('Pagos/Form', [
            'afiliado' => $afiliado,
            'pendientes' => $pendientes,
            'numero_sugerido' => Pago::siguienteNumero(),
        ]);
    }

    public function buscarAfiliado(Request $request)
    {
        $this->authorize('buscarAfiliado', Pago::class);

        $q = $this->sanitizeSearchInput($request->input('q', ''), 80) ?? '';

        $afiliados = Afiliado::query()
            ->where('estado', 'activo')
            ->where(function ($w) use ($q) {
                $w->where('ci', 'like', "%{$q}%")
                    ->orWhere('nombres', 'like', "%{$q}%")
                    ->orWhere('apellidos', 'like', "%{$q}%");
            })
            ->limit(15)
            ->get(['id', 'ci', 'nombres', 'apellidos']);

        return response()->json($afiliados);
    }

    public function store(StorePagoRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $pago = Pago::findOrFail($data['pago_id']);

        if ($pago->estado === Pago::ESTADO_PAGADO) {
            return back()->with('error', 'Este período ya fue pagado.');
        }

        $pago->update([
            'numero_recibo' => Pago::siguienteNumero(),
            'usuario_id' => $request->user()->id,
            'fecha_pago' => $data['fecha_pago'],
            'metodo' => $data['metodo'],
            'estado' => Pago::ESTADO_PAGADO,
            'observaciones' => $data['observaciones'] ?? null,
        ]);

        return redirect()
            ->route('pagos.show', $pago)
            ->with('success', 'Pago registrado correctamente.');
    }

    public function show(Pago $pago): Response
    {
        $this->authorize('view', $pago);

        $pago->load(['afiliado', 'usuario:id,name']);

        return Inertia::render('Pagos/Show', [
            'pago' => $pago,
        ]);
    }

    public function generarMes(Request $request): RedirectResponse
    {
        $this->authorize('generarMes', Pago::class);

        $this->gestionAgua->generarObligacionesMesActual();

        return back()->with('success', 'Obligaciones del mes generadas para todos los afiliados activos.');
    }
}
