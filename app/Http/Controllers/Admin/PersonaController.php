<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PersonaRequest;
use App\Models\Persona;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\View\View;

class PersonaController extends Controller
{
    public function index(Request $request): View
    {
        $query = Persona::query()->with('user')->withCount('sesionesControl');
        $q = trim((string) $request->query('q', ''));

        if ($q !== '') {
            $query->where(function (Builder $builder) use ($q) {
                $builder->where('numero_documento', 'like', "%{$q}%")
                    ->orWhere('parentesco', 'like', "%{$q}%")
                    ->orWhereHas('user', function (Builder $user) use ($q) {
                        $user->where('first_name', 'like', "%{$q}%")
                            ->orWhere('last_name', 'like', "%{$q}%")
                            ->orWhere('email', 'like', "%{$q}%");
                    });
            });
        }

        if ($request->filled('parentesco')) {
            $query->where('parentesco', $request->query('parentesco'));
        }

        if ($request->query('controles') === 'con') {
            $query->has('sesionesControl');
        } elseif ($request->query('controles') === 'sin') {
            $query->doesntHave('sesionesControl');
        }

        if ($request->query('orden') === 'nombre') {
            $query->orderBy(
                User::query()->select('first_name')->whereColumn('users.id', 'personas.user_id')->limit(1)
            )->orderBy('id');
        } else {
            $query->latest('id');
        }

        $personas = $query->paginate(12)->withQueryString();
        $parentescos = Persona::query()->whereNotNull('parentesco')
            ->where('parentesco', '<>', '')
            ->distinct()->orderBy('parentesco')->pluck('parentesco');

        return view('admin.personas.index', compact('personas', 'parentescos'));
    }

    public function create(): View
    {
        // Una ficha médica utiliza datos demográficos de un User existente.
        $usuarios = User::query()->whereDoesntHave('persona')
            ->orderBy('first_name')->orderBy('last_name')
            ->get(['id', 'first_name', 'last_name', 'email', 'dob', 'gender', 'phone']);

        return view('admin.personas.create', compact('usuarios'));
    }

    public function store(PersonaRequest $request): RedirectResponse
    {
        $persona = Persona::create(Arr::only($request->validated(), [
            'user_id', 'tipo_documento', 'numero_documento', 'parentesco',
        ]));

        return redirect()->route('admin.personas.show', $persona)
            ->with('success', 'Ficha personal creada correctamente.');
    }

    public function show(Persona $persona): View
    {
        $persona->load('user')->loadCount('sesionesControl');

        $sesiones = $persona->sesionesControl()
            ->with(['tipoControl', 'dispositivo', 'mediciones.valores.parametro'])
            ->orderByDesc('fecha_hora')
            ->paginate(10)
            ->withQueryString();

        $resumenTipos = $persona->sesionesControl()
            ->selectRaw('tipo_control_id, COUNT(*) AS total')
            ->with('tipoControl')
            ->groupBy('tipo_control_id')
            ->orderByDesc('total')
            ->get();

        $ultimaSesion = $persona->sesionesControl()
            ->with('tipoControl')
            ->orderByDesc('fecha_hora')
            ->first();

        return view('admin.personas.show', compact(
            'persona', 'sesiones', 'resumenTipos', 'ultimaSesion'
        ));
    }

    public function edit(Persona $persona): View
    {
        $persona->load('user');
        return view('admin.personas.edit', compact('persona'));
    }

    public function update(PersonaRequest $request, Persona $persona): RedirectResponse
    {
        $persona->update(Arr::only($request->validated(), [
            'tipo_documento', 'numero_documento', 'parentesco',
        ]));

        return redirect()->route('admin.personas.show', $persona)
            ->with('success', 'Ficha personal actualizada correctamente.');
    }

    public function destroy(Persona $persona): RedirectResponse
    {
        if ($persona->sesionesControl()->exists()) {
            return back()->with('error',
                'No se puede eliminar una ficha con controles médicos registrados. Conserva su historial.'
            );
        }

        // Solo eliminamos la ficha; el usuario y sus credenciales se conservan.
        $persona->delete();

        return redirect()->route('admin.personas.index')
            ->with('success', 'Ficha eliminada correctamente. El usuario permanece registrado.');
    }
}
