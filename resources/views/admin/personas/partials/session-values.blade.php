<div class="hh-history-values mt-2">
    @forelse ($sesion->mediciones as $medicion)
        @foreach ($medicion->valores as $valor)
            <span class="badge bg-light text-dark border">
                @if ($sesion->mediciones->count() > 1) T{{ $medicion->numero_medicion }} · @endif
                {{ $valor->parametro?->nombre_corto ?: ($valor->parametro?->nombre ?: 'Parámetro') }}:
                {{ $valor->valor !== null ? $valor->valor : ($valor->valor_texto ?? '—') }}
                {{ $valor->parametro?->unidad }}
            </span>
        @endforeach
    @empty
        <span class="text-muted small">Sin lecturas registradas.</span>
    @endforelse
</div>
