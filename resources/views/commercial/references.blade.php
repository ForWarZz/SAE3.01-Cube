<x-commercial-layout>
    <h1 class="mb-6 text-3xl font-bold text-gray-800">Créer une référence</h1>

    <div class="rounded-lg bg-white p-6 shadow-md">
        @if ($errors->any())
            <div class="mb-4 rounded border border-red-200 bg-red-50 p-3 text-red-700">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</x-commercial-layout>
