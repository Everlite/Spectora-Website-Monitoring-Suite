<x-app-layout>
    <div class="space-y-6">
        <div>
            <p class="sp-kicker">Konto</p>
            <h2 class="sp-display text-4xl text-studio-text mt-2">Einstellungen</h2>
            <p class="text-sm text-studio-muted mt-2">Profil, Passwort, Agentur-Logo für PDF-Reports.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <!-- Profile Info -->
            <div class="spectora-card p-5">
                @include('settings.partials.update-profile-information-form')
            </div>

            <!-- Password -->
            <div class="spectora-card p-5">
                @include('settings.partials.update-password-form')
            </div>

            <!-- Agency Branding -->
            <div class="spectora-card p-5 lg:col-span-2">
                <section>
                    <header class="mb-4 pb-3 border-b border-studio-border">
                        <h3 class="text-xs font-bold text-white uppercase tracking-wider">
                            {{ __('White-Label Agentur Branding') }}
                        </h3>
                        <p class="mt-0.5 text-xs text-studio-muted">
                            {{ __("Lade dein Agentur-Logo hoch, das automatisch auf PDF-Reports und Kunden-Exporten platziert wird.") }}
                        </p>
                    </header>
                
                    <form method="post" action="{{ route('agency.logo.update') }}" class="space-y-4" enctype="multipart/form-data">
                        @csrf
                
                        <div>
                            <input id="agency_logo" name="agency_logo" type="file" 
                                   class="block w-full text-xs text-studio-muted border border-studio-border rounded-studio-sm cursor-pointer bg-studio-bg focus:outline-none file:mr-4 file:py-2 file:px-4 file:rounded-studio-sm file:border-0 file:text-xs file:font-semibold file:bg-studio-brand file:text-white hover:file:bg-studio-brand-hover" accept="image/*" />
                            <x-input-error class="mt-2" :messages="$errors->get('agency_logo')" />
                        </div>
                
                        @if (Auth::user()->agency_logo_path)
                            <div class="mt-4 p-3 bg-studio-bg border border-studio-border rounded-studio-sm inline-block">
                                <p class="text-[11px] font-bold text-studio-muted mb-2">Aktuelles Logo:</p>
                                <img src="{{ asset('storage/' . Auth::user()->agency_logo_path) }}" alt="Agency Logo" class="h-12 object-contain">
                            </div>
                        @endif
                
                        <div class="flex items-center gap-3 pt-2">
                            <button type="submit" class="btn-spectora-primary">Logo speichern</button>
                
                            @if (session('status') === 'agency-logo-updated')
                                <span x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2500)" class="text-xs text-studio-emerald font-bold">
                                    ✓ Gespeichert!
                                </span>
                            @endif
                        </div>
                    </form>
                </section>
            </div>
        </div>
    </div>
</x-app-layout>
