@props([
    'name',
    'size' => 24,
])

@php
    $size = max(8, min(128, (int) $size));
@endphp

<iconify-icon
    icon="{{ $name }}"
    width="{{ $size }}"
    height="{{ $size }}"
    aria-hidden="true"
    {{ $attributes->class(['iconify-icon']) }}
></iconify-icon>
