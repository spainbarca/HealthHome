@php
    /** @var \App\Models\User|null $user */
    $avatarSize = $size ?? '';
    $mediaFoto = $user?->media?->first(fn ($media) => str_starts_with((string) $media->mime_type, 'image/'));
    $urlFoto = $mediaFoto?->getUrl();
    $iniciales = mb_strtoupper(
        mb_substr((string) ($user?->first_name ?: 'P'), 0, 1) .
        mb_substr((string) ($user?->last_name ?: ''), 0, 1)
    );
@endphp
@if ($urlFoto)
    <img class="hh-persona-avatar {{ $avatarSize }}" src="{{ $urlFoto }}" alt="Fotografía de {{ $user?->first_name }}">
@else
    <span class="hh-persona-avatar {{ $avatarSize }}" aria-label="Sin fotografía">{{ $iniciales }}</span>
@endif
