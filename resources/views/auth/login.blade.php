<x-auth-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <h1 class="sp-display text-4xl text-studio-text">Anmelden</h1>
    <p class="text-sm text-studio-muted mt-2">Zugang zu deiner Instanz.</p>

    <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
        @csrf

        <div>
            <label for="email" class="text-xs font-bold text-studio-muted">E-Mail</label>
            <input type="email" name="email" id="email" required autofocus autocomplete="username"
                class="spectora-input mt-1.5"
                value="{{ old('email') }}">
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <label for="password" class="text-xs font-bold text-studio-muted">Passwort</label>
            <input type="password" name="password" id="password" required autocomplete="current-password"
                class="spectora-input mt-1.5">
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between">
            <label for="remember_me" class="flex items-center gap-2 text-xs text-studio-muted">
                <input id="remember_me" type="checkbox" name="remember" class="rounded bg-studio-bg border-studio-border text-studio-brand focus:ring-studio-brand">
                Angemeldet bleiben
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-xs text-studio-brand hover:text-studio-brand-hover">Passwort vergessen?</a>
            @endif
        </div>

        <button type="submit" class="btn-spectora-primary w-full py-2.5">
            Anmelden
        </button>

        @if (config('auth.registration_enabled'))
            <p class="text-center text-xs text-studio-muted">
                Noch kein Konto?
                <a href="{{ route('register') }}" class="font-bold text-studio-brand hover:text-studio-brand-hover">Registrieren</a>
            </p>
        @endif
    </form>
</x-auth-layout>
