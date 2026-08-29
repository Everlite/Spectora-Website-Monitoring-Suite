<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn-horizon-danger']) }}>
    {{ $slot }}
</button>
