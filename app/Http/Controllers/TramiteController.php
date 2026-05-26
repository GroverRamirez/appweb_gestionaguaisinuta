<?php

namespace App\Http\Controllers;

use App\Models\Afiliado;
use App\Models\Tramite;
use App\Services\GestionAguaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TramiteController extends Controller
{
    public function __construct(private GestionAguaService $gestionAgua) {}

    public function index(Request $request): Response
    {
        $estado = $request->input('estado');

        $tramites = Tramite::query()
            ->with(['afiliado:id,ci,nombres,apellidos'])
            ->when($estado, fn ($query) => $query->where('estado', $estado))
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Tramites/Index', [
            'tramites' => $tramites,
            'filtros' => $request->only('estado'),
        ]);
    }

    public function create(Request $request): Response
    {
        $afiliado = $request->input('afiliado_id')
            ? Afiliado::find($request->input('afiliado_id'))
            : null;

        $deuda = $afiliado ? $this->gestionAgua->calcularDeudaAfiliado($afiliado) : null;

        return Inertia::render('Tramites/Form', [
            'afiliado' => $afiliado,
            'deuda' => $deuda,
            'sin_deudas' => $afiliado ? $this->gestionAgua->afiliadoSinDeudas($afiliado) : false,
        ]);
    }

    public function verificarDeudas(Afiliado $afiliado)
    {
        $deuda = $this->gestionAgua->calcularDeudaAfiliado($afiliado);

        return response()->json([
            'sin_deudas' => $deuda['total'] <= 0,
            'deuda' => $deuda,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'afiliado_id' => 'required|exists:afiliados,id',
            'ci_nuevo' => 'required|string|max:20',
            'nombres_nuevo' => 'required|string|max:255',
            'apellidos_nuevo' => 'required|string|max:255',
            'observaciones' => 'nullable|string',
        ]);

        $afiliado = Afiliado::findOrFail($data['afiliado_id']);
        $deuda = $this->gestionAgua->calcularDeudaAfiliado($afiliado);

        Tramite::create([
            'afiliado_id' => $afiliado->id,
            'ci_nuevo' => $data['ci_nuevo'],
            'nombres_nuevo' => $data['nombres_nuevo'],
            'apellidos_nuevo' => $data['apellidos_nuevo'],
            'observaciones' => $data['observaciones'] ?? null,
            'sin_deudas_verificado' => $deuda['total'] <= 0,
            'deuda_total_verificada' => $deuda['total'],
            'estado' => Tramite::ESTADO_PENDIENTE,
        ]);

        return redirect()
            ->route('tramites.index')
            ->with('success', 'Trámite de cambio de nombre registrado.');
    }

    public function aprobar(Request $request, Tramite $tramite): RedirectResponse
    {
        if ($tramite->estado !== Tramite::ESTADO_PENDIENTE) {
            return back()->with('error', 'El trámite ya fue resuelto.');
        }

        if (! $tramite->sin_deudas_verificado) {
            return back()->with('error', 'No se puede aprobar: el afiliado registra deudas pendientes.');
        }

        $tramite->afiliado->update([
            'ci' => $tramite->ci_nuevo,
            'nombres' => $tramite->nombres_nuevo,
            'apellidos' => $tramite->apellidos_nuevo,
        ]);

        $tramite->update([
            'estado' => Tramite::ESTADO_APROBADO,
            'usuario_id' => $request->user()->id,
            'fecha_resolucion' => now(),
        ]);

        return back()->with('success', 'Trámite aprobado y titular actualizado.');
    }

    public function rechazar(Request $request, Tramite $tramite): RedirectResponse
    {
        $data = $request->validate([
            'observaciones' => 'nullable|string',
        ]);

        $tramite->update([
            'estado' => Tramite::ESTADO_RECHAZADO,
            'usuario_id' => $request->user()->id,
            'fecha_resolucion' => now(),
            'observaciones' => $data['observaciones'] ?? $tramite->observaciones,
        ]);

        return back()->with('success', 'Trámite rechazado.');
    }
}
