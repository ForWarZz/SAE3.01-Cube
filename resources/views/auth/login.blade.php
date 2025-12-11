<x-app-layout>
    <div class="flex min-h-screen items-center justify-center px-4">
        <div class="w-full max-w-md rounded-2xl border border-gray-200 bg-white p-8 shadow-lg">
            {{-- Titre --}}
            <div class="mb-8 text-center">
                <a href="{{ route("home") }}">
                    <h1 class="text-3xl font-bold text-gray-900 uppercase">Cube France</h1>
                </a>
                <h2 class="mt-2 text-2xl font-semibold text-gray-800">Connexion</h2>
                <p class="text-sm text-gray-600">Accédez à votre espace client</p>
            </div>

            <form method="POST" action="{{ route("login") }}" class="space-y-6">
                @csrf

                {{-- Email --}}
                <div>
                    <label class="text-sm font-medium text-gray-700">
                        Adresse email
                        <span class="text-red-600">*</span>
                    </label>
                    <input
                        type="email"
                        name="email"
                        value="{{ old("email") }}"
                        class="mt-1 w-full rounded-lg border-gray-300 bg-gray-50 px-4 py-3 focus:bg-white focus:ring-2 focus:ring-blue-200"
                        required
                    />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                {{-- Mot de passe --}}
                <div>
                    <label class="text-sm font-medium text-gray-700">
                        Mot de passe
                        <span class="text-red-600">*</span>
                    </label>
                    <input
                        type="password"
                        name="password"
                        class="mt-1 w-full rounded-lg border-gray-300 bg-gray-50 px-4 py-3 focus:bg-white focus:ring-2 focus:ring-blue-200"
                        required
                    />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <button
                    class="w-full rounded-lg bg-blue-600 py-3 font-semibold text-white hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                >
                    Se connecter
                </button>
            </form>

            <p class="mt-6 text-center text-sm text-gray-600">
                Pas encore de compte ?
                <a href="{{ route("register") }}" class="font-semibold text-blue-600 hover:text-blue-700">Créer un compte</a>
            </p>
            <p class="mt-6 text-center text-sm text-gray-600">
                <a href="{{ route("commercial.login") }}" class="font-semibold text-blue-600 hover:text-blue-700">Connexion Service Commercial</a>
            </p>
        </div>
    </div>
</x-app-layout>
