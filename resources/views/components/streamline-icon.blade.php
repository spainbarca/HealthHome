@props([
    'name',
    'size' => 24,
])

@php
    /*
    |--------------------------------------------------------------------------
    | Validar el nombre del icono
    |--------------------------------------------------------------------------
    */
    if (! is_string($name) ||
        ! preg_match('/\A[a-z0-9-]+(?:\/[a-z0-9-]+)*\z/D', $name)) {
        throw new \InvalidArgumentException(
            'Nombre de icono Streamline no válido.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Generar el nombre registrado en Blade Icons
    |--------------------------------------------------------------------------
    |
    | sharp/line/interface-essential/new-file
    | pasa a:
    | sl-sharp.line.interface-essential.new-file
    |
    */
    $iconName = 'sl-' . str_replace('/', '.', $name);

    /*
    |--------------------------------------------------------------------------
    | Tamaño del icono
    |--------------------------------------------------------------------------
    */
    $size = max(8, min(128, (int) $size));
@endphp

<span
    {{ $attributes->class(['streamline-icon'])->merge([
        'style' => "width: {$size}px; height: {$size}px;",
    ]) }}
>
    <x-icon :name="$iconName" />
</span>
