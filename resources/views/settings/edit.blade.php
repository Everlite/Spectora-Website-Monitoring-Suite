<x-app-layout>
    <div class="space-y-6">
        <div>
            <h2 class="text-xl font-extrabold text-white tracking-tight">Einstellungen & Agentur-Profil</h2>
            <p class="text-xs text-[#A3AED0] mt-0.5">Verwalte dein Konto, Sicherheits-Passwörter und White-Label-Agentur-Branding.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Profile Info -->
            <div class="horizon-card p-6">
                @include('settings.partials.update-profile-information-form')
            </div>

            <!-- Password -->
            <div class="horizon-card p-6">
                @include('settings.partials.update-password-form')
            </div>

            <!-- Agency Branding -->
            <div class="horizon-card p-6 lg:col-span-2">
                <section>
                    <header class="mb-4">
                        <h3 class="text-sm font-bold text-white">
                            {{ __('White-Label Agentur Branding') }}
                        </h3>
                        <p class="mt-0.5 text-xs text-[#A3AED0]">
                            {{ __("Lade dein Agentur-Logo hoch, das automatisch auf PDF-Reports und Kunden-Exporten platziert wird.") }}
                        </p>
                    </header>
                
                    <form method="post" action="{{ route('agency.logo.update') }}" class="space-y-4" enctype="multipart/form-data">
                        @csrf
                
                        <div>
                            <input id="agency_logo" name="agency_logo" type="file" 
                                   class="block w-full text-xs text-[#A3AED0] border border-[#1B254B] rounded-horizon-sm cursor-pointer bg-[#0B1437] focus:outline-none file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-[#7551FF] file:text-white hover:file:bg-[#603BFF]" accept="image/*" />
                            <x-input-error class="mt-2" :messages="$errors->get('agency_logo')" />
                        </div>
                
                        @if (Auth::user()->agency_logo_path)
                            <div class="mt-4 p-3 bg-[#0B1437] border border-[#1B254B] rounded-horizon-sm inline-block">
                                <p class="text-[11px] font-bold text-[#A3AED0] mb-2">Aktuelles Logo:</p>
                                <img src="{{ asset('storage/' . Auth::user()->agency_logo_path) }}" alt="Agency Logo" class="h-12 object-contain">
                            </div>
                        @endif
                
                        <div class="flex items-center gap-3 pt-2">
                            <button type="submit" class="btn-horizon-primary">Logo speichern</button>
                
                            @if (session('status') === 'agency-logo-updated')
                                <span x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2500)" class="text-xs text-[#01B574] font-bold">
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
