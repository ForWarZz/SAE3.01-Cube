@php
    use Carbon\Carbon;

    $orderDate = Carbon::parse($order->date_commande);
    $returnDeadline = $orderDate->copy()->addDays(14);
    $daysRemaining = now()->diffInDays($returnDeadline, false);
    $canReturn = $daysRemaining > 0;
@endphp

<x-app-layout>
    <div class="bg-gray-50 py-8">
        <div class="mx-auto max-w-4xl px-8">
            {{-- <nav class="mb-6"> --}}
            {{-- <div class="flex items-center gap-2 text-sm"> --}}
            {{-- <a --}}
            {{-- href="{{ route("dashboard.orders.index") }}" --}}
            {{-- class="font-medium text-gray-500 transition-colors hover:text-gray-900" --}}
            {{-- > --}}
            {{-- Mes commandes --}}
            {{-- </a> --}}
            {{-- <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"> --}}
            {{-- <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /> --}}
            {{-- </svg> --}}
            {{-- <a --}}
            {{-- href="{{ route("dashboard.orders.show", $order) }}" --}}
            {{-- class="font-medium text-gray-500 transition-colors hover:text-gray-900" --}}
            {{-- > --}}
            {{-- Commande #{{ $order->num_commande }} --}}
            {{-- </a> --}}
            {{-- <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"> --}}
            {{-- <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /> --}}
            {{-- </svg> --}}
            {{-- <span class="font-medium text-gray-900">Demande de retour</span> --}}
            {{-- </div> --}}
            {{-- </nav> --}}

            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Demander un retour</h1>
                <p class="mt-2 text-gray-600">Commande #{{ $order->num_commande }}</p>
            </div>

            <div class="{{ $canReturn ? "border-blue-200 bg-blue-50" : "border-red-200 bg-red-50" }} mb-6 rounded-xl border p-5">
                <div class="flex gap-4">
                    <div class="flex-shrink-0">
                        @if ($canReturn)
                            <x-heroicon-o-information-circle class="size-6 text-blue-600" />
                        @else
                            <x-heroicon-o-exclamation-triangle class="size-6 text-red-600" />
                        @endif
                    </div>
                    <div>
                        <h3 class="{{ $canReturn ? "text-blue-900" : "text-red-900" }} font-semibold">Droit de rétractation (14 jours)</h3>

                        @if ($canReturn)
                            <p class="mt-1 text-sm text-blue-800">
                                Vous avez encore
                                <strong>{{ $daysRemaining }} jour(s)</strong>
                                pour exercer votre droit de rétractation. Date limite :
                                <strong>{{ $returnDeadline->format("d/m/Y") }}</strong>
                            </p>
                            <p class="mt-2 text-xs text-blue-700">
                                Les articles doivent être retournés dans leur emballage d'origine, non utilisés et accompagnés de tous leurs
                                accessoires.
                            </p>
                        @else
                            <p class="mt-1 text-sm text-red-800">
                                Le délai de rétractation de 14 jours est expiré depuis le
                                <strong>{{ $returnDeadline->format("d/m/Y") }}</strong>
                                .
                            </p>
                            <p class="mt-2 text-xs text-red-700">
                                Vous ne pouvez plus exercer votre droit de rétractation pour cette commande. Contactez notre service client
                                pour toute question.
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            @if ($canReturn)
                <form class="space-y-6" action="{{ route("dashboard.orders.return.store", $order->id_commande) }}" method="POST">
                    @csrf

                    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                        <h2 class="mb-4 text-lg font-semibold text-gray-900">Articles à retourner</h2>
                        <p class="mb-6 text-sm text-gray-600">
                            Indiquez la quantité que vous souhaitez retourner pour chaque article. Laissez à 0 si vous ne souhaitez pas le
                            retourner.
                        </p>

                        <div class="space-y-4">
                            @foreach ($items as $index => $item)
                                <div
                                    class="flex items-start gap-4 rounded-lg border border-gray-200 p-4 transition-all hover:border-blue-300 hover:bg-blue-50/30"
                                >
                                    <div class="size-20 flex-shrink-0 overflow-hidden rounded-lg border border-gray-200">
                                        <img
                                            src="{{ $item->image }}"
                                            alt="{{ $item->name }}"
                                            class="h-full w-full object-contain p-1"
                                            loading="lazy"
                                        />
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        <h3 class="font-semibold text-gray-900">{{ $item->name }}</h3>
                                        @if ($item->subtitle)
                                            <p class="text-sm text-gray-500">{{ $item->subtitle }}</p>
                                        @endif

                                        <div class="mt-2 flex flex-wrap items-center gap-3 text-sm text-gray-600">
                                            @if ($item->colorName)
                                                <div class="flex items-center gap-1.5">
                                                    <span
                                                        class="h-3 w-3 rounded-full border border-gray-300"
                                                        style="background-color: {{ $item->colorHex }}"
                                                    ></span>
                                                    {{ $item->colorName }}
                                                </div>
                                            @endif

                                            @if ($item->size)
                                                <span>Taille : {{ $item->size }}</span>
                                            @endif

                                            <span class="font-medium">{{ number_format($item->unitPrice, 2, ",", " ") }} € / unité</span>
                                        </div>
                                    </div>

                                    <div class="flex-shrink-0 text-center">
                                        <label for="quantity-{{ $index }}" class="mb-1 block text-xs font-medium text-gray-700">
                                            Quantité à retourner
                                        </label>
                                        <input
                                            type="number"
                                            id="quantity-{{ $index }}"
                                            name="items[{{ $index }}][quantity]"
                                            min="0"
                                            max="{{ $item->availableQuantity }}"
                                            value="{{ old("items.{$index}.quantity", 0) }}"
                                            class="@error("items.{$index}.quantity") @enderror w-24 rounded-lg border-gray-300 border-red-500 text-center focus:border-blue-500 focus:ring-blue-500"
                                        />
                                        <p class="mt-1 text-center text-xs text-gray-500">max: {{ $item->availableQuantity }}</p>
                                        @error("items.{$index}.quantity")
                                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <input type="hidden" name="items[{{ $index }}][line_id]" value="{{ $item->lineId }}" />
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                        <label for="message" class="mb-4 block text-lg font-semibold text-gray-900">Message au service client</label>
                        <x-form-input
                            type="textarea"
                            id="message"
                            name="message"
                            rows="5"
                            placeholder="Expliquez la raison de votre retour (optionnel)"
                            class="w-full"
                        />
                        <p class="mt-2 text-sm text-gray-500">Ce message sera envoyé à notre service client pour traiter votre demande.</p>
                    </div>

                    <div class="flex items-center justify-between gap-4 rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                        <a
                            href="{{ route("dashboard.orders.show", $order) }}"
                            class="text-sm font-medium text-gray-600 hover:text-gray-900"
                        >
                            ← Retour à la commande
                        </a>

                        <x-button type="submit" icon="heroicon-o-paper-airplane" color="blue">Soumettre la demande de retour</x-button>
                    </div>
                </form>
            @else
                <div class="rounded-xl border border-gray-200 bg-white p-8 text-center shadow-sm">
                    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-gray-100">
                        <x-heroicon-o-clock class="size-8 text-gray-600" />
                    </div>
                    <h2 class="mb-2 text-xl font-semibold text-gray-900">Délai de rétractation expiré</h2>
                    <p class="mb-6 text-gray-600">
                        Le délai de 14 jours pour retourner vos articles est dépassé. Si vous avez un problème avec votre commande, notre
                        service client reste à votre disposition.
                    </p>
                    <div class="flex justify-center gap-3">
                        <a
                            href="{{ route("dashboard.orders.show", $order) }}"
                            class="rounded-lg border border-gray-300 bg-white px-6 py-3 text-sm font-semibold text-gray-700 transition-colors hover:bg-gray-50"
                        >
                            Retour à la commande
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
