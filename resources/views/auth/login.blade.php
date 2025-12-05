<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <!-- Error Message -->
    @if (session("error"))
        <div class="mb-4 rounded border border-red-400 bg-red-100 p-4 text-red-700">
            {{ session("error") }}
        </div>
    @endif

    <form method="POST" action="{{ route("login") }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Adresse électronique')" />
            <x-text-input
                id="email"
                class="mt-1 block w-full"
                type="email"
                name="email"
                :value="old('email')"
                required
                autofocus
                autocomplete="username"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Mot de passe')" />

            <x-text-input
                id="password"
                class="mt-1 block w-full"
                type="password"
                name="password"
                required
                autocomplete="current-password"
            />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4 flex items-center justify-end">
            {{-- @if (Route::has("password.request")) --}}
            {{-- <a --}}
            {{-- class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-none dark:text-gray-400 dark:hover:text-gray-100 dark:focus:ring-offset-gray-800" --}}
            {{-- href="{{ route("password.request") }}" --}}
            {{-- > --}}
            {{-- {{ __("Forgot your password?") }} --}}
            {{-- </a> --}}
            {{-- @endif --}}

            <x-primary-button class="ms-3">
                {{ __("Se connecter") }}
            </x-primary-button>
        </div>
    </form>

    <div class="mt-6 text-center">
        <p class="text-sm text-gray-600 dark:text-gray-400">
            {{ __("Pas de compte client ?") }}
        </p>
        <a
            href="{{ route("register") }}"
            class="mt-2 inline-flex items-center justify-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition duration-150 ease-in-out hover:bg-gray-700 focus:bg-gray-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-none active:bg-gray-900 dark:bg-gray-200 dark:text-gray-800 dark:hover:bg-white dark:focus:bg-white dark:focus:ring-offset-gray-800 dark:active:bg-gray-300"
        >
            {{ __("Créer un compte client") }}
        </a>
    </div>
</x-guest-layout>
