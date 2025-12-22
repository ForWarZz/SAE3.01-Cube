<x-guest-layout>
    <form method="POST" action="{{ route('two-factor.verify') }}">
        @csrf

        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Authentification à deux facteurs</h2>
            <p class="mt-2 text-sm text-gray-600">
                Entrez le code à six chiffres de votre application d'authentification ou un code de récupération.
            </p>
        </div>

        <!-- Code Input -->
        <div>
            <x-input-label for="code" :value="__('Code de vérification')" />
            <x-text-input id="code" class="block mt-1 w-full text-center text-lg tracking-widest"
                          type="text"
                          name="code"
                          maxlength="20"
                          required
                          autofocus
                          placeholder="000000" />
            <x-input-error :messages="$errors->get('code')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-6">
            <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-gray-900">
                &larr; Retour
            </a>

            <x-primary-button>
                {{ __('Vérifier') }}
            </x-primary-button>
        </div>

        <div class="mt-4 text-center">
            <p class="text-xs text-gray-500">
                Utilisez un code de récupération si vous n'avez pas accès à votre appareil.
            </p>
        </div>
    </form>
</x-guest-layout>
