<x-app-layout>
    <div class="min-h-screen bg-gradient-to-b from-amber-50 to-white">
        <div class="mx-auto max-w-6xl px-4 py-6 sm:py-10">
            <!-- Header -->
            <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">📋 Le mie prenotazioni</h1>
                    <p class="mt-2 text-sm text-gray-600">Visualizza e gestisci tutti i tuoi ordini</p>
                </div>
                <a href="{{ route('orders.create') }}" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-amber-500 to-amber-600 px-6 py-3 font-bold text-white shadow-lg hover:from-amber-600 hover:to-amber-700 transition-all whitespace-nowrap">
                    ➕ Nuova prenotazione
                </a>
            </div>

            @if (session('success'))
                <div class="mb-6 rounded-2xl bg-emerald-50 p-4 text-emerald-700 border-l-4 border-emerald-500 font-medium">
                    ✅ {{ session('success') }}
                </div>
            @endif

            @if ($orders->isEmpty())
                <div class="rounded-3xl border-2 border-dashed border-gray-300 bg-white p-12 text-center">
                    <div class="mb-3 text-4xl">📭</div>
                    <p class="text-gray-600 font-medium mb-4">Nessuna prenotazione ancora</p>
                    <a href="{{ route('orders.create') }}" class="inline-block rounded-xl bg-amber-500 px-6 py-2.5 text-white font-semibold hover:bg-amber-600 transition-colors">
                        Crea la prima prenotazione
                    </a>
                </div>
            @else
                <!-- DESKTOP VIEW (tabella) -->
                <div class="hidden md:block overflow-hidden rounded-3xl border-2 border-gray-200 bg-white shadow-lg">
                    <table class="w-full divide-y divide-gray-200">
                        <thead class="bg-gradient-to-r from-amber-50 to-amber-100">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wide text-gray-700">Prodotti</th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wide text-gray-700">Totale</th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wide text-gray-700">Data</th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wide text-gray-700">Ora</th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wide text-gray-700">Stato</th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wide text-gray-700">Note</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($orders as $order)
                                <tr class="hover:bg-amber-50/50 transition-colors">
                                    <td class="px-6 py-4 text-sm">
                                        @foreach ($order->items as $item)
                                            <div class="flex gap-2 mb-2 last:mb-0">
                                                <span class="font-semibold text-gray-900">{{ $item->product->name ?? 'Prodotto eliminato' }}</span>
                                                <span class="text-gray-500">({{ number_format($item->weight, 3, ',', '.') }} kg)</span>
                                                <span class="text-amber-600 font-semibold">€ {{ number_format($item->total_price, 2, ',', '.') }}</span>
                                            </div>
                                        @endforeach
                                    </td>
                                    <td class="px-6 py-4 text-sm font-bold text-gray-900">
                                        € {{ number_format($order->total_price, 2, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        {{ \Carbon\Carbon::parse($order->pickup_date)->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        {{ \Carbon\Carbon::parse($order->pickup_time)->format('H:i') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $map = [
                                                'pending'   => ['⏳ In attesa', 'bg-yellow-100 text-yellow-800'],
                                                'confirmed' => ['✅ Confermato', 'bg-emerald-100 text-emerald-800'],
                                                'cancelled' => ['❌ Annullato', 'bg-red-100 text-red-800'],
                                                'completed' => ['🎉 Completato', 'bg-blue-100 text-blue-800'],
                                            ];
                                            [$label, $cls] = $map[$order->status] ?? [$order->status, 'bg-gray-100 text-gray-800'];
                                        @endphp
                                        <span class="rounded-lg px-3 py-1 text-xs font-bold {{ $cls }}">{{ $label }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate">
                                        {{ $order->notes ?: '—' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- MOBILE VIEW (card) -->
                <div class="md:hidden space-y-4">
                    @foreach ($orders as $order)
                        <div class="rounded-2xl border-2 border-gray-200 bg-white shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                            <!-- Header card -->
                            <div class="bg-gradient-to-r from-amber-500 to-amber-600 px-5 py-4 text-white">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-2xl font-black">€ {{ number_format($order->total_price, 2, ',', '.') }}</span>
                                    @php
                                        $map = [
                                            'pending'   => ['⏳ In attesa', 'bg-yellow-200 text-yellow-900'],
                                            'confirmed' => ['✅ Confermato', 'bg-emerald-200 text-emerald-900'],
                                            'cancelled' => ['❌ Annullato', 'bg-red-200 text-red-900'],
                                            'completed' => ['🎉 Completato', 'bg-blue-200 text-blue-900'],
                                        ];
                                        [$label, $cls] = $map[$order->status] ?? [$order->status, 'bg-gray-200 text-gray-900'];
                                    @endphp
                                    <span class="rounded-lg px-3 py-1 text-xs font-bold {{ $cls }}">{{ $label }}</span>
                                </div>
                                <div class="flex gap-4 text-sm font-semibold opacity-90">
                                    <span>📅 {{ \Carbon\Carbon::parse($order->pickup_date)->format('d/m/Y') }}</span>
                                    <span>🕐 {{ \Carbon\Carbon::parse($order->pickup_time)->format('H:i') }}</span>
                                </div>
                            </div>

                            <!-- Prodotti -->
                            <div class="px-5 py-4 border-b border-gray-100">
                                <h3 class="text-xs font-bold uppercase text-gray-600 mb-3">🍗 Prodotti</h3>
                                <div class="space-y-2.5">
                                    @foreach ($order->items as $item)
                                        <div class="rounded-lg bg-gray-50 p-3 border border-gray-100">
                                            <div class="font-semibold text-gray-900 text-sm mb-1">{{ $item->product->name ?? 'Prodotto eliminato' }}</div>
                                            <div class="flex items-center justify-between text-xs text-gray-600">
                                                <span>{{ number_format($item->weight, 3, ',', '.') }} kg</span>
                                                <span class="font-bold text-amber-600">€ {{ number_format($item->total_price, 2, ',', '.') }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Note -->
                            @if ($order->notes)
                                <div class="px-5 py-3 bg-blue-50 border-t border-blue-100">
                                    <p class="text-xs font-semibold text-blue-900 mb-1">📝 Note:</p>
                                    <p class="text-sm text-blue-900">{{ $order->notes }}</p>
                                </div>
                            @endif

                            <!-- Footer con CTA -->
<a href="https://wa.me/39817672400?text=Ciao%2C%20ho%20una%20domanda%20sulla%20mia%20prenotazione"
   class="w-full rounded-lg bg-emerald-500 px-4 py-2.5 text-center text-white text-sm font-bold hover:bg-emerald-600 transition-colors">
    Contattaci se hai domande
</a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>