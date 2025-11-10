<x-app-layout>
    <div class="mx-auto max-w-6xl px-4 py-10">
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-bold">Le mie prenotazioni</h1>
            <a href="{{ route('orders.create') }}" class="rounded-xl bg-amber-500 px-4 py-2 text-white hover:bg-amber-600">
                + Nuova prenotazione
            </a>
        </div>

        @if (session('success'))
            <div class="mb-6 rounded-xl bg-emerald-50 p-3 text-emerald-700">{{ session('success') }}</div>
        @endif

        @if ($orders->isEmpty())
            <div class="rounded-2xl border border-dashed border-gray-300 p-12 text-center text-gray-500">
                Nessuna prenotazione. <a class="text-amber-600 underline" href="{{ route('orders.create') }}">Crea la prima</a>.
            </div>
        @else
            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-amber-50/50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Prodotti</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Totale</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Data</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Ora</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Stato</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Note</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($orders as $order)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-800">
                                    @foreach ($order->items as $item)
                                        <div class="flex items-center gap-2">
                                            <span class="font-medium">{{ $item->product->name ?? 'Prodotto eliminato' }}</span>
                                            <span class="text-gray-500">({{ number_format($item->weight, 3, ',', '.') }} kg)</span>
                                            <span class="text-gray-500">– € {{ number_format($item->total_price, 2, ',', '.') }}</span>
                                        </div>
                                    @endforeach
                                </td>
                                <td class="px-4 py-3 text-sm font-semibold text-gray-900">
                                    € {{ number_format($order->total_price, 2, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-800">
                                    {{ \Carbon\Carbon::parse($order->pickup_date)->format('d/m/Y') }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-800">
                                    {{ \Carbon\Carbon::parse($order->pickup_time)->format('H:i') }}
                                </td>
                                <td class="px-4 py-3">
                                    @php
                                        $map = [
                                            'pending'   => ['In attesa', 'bg-yellow-100 text-yellow-800'],
                                            'confirmed' => ['Confermato', 'bg-emerald-100 text-emerald-800'],
                                            'cancelled' => ['Annullato', 'bg-red-100 text-red-800'],
                                            'completed' => ['Completato', 'bg-gray-200 text-gray-800'],
                                        ];
                                        [$label, $cls] = $map[$order->status] ?? [$order->status, 'bg-gray-100 text-gray-800'];
                                    @endphp
                                    <span class="rounded-md px-2 py-1 text-xs font-semibold {{ $cls }}">{{ $label }}</span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    {{ $order->notes ?: '—' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-app-layout>
