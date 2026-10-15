<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ControlMedicoRequest;
use App\Models\Dispositivo;
use App\Models\Persona;
use App\Models\SesionControl;
use App\Models\Sintoma;
use App\Models\TipoControl;
use App\Services\ControlMedicoService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ControlMedicoController extends Controller
{
    public function index(Request $request): View
    {
        // Seleccionar persona es obligatorio para consultar el historial.
        // De esta manera nunca se ejecuta una consulta global de sesiones.
        $filtros = $request->validate([
            'persona_id' => ['nullable', 'integer', 'exists:personas,id'],
            'tipo_control_id' => ['nullable', 'integer', 'exists:tipos_control,id'],
            'desde' => ['nullable', 'date'],
            'hasta' => ['nullable', 'date', ...($request->filled('desde') ? ['after_or_equal:desde'] : [])],
            'q' => ['nullable', 'string', 'max:120'],
        ]);

        $personas = Persona::with('user')
            ->get()
            ->sortBy('nombre_completo', SORT_NATURAL | SORT_FLAG_CASE);

        $personaSeleccionada = null;
        $controles = null;
        $ultimaSesion = null;
        $tiposControl = collect();

        if ($request->filled('persona_id')) {
            $personaSeleccionada = Persona::with('user')
                ->withCount('sesionesControl')
                ->findOrFail((int) $filtros['persona_id']);

            $ultimaSesion = $personaSeleccionada->sesionesControl()
                ->orderByDesc('fecha_hora')
                ->orderByDesc('id')
                ->first();

            // Tipos que esta persona ha utilizado (no una lista global).
            $tiposControl = TipoControl::query()
                ->whereHas('sesionesControl', fn (Builder $q) => $q
                    ->where('persona_id', $personaSeleccionada->id))
                ->orderBy('nombre')
                ->get();

            $query = $personaSeleccionada->sesionesControl()
                ->with(['tipoControl', 'dispositivo', 'mediciones.valores.parametro'])
                ->withCount('mediciones');

            if ($request->filled('tipo_control_id')) {
                $query->where('tipo_control_id', (int) $filtros['tipo_control_id']);
            }

            if ($request->filled('desde')) {
                $query->whereDate('fecha_hora', '>=', $filtros['desde']);
            }

            if ($request->filled('hasta')) {
                $query->whereDate('fecha_hora', '<=', $filtros['hasta']);
            }

            $texto = trim((string) ($filtros['q'] ?? ''));
            if ($texto !== '') {
                $query->where(function (Builder $q) use ($texto) {
                    $q->whereHas('tipoControl', fn (Builder $tipo) =>
                        $tipo->where('nombre', 'like', "%{$texto}%"))
                      ->orWhereHas('dispositivo', fn (Builder $equipo) =>
                        $equipo->where('nombre', 'like', "%{$texto}%")
                            ->orWhere('marca', 'like', "%{$texto}%")
                            ->orWhere('modelo', 'like', "%{$texto}%"))
                      ->orWhere('observaciones', 'like', "%{$texto}%");
                });
            }

            // Orden descendente estable: primero fecha y hora, luego ID.
            $controles = $query
                ->orderByDesc('fecha_hora')
                ->orderByDesc('id')
                ->paginate(10)
                ->withQueryString();
        }

        return view('admin.controles.index', compact(
            'personas', 'personaSeleccionada', 'controles',
            'tiposControl', 'ultimaSesion'
        ));
    }

    public function create(Request $request): View
    {
        $personas = Persona::with('user')->get()->sortBy('nombre_completo');
        $tiposControl = TipoControl::activos()->whereHas('parametros')
            ->with('parametros')->orderBy('nombre')->get();
        $dispositivos = Dispositivo::activos()->orderBy('nombre')->get();
        $sintomas = Sintoma::activos()->orderBy('nombre')->get();
        $seleccionPersona = (int) $request->query('persona_id', 0);
        $medicionesIniciales = [];

        return view('admin.controles.create', compact(
            'personas', 'tiposControl', 'dispositivos', 'sintomas',
            'seleccionPersona', 'medicionesIniciales'
        ));
    }

    public function store(ControlMedicoRequest $request, ControlMedicoService $service): RedirectResponse
    {
        $control = $service->guardar($request->validated());

        return redirect()->route('admin.controles.show', $control)
            ->with('success', 'Control médico registrado correctamente.');
    }

    public function show(SesionControl $control): View
    {
        $control->load([
            'persona.user', 'tipoControl.parametros', 'dispositivo',
            'mediciones.valores.parametro', 'sintomas',
        ]);

        return view('admin.controles.show', compact('control'));
    }

    public function edit(SesionControl $control): View
    {
        $control->load(['tipoControl.parametros', 'mediciones.valores', 'sintomas']);
        $personas = Persona::with('user')->get()->sortBy('nombre_completo');
        // Incluimos el tipo actual aunque se haya desactivado tras crear la sesión.
        $tiposControl = TipoControl::where(function (Builder $q) use ($control) {
            $q->where('activo', true)->orWhere('id', $control->tipo_control_id);
        })->whereHas('parametros')->with('parametros')->orderBy('nombre')->get();
        $dispositivos = Dispositivo::where(function (Builder $q) use ($control) {
            $q->where('activo', true);
            if ($control->dispositivo_id !== null) {
                $q->orWhere('id', $control->dispositivo_id);
            }
        })->orderBy('nombre')->get();
        $sintomas = Sintoma::activos()->orWhereHas('sesionesControl', fn (Builder $q) => $q->whereKey($control->id))
            ->orderBy('nombre')->get();

        $medicionesIniciales = $control->mediciones->map(function ($medicion) {
            return [
                'id' => $medicion->id,
                'fecha_hora' => $medicion->fecha_hora->format('Y-m-d\TH:i'),
                'observaciones' => $medicion->observaciones,
                'valores' => $medicion->valores->mapWithKeys(fn ($v) => [(string) $v->tipo_parametro_id => $v->valor])->all(),
            ];
        })->values()->all();
        $seleccionPersona = $control->persona_id;

        return view('admin.controles.edit', compact(
            'control', 'personas', 'tiposControl', 'dispositivos',
            'sintomas', 'seleccionPersona', 'medicionesIniciales'
        ));
    }

    public function update(ControlMedicoRequest $request, SesionControl $control, ControlMedicoService $service): RedirectResponse
    {
        $control = $service->guardar($request->validated(), $control);

        return redirect()->route('admin.controles.show', $control)
            ->with('success', 'Control médico actualizado correctamente.');
    }

    public function destroy(SesionControl $control): RedirectResponse
    {
        if ($control->archivos()->exists() || $control->mediciones()->whereHas('archivos')->exists()) {
            return back()->with('error', 'No se puede eliminar una sesión con archivos o evidencias asociados.');
        }

        // Las FK de medicion_valores y mediciones deben configurarse con cascadeOnDelete().
        $personaId = $control->persona_id;
        $control->delete();

        return redirect()->route('admin.controles.index', ['persona_id' => $personaId])
            ->with('success', 'Sesión eliminada correctamente.');
    }
}
