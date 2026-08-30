<x-auth-layout>
    <h1 class="text-lg font-extrabold text-white tracking-tight">Passwort zurücksetzen</h1>
    <p class="text-xs text-studio-muted mt-1">Wir schicken dir einen Link an deine E-Mail-Adresse.</p>

    <x-auth-session-status class="mt-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="mt-6 space-y-4">
        @csrf

        <div>
            <label for="email" class="text-xs font-bold text-studio-muted">E-Mail</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                class="spectora-input mt-1.5">
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <button type="submit" class="btn-spectora-primary w-full py-2.5">Link senden</button>

        <p class="text-center text-xs text-studio-muted">
            <a href="{{ route('login') }}" class="font-bold text-studio-brand hover:text-studio-brand-hover">Zurück zur Anmeldung</a>
        </p>
    </form>
</x-auth-layout>
