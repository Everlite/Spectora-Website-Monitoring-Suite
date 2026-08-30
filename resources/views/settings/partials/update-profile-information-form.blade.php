<section>
    <header>
        <h2 class="text-xs font-bold text-white uppercase tracking-wider">
            Profil
        </h2>

        <p class="mt-1 text-xs text-studio-muted">
            Name, E-Mail, Zeitzone und Alert-Webhook.
        </p>
    </header>


    <form method="post" action="{{ route('settings.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="first_name" :value="__('First Name')" />
            <x-text-input id="first_name" name="first_name" type="text" class="mt-1 block w-full" :value="old('first_name', $user->first_name)" required autofocus autocomplete="given-name" />
            <x-input-error class="mt-2" :messages="$errors->get('first_name')" />
        </div>

        <div>
            <x-input-label for="last_name" :value="__('Last Name')" />
            <x-text-input id="last_name" name="last_name" type="text" class="mt-1 block w-full" :value="old('last_name', $user->last_name)" autocomplete="family-name" />
            <x-input-error class="mt-2" :messages="$errors->get('last_name')" />
        </div>

        <div>
            <x-input-label for="company_name" :value="__('Company Name')" />
            <x-text-input id="company_name" name="company_name" type="text" class="mt-1 block w-full" :value="old('company_name', $user->company_name)" required autocomplete="organization" />
            <x-input-error class="mt-2" :messages="$errors->get('company_name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

        </div>

        <div>
            <x-input-label for="timezone" :value="__('Timezone')" />
            <select id="timezone" name="timezone" class="spectora-input mt-1">
                @foreach(timezone_identifiers_list() as $timezone)
                    <option value="{{ $timezone }}" {{ old('timezone', $user->timezone) == $timezone ? 'selected' : '' }}>
                        {{ $timezone }}
                    </option>
                @endforeach
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('timezone')" />
        </div>

        <div>
            <x-input-label for="webhook_url" :value="__('Discord / Slack Webhook URL (Optional)')" />
            <x-text-input id="webhook_url" name="webhook_url" type="url" placeholder="https://discord.com/api/webhooks/... or https://hooks.slack.com/..." class="mt-1 block w-full" :value="old('webhook_url', $user->webhook_url)" />
            <p class="mt-1 text-xs text-studio-muted">Discord- oder Slack-Webhook für Ausfall- und Recovery-Alerts.</p>
            <x-input-error class="mt-2" :messages="$errors->get('webhook_url')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-xs font-bold text-studio-emerald"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
