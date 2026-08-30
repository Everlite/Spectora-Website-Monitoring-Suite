<x-auth-layout>
    <h1 class="text-lg font-extrabold text-white tracking-tight">Passwort bestätigen</h1>
    <p class="text-xs text-studio-muted mt-1">Dieser Bereich ist geschützt. Bitte Passwort erneut eingeben.</p>

    <form method="POST" action="{{ route('password.confirm') }}" class="mt-6 space-y-4">
        @csrf

        <div>
            <label for="password" class="text-xs font-bold text-studio-muted">Passwort</label>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                class="spectora-input mt-1.5">
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <button type="submit" class="btn-spectora-primary w-full py-2.5">Bestätigen</button>
    </form>
</x-auth-layout>
