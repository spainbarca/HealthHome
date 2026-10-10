{{-- Usa la librería CSS Health Icons ya instalada; no incrusta ni descarga SVG. --}}
@php
    $healthIconFilename = trim((string) ($filename ?? ''));
    $healthIconFilename = preg_replace('/^healthicons-/i', '', $healthIconFilename);
    $healthIconFilename = strtolower($healthIconFilename);
    $healthIconClass = preg_match('/^[a-z0-9][a-z0-9_-]*$/', $healthIconFilename)
        ? 'healthicons-' . $healthIconFilename
        : null;
@endphp
@if ($healthIconClass)
    <i class="{{ $healthIconClass }} {{ $class ?? '' }}" aria-hidden="true"></i>
@elseif (!empty($fallbackText))
    <span class="text-muted small">{{ $fallbackText }}</span>
@endif
