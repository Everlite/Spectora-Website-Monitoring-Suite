<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn-horizon-primary']) }}>
    {{ $slot }}
</button>
