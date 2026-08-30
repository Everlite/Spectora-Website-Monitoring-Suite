<x-auth-layout>
    <h1 class="text-lg font-extrabold text-white tracking-tight">Neues Passwort</h1>
    <p class="text-xs text-studio-muted mt-1">Wähle ein neues Passwort für dein Konto.</p>

    <form method="POST" action="{{ route('password.store') }}" class="mt-6 space-y-4">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div>
            <label for="email" class="text-xs font-bold text-studio-muted">E-Mail</label>
            <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username"
                class="spectora-input mt-1.5">
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <label for="password" class="text-xs font-bold text-studio-muted">Passwort</label>
            <input id="password" type="password" name="password" required autocomplete="new-password"
                class="spectora-input mt-1.5">
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <label for="password_confirmation" class="text-xs font-bold text-studio-muted">Passwort bestätigen</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                class="spectora-input mt-1.5">
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <button type="submit" class="btn-spectora-primary w-full py-2.5">Passwort speichern</button>
    </form>
</x-auth-layout>
