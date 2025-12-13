<x-commercial-layout>
    @php
        $reports = [
            [
                "title" => "Analyse des ventes",
                "description" => "Chiffre d’affaires par région et commercial.",
                "url" => "URL_POWER_BI_1",
            ],
            [
                "title" => "Performance produits",
                "description" => "Top ventes, marges et stocks.",
                "url" => "URL_POWER_BI_2",
            ],
            [
                "title" => "Suivi des objectifs",
                "description" => "Comparatif N / N-1 et KPIs.",
                "url" => "URL_POWER_BI_3",
            ],
            [
                "title" => "Portefeuille clients",
                "description" => "Segmentation et fidélisation.",
                "url" => "URL_POWER_BI_4",
            ],
        ];
    @endphp

    <div class="mx-auto max-w-6xl px-6 py-10">
        <h1 class="mb-8 text-2xl font-semibold text-gray-900">Tableau de bord commercial</h1>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            @foreach ($reports as $report)
                <div class="rounded-lg border bg-white p-5">
                    <h2 class="text-lg font-medium text-gray-900">
                        {{ $report["title"] }}
                    </h2>

                    <p class="mt-1 text-sm text-gray-600">
                        {{ $report["description"] }}
                    </p>

                    <button
                        onclick="openReport('{{ $report["url"] }}', '{{ $report["title"] }}')"
                        class="mt-4 text-sm font-medium text-indigo-600 hover:underline"
                    >
                        Ouvrir le rapport →
                    </button>
                </div>
            @endforeach
        </div>
    </div>

    <div id="reportModal" class="fixed inset-0 z-50 hidden bg-black/70">
        <div class="flex h-full items-center justify-center p-4">
            <div class="flex h-dvh w-dvw flex-col rounded bg-white">
                <div class="flex items-center justify-between border-b px-4 py-3">
                    <h3 id="modalTitle" class="text-sm font-medium text-gray-800"></h3>
                    <button onclick="closeReport()" class="text-gray-500 hover:text-gray-800">✕</button>
                </div>

                <iframe id="reportIframe" class="h-full w-full border-0"></iframe>
            </div>
        </div>
    </div>

    <script>
        function openReport(url, title) {
            document.getElementById('modalTitle').innerText = title;
            document.getElementById('reportIframe').src = url;
            document.getElementById('reportModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeReport() {
            document.getElementById('reportIframe').src = '';
            document.getElementById('reportModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
    </script>
</x-commercial-layout>
