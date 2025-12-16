<x-app-layout>
    <div class="flex min-h-screen items-center justify-center px-4">
        <div class="w-full max-w-md rounded-2xl border border-gray-200 bg-white p-8 shadow-lg">
            <div class="mb-8 text-center">
                <a href="{{ route('home') }}">
                    <h1 class="text-3xl font-bold text-gray-900 uppercase">Cube France</h1>
                </a>
                <h2 class="mt-2 text-2xl font-semibold text-gray-800">Mot de passe oublié</h2>
            </div>

            <div class="mb-6 text-sm text-gray-600">
                Vous avez oublié votre mot de passe ? Pas de problème. Indiquez-nous votre adresse email et nous vous enverrons un lien pour le réinitialiser.
            </div>

            @if (session('status'))
                <div class="mb-4 rounded-lg bg-green-100 p-4 text-sm text-green-700">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                @csrf

                <div>
                    <label class="text-sm font-medium text-gray-700">
                        Adresse email
                        <span class="text-red-600">*</span>
                    </label>
                    <input
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        class="mt-1 w-full rounded-lg border-gray-300 bg-gray-50 px-4 py-3 focus:bg-white focus:ring-2 focus:ring-blue-200"
                        required
                        autofocus
                    />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <button
                    type="submit"
                    class="w-full rounded-lg bg-blue-600 py-3 font-semibold text-white hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                >
                    Envoyer le lien de réinitialisation
                </button>
            </form>

            <p class="mt-6 text-center text-sm text-gray-600">
                <a href="{{ route('login') }}" class="font-semibold text-blue-600 hover:text-blue-700">
                    &larr; Retour à la connexion
                </a>
            </p>
        </div>
    </div>
</x-app-layout>
