<section class="space-y-8">
    <header class="border-b border-gray-100 dark:border-gray-700 pb-5">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 flex items-center gap-2">
            <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
            </svg>
            {{ __('User Management') }}
        </h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Manage access, roles, and administrative users for this self-hosted Spectora suite.') }}
        </p>
    </header>

    @if ($errors->has('delete_user'))
        <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
            <span class="font-medium">Error!</span> {{ $errors->first('delete_user') }}
        </div>
    @endif

    <!-- Part A: User Directory -->
    <div class="bg-gray-50/50 dark:bg-gray-900/30 rounded-xl p-6 border border-gray-100 dark:border-gray-800/80">
        <h3 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-4 flex items-center gap-2">
            {{ __('User Directory') }}
            <span class="px-2.5 py-0.5 text-xs font-semibold bg-indigo-50 text-indigo-600 dark:bg-indigo-950/50 dark:text-indigo-400 rounded-full">
                {{ $users->count() }} {{ trans_choice('user|users', $users->count()) }}
            </span>
        </h3>

        <div class="overflow-x-auto rounded-lg border border-gray-200/60 dark:border-gray-700/60">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800/50">
                    <tr>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            {{ __('Name') }}
                        </th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            {{ __('Email') }}
                        </th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            {{ __('Role') }}
                        </th>
                        <th scope="col" class="px-6 py-3.5 class text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            {{ __('Timezone') }}
                        </th>
                        <th scope="col" class="relative px-6 py-3.5 text-right">
                            <span class="sr-only">{{ __('Actions') }}</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800/20 divide-y divide-gray-150 dark:divide-gray-700/50">
                    @foreach ($users as $u)
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-800/30 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gradient-to-tr from-indigo-500 to-violet-500 flex items-center justify-center text-white font-bold shadow-sm">
                                        {{ strtoupper(substr($u->first_name, 0, 1) . substr($u->last_name, 0, 1)) }}
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100 flex items-center gap-1.5">
                                            {{ $u->name }}
                                            @if ($u->id === auth()->id())
                                                <span class="px-2 py-0.2 text-[10px] font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded border border-gray-200 dark:border-gray-600">{{ __('You') }}</span>
                                            @endif
                                        </div>
                                        <div class="text-xs text-gray-400 dark:text-gray-500">
                                            {{ __('Created') }} {{ $u->created_at->format('M d, Y') }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                {{ $u->email }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if ($u->is_admin)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-400 border border-emerald-200/50 dark:border-emerald-900/30">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        {{ __('Administrator') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                        {{ __('User') }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $u->timezone }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                @if ($u->id !== auth()->id())
                                    <form method="POST" action="{{ route('users.destroy', $u) }}" onsubmit="return confirm('Are you sure you want to permanently delete this user account? All access will be revoked immediately.');" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 transition-colors font-medium text-sm">
                                            {{ __('Delete') }}
                                        </button>
                                    </form>
                                @else
                                    <span class="text-gray-300 dark:text-gray-600 text-sm cursor-not-allowed">{{ __('Delete') }}</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Part B: Create New User -->
    <div class="bg-gray-50/50 dark:bg-gray-900/30 rounded-xl p-6 border border-gray-100 dark:border-gray-800/80 mt-8">
        <h3 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
            </svg>
            {{ __('Add New User') }}
        </h3>

        <form method="POST" action="{{ route('users.store') }}" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- First Name -->
                <div>
                    <x-input-label for="new_first_name" :value="__('First Name')" />
                    <x-text-input id="new_first_name" name="first_name" type="text" class="mt-1 block w-full" :value="old('first_name')" required autocomplete="given-name" />
                    <x-input-error class="mt-2" :messages="$errors->get('first_name')" />
                </div>

                <!-- Last Name -->
                <div>
                    <x-input-label for="new_last_name" :value="__('Last Name')" />
                    <x-text-input id="new_last_name" name="last_name" type="text" class="mt-1 block w-full" :value="old('last_name')" required autocomplete="family-name" />
                    <x-input-error class="mt-2" :messages="$errors->get('last_name')" />
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Email Address -->
                <div>
                    <x-input-label for="new_email" :value="__('Email Address')" />
                    <x-text-input id="new_email" name="email" type="email" class="mt-1 block w-full" :value="old('email')" required autocomplete="username" />
                    <x-input-error class="mt-2" :messages="$errors->get('email')" />
                </div>

                <!-- Temporary Password -->
                <div>
                    <x-input-label for="new_password" :value="__('Temporary Password')" />
                    <x-text-input id="new_password" name="password" type="password" class="mt-1 block w-full" required autocomplete="new-password" placeholder="Min. 8 characters" />
                    <x-input-error class="mt-2" :messages="$errors->get('password')" />
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Timezone -->
                <div>
                    <x-input-label for="new_timezone" :value="__('Timezone')" />
                    <select id="new_timezone" name="timezone" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                        @foreach(timezone_identifiers_list() as $timezone)
                            <option value="{{ $timezone }}" {{ old('timezone', $user->timezone) == $timezone ? 'selected' : '' }}>
                                {{ $timezone }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('timezone')" />
                </div>

                <!-- Administrator Role Checkbox -->
                <div class="flex items-center h-full pt-6">
                    <label for="is_admin" class="inline-flex items-center cursor-pointer">
                        <input id="is_admin" type="checkbox" name="is_admin" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" {{ old('is_admin') ? 'checked' : '' }}>
                        <span class="ms-2 text-sm text-gray-700 dark:text-gray-300 font-medium">{{ __('Assign Administrator Privileges') }}</span>
                    </label>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <x-primary-button>{{ __('Create User') }}</x-primary-button>

                @if (session('status') === 'user-created')
                    <p
                        x-data="{ show: true }"
                        x-show="show"
                        x-transition
                        x-init="setTimeout(() => show = false, 3000)"
                        class="text-sm text-emerald-600 dark:text-emerald-400 font-medium"
                    >{{ __('User account created successfully.') }}</p>
                @endif
                
                @if (session('status') === 'user-deleted')
                    <p
                        x-data="{ show: true }"
                        x-show="show"
                        x-transition
                        x-init="setTimeout(() => show = false, 3000)"
                        class="text-sm text-amber-600 dark:text-amber-400 font-medium"
                    >{{ __('User account deleted.') }}</p>
                @endif
            </div>
        </form>
    </div>
</section>
