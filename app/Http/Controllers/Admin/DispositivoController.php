<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DispositivoRequest;
use App\Models\Dispositivo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Throwable;

class DispositivoController extends Controller
{
    public function index(Request $request): View
    {
        $query = Dispositivo::query()->withCount('sesionesControl');
        $busqueda = trim((string) $request->query('q', ''));

        if ($busqueda !== '') {
            $query->where(function (Builder $q) use ($busqueda) {
                $q->where('nombre', 'like', "%{$busqueda}%")
                  ->orWhere('tipo', 'like', "%{$busqueda}%")
                  ->orWhere('marca', 'like', "%{$busqueda}%")
                  ->orWhere('modelo', 'like', "%{$busqueda}%")
                  ->orWhere('numero_serie', 'like', "%{$busqueda}%");
            });
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->query('tipo'));
        }

        if ($request->query('estado') === 'activos') {
            $query->where('activo', true);
        } elseif ($request->query('estado') === 'inactivos') {
            $query->where('activo', false);
        }

        switch ($request->query('orden')) {
            case 'nombre':
                $query->orderBy('nombre')->orderBy('id');
                break;
            case 'marca':
                $query->orderBy('marca')->orderBy('nombre');
                break;
            default:
                $query->latest('id');
        }

        $dispositivos = $query->paginate(12)->withQueryString();
        $tipos = Dispositivo::query()
            ->whereNotNull('tipo')
            ->where('tipo', '<>', '')
            ->distinct()
            ->orderBy('tipo')
            ->pluck('tipo');

        return view('admin.dispositivos.index', compact('dispositivos', 'tipos'));
    }

    public function create(): View
    {
        return view('admin.dispositivos.create');
    }

    public function store(DispositivoRequest $request): RedirectResponse
    {
        $datos = $this->datosFormulario($request);
        $imagenNueva = null;

        if ($request->hasFile('imagen')) {
            $imagenNueva = $request->file('imagen')->store('dispositivos', 'public');
            $datos['imagen'] = $imagenNueva;
        }

        try {
            $dispositivo = Dispositivo::create($datos);
        } catch (Throwable $exception) {
            if ($imagenNueva) {
                Storage::disk('public')->delete($imagenNueva);
            }
            throw $exception;
        }

        return redirect()
            ->route('admin.dispositivos.show', $dispositivo)
            ->with('success', 'Dispositivo registrado correctamente.');
    }

    public function show(Dispositivo $dispositivo): View
    {
        $dispositivo->loadCount('sesionesControl');

        return view('admin.dispositivos.show', compact('dispositivo'));
    }

    public function edit(Dispositivo $dispositivo): View
    {
        return view('admin.dispositivos.edit', compact('dispositivo'));
    }

    public function update(DispositivoRequest $request, Dispositivo $dispositivo): RedirectResponse
    {
        $datos = $this->datosFormulario($request);
        $imagenAnterior = $dispositivo->imagen;
        $imagenNueva = null;
        $reemplazarImagen = false;

        if ($request->hasFile('imagen')) {
            $imagenNueva = $request->file('imagen')->store('dispositivos', 'public');
            $datos['imagen'] = $imagenNueva;
            $reemplazarImagen = true;
        } elseif ($request->boolean('eliminar_imagen')) {
            $datos['imagen'] = null;
            $reemplazarImagen = true;
        }

        try {
            $dispositivo->update($datos);
        } catch (Throwable $exception) {
            if ($imagenNueva) {
                Storage::disk('public')->delete($imagenNueva);
            }
            throw $exception;
        }

        if ($reemplazarImagen && $imagenAnterior && $imagenAnterior !== $imagenNueva) {
            Storage::disk('public')->delete($imagenAnterior);
        }

        return redirect()
            ->route('admin.dispositivos.show', $dispositivo)
            ->with('success', 'Dispositivo actualizado correctamente.');
    }

    public function destroy(Dispositivo $dispositivo): RedirectResponse
    {
        // Preservar el historial: un dispositivo utilizado no se elimina.
        if ($dispositivo->sesionesControl()->exists()) {
            return back()->with('error', 'Este dispositivo tiene controles registrados. Desactívalo en lugar de eliminarlo.');
        }

        $imagen = $dispositivo->imagen;
        $dispositivo->delete();

        if ($imagen) {
            Storage::disk('public')->delete($imagen);
        }

        return redirect()
            ->route('admin.dispositivos.index')
            ->with('success', 'Dispositivo eliminado correctamente.');
    }

    private function datosFormulario(DispositivoRequest $request): array
    {
        $datos = Arr::except($request->validated(), [
            'imagen',
            'eliminar_imagen',
        ]);

        $datos['activo'] = $request->boolean('activo');

        return $datos;
    }
}
