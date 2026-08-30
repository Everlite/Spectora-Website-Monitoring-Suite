<x-auth-layout>
    <h1 class="text-lg font-extrabold text-white tracking-tight">E-Mail bestätigen</h1>
    <p class="text-xs text-studio-muted mt-1">Bitte den Link in der E-Mail öffnen. Kein Mail bekommen? Wir senden ihn erneut.</p>

    @if (session('status') == 'verification-link-sent')
        <p class="mt-4 text-xs font-bold text-studio-emerald">Neuer Bestätigungslink wurde gesendet.</p>
    @endif

    <div class="mt-6 flex items-center justify-between gap-3">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn-spectora-primary">Link erneut senden</button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-spectora-ghost">Abmelden</button>
        </form>
    </div>
</x-auth-layout>
