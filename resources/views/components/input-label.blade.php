@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-bold text-xs text-white mb-1.5']) }}>
    {{ $value ?? $slot }}
</label>
