<div class="grid grid-cols-2 gap-6">
    {{-- 📋 COLONNA SINISTRA --}}
    <div class="space-y-5">
        <x-filament::section>
            <x-slot name="heading">Dettagli ordine</x-slot>

            <div class="space-y-2 text-sm text-gray-200">
                <div class="flex justify-between">
                    <span class="text-gray-400">Cliente:</span>
                    <span class="font-medium">{{ $order->user->name }}</span>
                </div>

                <div class="flex justify-between">
                    <span class="text-gray-400">Data ritiro:</span>
                    <span class="font-medium">
                        {{ \Carbon\Carbon::parse($order->pickup_date)->format('d/m/Y') }}
                        alle {{ \Carbon\Carbon::parse($order->pickup_time)->format('H:i') }}
                    </span>
                </div>

                <div class="flex justify-between">
                    <span class="text-gray-400">Stato:</span>
                    <x-filament::badge :color="match($order->status) {
                        'pending' => 'warning',
                        'confirmed' => 'success',
                        'cancelled' => 'danger',
                        'completed' => 'gray',
                        default => 'gray'
                    }">
                        {{ ucfirst($order->status) }}
                    </x-filament::badge>
                </div>
            </div>
        </x-filament::section>

        @if ($order->notes)
            <x-filament::section>
                <x-slot name="heading">Note cliente</x-slot>
                <p class="text-gray-300 leading-relaxed">{{ $order->notes }}</p>
            </x-filament::section>
        @endif
    </div>

    {{-- 🧾 COLONNA DESTRA --}}
    <div>
        <x-filament::section>
            <x-slot name="heading">Prodotti ordinati</x-slot>

            <div class="divide-y divide-gray-800">
                @foreach ($order->items as $item)
                    <div class="flex justify-between py-3 items-start">
                        <div class="flex-1">
                            <p class="font-semibold text-gray-100">{{ $item->product->name }}</p>
                            <p class="text-gray-400 text-sm mt-1">
                                @if ($item->quantity_type === 'weight')
                                    {{ number_format($item->quantity, 3, ',', '.') }} kg
                                @elseif ($item->quantity_type === 'unit')
                                    {{ (int)$item->quantity }} pezzi
                                @elseif ($item->quantity_type === 'package')
                                    {{ (int)$item->quantity }} confezioni
                                @else
                                    {{ $item->quantity }}
                                @endif
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-filament::section>
    </div>
</div>