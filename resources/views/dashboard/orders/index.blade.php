<x-app-layout>
    <div class="py-12">
        <div class="mx-auto max-w-7xl px-8">
            <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                <div class="p-6">
                    <div class="mb-6 flex items-center justify-between border-b border-gray-100 pb-4">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Mes commandes</h1>
                            <p class="mt-1 text-sm text-gray-500">Historique de vos commandes passées</p>
                        </div>
                        <a href="{{ route("dashboard.index") }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">
                            &larr; Retour au tableau de bord
                        </a>
                    </div>

                    @if ($orders->isEmpty())
                        <div class="py-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"
                                />
                            </svg>
                            <h3 class="mt-2 text-lg font-medium text-gray-900">Aucune commande</h3>
                            <p class="mt-1 text-sm text-gray-500">Vous n'avez pas encore passé de commande.</p>
                            <a
                                href="{{ route("home") }}"
                                class="mt-4 inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
                            >
                                Découvrir nos produits
                            </a>
                        </div>
                    @else
                        <div class="grid grid-cols-2 gap-6">
                            @foreach ($orders as $order)
                                <div
                                    class="flex flex-col justify-between rounded-lg border border-gray-200 bg-white p-5 transition hover:shadow-md"
                                >
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1 text-sm">
                                            <div class="mb-3 flex items-center justify-between">
                                                <div class="flex items-center gap-3">
                                                    <h3 class="font-bold text-gray-900">#{{ $order->number }}</h3>
                                                    <span
                                                        class="{{ $order->statusColors["bg"] }} {{ $order->statusColors["text"] }} inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                                                    >
                                                        {{ $order->statusLabel }}
                                                    </span>
                                                </div>
                                                <p class="text-gray-500">{{ $order->date }}</p>
                                            </div>

                                            @if ($order->tracking)
                                                <div class="mb-3">
                                                    <p class="text-xs text-gray-500">N° Suivi</p>
                                                    <p class="font-mono font-medium text-gray-700">{{ $order->tracking }}</p>
                                                </div>
                                            @endif

                                            @if ($order->address)
                                                <div class="mb-3 border-t border-gray-100 pt-3">
                                                    <p class="mb-1 text-xs font-medium tracking-wider text-gray-400 uppercase">Livraison</p>
                                                    <p class="font-medium text-gray-900">{{ $order->address->name }}</p>
                                                    <p class="text-gray-500">{{ $order->address->street }}</p>
                                                    <p class="text-gray-500">{{ $order->address->city }}</p>
                                                </div>
                                            @endif

                                            <div class="mt-3 space-y-1 border-t border-gray-100 pt-3">
                                                <div class="flex justify-between text-gray-500">
                                                    <span>{{ $order->countArticles }} article(s)</span>
                                                    <span>{{ number_format($order->financials->subtotal, 2, ",", " ") }} €</span>
                                                </div>

                                                @if ($order->financials->shipping > 0)
                                                    <div class="flex justify-between text-gray-500">
                                                        <span>Livraison</span>
                                                        <span>{{ number_format($order->financials->shipping, 2, ",", " ") }} €</span>
                                                    </div>
                                                @endif

                                                @if ($order->financials->discount > 0)
                                                    <div class="flex justify-between text-green-600">
                                                        <span>Remise (-{{ $order->financials->discountPercent }}%)</span>
                                                        <span>-{{ number_format($order->financials->discount, 2, ",", " ") }} €</span>
                                                    </div>
                                                @endif

                                                <div
                                                    class="mt-2 flex justify-between border-t border-gray-100 pt-2 font-bold text-gray-900"
                                                >
                                                    <span>Total</span>
                                                    <span>{{ number_format($order->financials->total, 2, ",", " ") }} €</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-4 flex justify-end border-t border-gray-100 pt-4">
                                        <a
                                            href="{{ route("dashboard.orders.show", $order->id) }}"
                                            class="inline-flex items-center gap-1 text-sm font-medium text-indigo-600 hover:text-indigo-800 hover:underline"
                                        >
                                            Voir le détail
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6">
                            {{ $orders->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
