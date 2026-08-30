<x-auth-layout>
    <h1 class="text-lg font-extrabold text-white tracking-tight">Konto anlegen</h1>
    <p class="text-xs text-studio-muted mt-1">Erste Administratorin oder Administrator für diese Instanz.</p>

    <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" class="mt-6 space-y-4">
        @csrf

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label for="first_name" class="text-xs font-bold text-studio-muted">Vorname</label>
                <input type="text" name="first_name" id="first_name" required autofocus
                    class="spectora-input mt-1.5" value="{{ old('first_name') }}">
                <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
            </div>
            <div>
                <label for="last_name" class="text-xs font-bold text-studio-muted">Nachname</label>
                <input type="text" name="last_name" id="last_name" required
                    class="spectora-input mt-1.5" value="{{ old('last_name') }}">
                <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
            </div>
        </div>

        <div>
            <label for="company_name" class="text-xs font-bold text-studio-muted">Agentur / Firma</label>
            <input type="text" name="company_name" id="company_name" required
                class="spectora-input mt-1.5" value="{{ old('company_name') }}">
            <x-input-error :messages="$errors->get('company_name')" class="mt-2" />
        </div>

        <div>
            <label for="logo" class="text-xs font-bold text-studio-muted">Logo <span class="font-normal">(optional)</span></label>
            <input type="file" name="logo" id="logo" accept="image/jpeg,image/png,image/jpg,image/gif,image/svg+xml"
                class="mt-1.5 block w-full text-xs text-studio-muted file:mr-3 file:py-1.5 file:px-3 file:rounded-studio-sm file:border-0 file:text-xs file:font-bold file:bg-studio-brand file:text-white">
            <x-input-error :messages="$errors->get('logo')" class="mt-2" />
        </div>

        <div>
            <label for="email" class="text-xs font-bold text-studio-muted">E-Mail</label>
            <input type="email" name="email" id="email" required autocomplete="username"
                class="spectora-input mt-1.5" value="{{ old('email') }}">
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <label for="password" class="text-xs font-bold text-studio-muted">Passwort</label>
            <input type="password" name="password" id="password" required autocomplete="new-password"
                class="spectora-input mt-1.5">
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <label for="password_confirmation" class="text-xs font-bold text-studio-muted">Passwort bestätigen</label>
            <input type="password" name="password_confirmation" id="password_confirmation" required autocomplete="new-password"
                class="spectora-input mt-1.5">
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <button type="submit" class="btn-spectora-primary w-full py-2.5">Registrieren</button>

        <p class="text-center text-xs text-studio-muted">
            Schon ein Konto?
            <a href="{{ route('login') }}" class="font-bold text-studio-brand hover:text-studio-brand-hover">Anmelden</a>
        </p>
    </form>
</x-auth-layout>
