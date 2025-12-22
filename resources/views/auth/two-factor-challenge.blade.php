<x-app-layout>
    <div class="flex min-h-screen items-center justify-center px-4">
        <div class="w-full max-w-md rounded-2xl border border-gray-200 bg-white p-8 shadow-lg">
            <x-flash-message key="error" type="error" />

            <div class="mb-8 text-center">
                <h1 class="text-3xl font-bold text-gray-900 uppercase">Cube France</h1>
                <h2 class="mt-2 text-2xl font-semibold text-gray-800">Double authentification</h2>
                <p class="text-sm text-gray-600">Entrez le code de votre application d’authentification</p>
            </div>

            <form method="POST" action="{{ route("two-factor.verify") }}" class="space-y-6">
                @csrf

                <div>
                    <label for="code" class="text-sm font-medium text-gray-700">
                        Code de vérification
                        <span class="text-red-600">*</span>
                    </label>

                    <input
                        type="text"
                        id="code"
                        name="code"
                        maxlength="20"
                        autofocus
                        required
                        placeholder="000000"
                        class="mt-1 w-full rounded-lg border-gray-300 bg-gray-50 px-4 py-3 text-center text-lg tracking-widest focus:bg-white focus:ring-2 focus:ring-blue-200"
                    />

                    <x-input-error :messages="$errors->get('code')" class="mt-2" />
                </div>

                <button
                    class="w-full rounded-lg bg-blue-600 py-3 font-semibold text-white hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                >
                    Vérifier
                </button>
            </form>

            <div class="mt-4 text-center">
                <p class="text-xs text-gray-500">Vous pouvez aussi utiliser un code de récupération.</p>
            </div>

            <div class="mt-6 text-center">
                <a href="{{ route("login") }}" class="text-sm text-blue-600 hover:text-blue-700">&larr; Retour</a>
            </div>
        </div>
    </div>
</x-app-layout>
