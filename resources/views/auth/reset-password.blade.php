<x-app-layout>
    <div class="flex min-h-screen items-center justify-center px-4">
        <div class="w-full max-w-md rounded-2xl border border-gray-200 bg-white p-8 shadow-lg">
            <div class="mb-8 text-center">
                <a href="{{ route('home') }}">
                    <h1 class="text-3xl font-bold text-gray-900 uppercase">Cube France</h1>
                </a>
                <h2 class="mt-2 text-2xl font-semibold text-gray-800">Réinitialiser le mot de passe</h2>
            </div>

            <form method="POST" action="{{ route('password.store') }}" class="space-y-6">
                @csrf

                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <div>
                    <label class="text-sm font-medium text-gray-700">
                        Adresse email
                        <span class="text-red-600">*</span>
                    </label>
                    <input
                        type="email"
                        name="email"
                        value="{{ old('email', $request->email) }}"
                        class="mt-1 w-full rounded-lg border-gray-300 bg-gray-50 px-4 py-3 focus:bg-white focus:ring-2 focus:ring-blue-200"
                        required
                        autofocus
                    />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-700">
                        Nouveau mot de passe
                        <span class="text-red-600">*</span>
                    </label>
                    <input
                        type="password"
                        name="password"
                        class="mt-1 w-full rounded-lg border-gray-300 bg-gray-50 px-4 py-3 focus:bg-white focus:ring-2 focus:ring-blue-200"
                        required
                    />
                    <p class="mt-1 text-xs text-gray-500">
                        Minimum 12 caractères avec majuscule, minuscule, chiffre et caractère spécial (@$!%*?&#)
                    </p>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-700">
                        Confirmer le mot de passe
                        <span class="text-red-600">*</span>
                    </label>
                    <input
                        type="password"
                        name="password_confirmation"
                        class="mt-1 w-full rounded-lg border-gray-300 bg-gray-50 px-4 py-3 focus:bg-white focus:ring-2 focus:ring-blue-200"
                        required
                    />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <button
                    type="submit"
                    class="w-full rounded-lg bg-blue-600 py-3 font-semibold text-white hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                >
                    Réinitialiser le mot de passe
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
