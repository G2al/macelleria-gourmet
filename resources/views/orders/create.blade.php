<x-app-layout>
    @php
        $productsPayload = $products->map(function ($p) {
            $url = $p->photo ?? null;
            if ($url && !filter_var($url, FILTER_VALIDATE_URL)) {
                try { $url = \Illuminate\Support\Facades\Storage::url($url); } catch (\Throwable $e) { $url = null; }
            }
            return [
                'id'    => $p->id,
                'name'  => $p->name,
                'price' => (float) $p->price_per_kg,
                'photo' => $url,
            ];
        });
    @endphp

    <div class="min-h-screen bg-gradient-to-b from-amber-50 to-white">
        <div class="mx-auto max-w-5xl px-4 py-6 sm:py-10">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">📋 Nuova prenotazione</h1>
                <p class="mt-2 text-sm text-gray-600">Scegli i prodotti, indica il peso e seleziona l'orario</p>
            </div>

            @if (session('success'))
                <div class="mb-6 rounded-2xl bg-emerald-50 p-4 text-emerald-700 border border-emerald-200">
                    ✅ {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('orders.store') }}" class="space-y-6">
                @csrf

                <!-- SEZIONE PRODOTTI -->
                <div class="rounded-3xl border-2 border-amber-200 bg-white p-6 shadow-lg overflow-hidden">
                    <div class="mb-6 flex items-center justify-between gap-4">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">🍗 Prodotti</h3>
                            <p class="text-sm text-gray-500 mt-1">Aggiungi uno o più articoli</p>
                        </div>
                        <button type="button" id="add-product"
                                class="inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-amber-500 to-amber-600 px-4 py-3 text-white font-semibold hover:from-amber-600 hover:to-amber-700 transition-all shadow-md whitespace-nowrap">
                            ➕ Aggiungi
                        </button>
                    </div>

                    <div id="products-container" class="space-y-5">
                        <!-- RIGA PRODOTTO (template) -->
                        <div class="product-item rounded-2xl bg-gradient-to-br from-gray-50 to-gray-100 p-5 border border-gray-200 transition-all" x-data="productRow()">
                            <div class="space-y-4">
                                <!-- Custom Select con immagine -->
                                <div class="relative" x-data="productPicker()">
                                    <input type="hidden" name="products[0][id]" class="product-id">
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Seleziona prodotto</label>

                                    <!-- Trigger -->
                                    <button type="button" @click="open = !open"
                                            class="w-full rounded-2xl border-2 border-gray-300 bg-white p-3.5 text-left shadow-sm hover:border-amber-400 focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/50 transition-all">
                                        <div class="flex items-center gap-3">
                                            <div class="h-12 w-12 overflow-hidden rounded-xl bg-gray-100 grid place-items-center flex-shrink-0">
                                                <template x-if="selected && selected.photo">
                                                    <img :src="selected.photo" alt="" class="h-full w-full object-cover">
                                                </template>
                                                <template x-if="!selected || !selected.photo">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                         class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                              d="M2.25 21h19.5M4.5 17.25l4.5-6 3 4.5 3-3 4.5 4.5M9 8.25a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/>
                                                    </svg>
                                                </template>
                                            </div>

                                            <div class="min-w-0 flex-1">
                                                <div class="font-bold text-gray-900 truncate" x-text="selected ? selected.name : '— Seleziona —'"></div>
                                                <div class="text-sm text-amber-600 font-semibold" x-show="selected" x-text="formatPrice(selected.price) + ' €/kg'"></div>
                                            </div>

                                            <svg class="h-5 w-5 text-gray-400 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.24 4.5a.75.75 0 01-1.08 0l-4.24-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                    </button>

                                    <!-- Dropdown -->
                                    <div x-show="open" x-transition @click.outside="open = false" @keydown.escape.window="open = false"
                                         class="absolute z-20 mt-2 w-full rounded-2xl border-2 border-amber-200 bg-white shadow-2xl">
                                        <div class="border-b border-gray-100 p-3">
                                            <input x-model="query" type="text" placeholder="🔍 Cerca prodotto…"
                                                   class="w-full rounded-xl border border-gray-300 p-3 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/50 text-sm font-medium">
                                        </div>
                                        <ul class="max-h-80 divide-y divide-gray-100 overflow-auto">
                                            <template x-for="p in filtered()" :key="p.id">
                                                <li>
                                                    <button type="button" @click="select(p)"
                                                            class="flex w-full items-center gap-3 p-3.5 hover:bg-amber-50 transition-colors">
                                                        <img :src="p.photo || placeholder" class="h-12 w-12 rounded-xl object-cover bg-gray-100 flex-shrink-0" alt="">
                                                        <div class="min-w-0 text-left flex-1">
                                                            <div class="truncate font-bold text-gray-900" x-text="p.name"></div>
                                                            <div class="text-sm text-amber-600 font-semibold" x-text="formatPrice(p.price) + ' €/kg'"></div>
                                                        </div>
                                                    </button>
                                                </li>
                                            </template>
                                            <li x-show="filtered().length === 0" class="p-4 text-center text-sm text-gray-500">Nessun risultato</li>
                                        </ul>
                                    </div>
                                </div>

                                <!-- Peso -->
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">
                                        Peso (Kg)
                                        <span class="text-xs font-normal text-gray-500">(es. 0,5 o 0.5)</span>
                                    </label>
                                    <input type="text" 
                                        name="products[0][weight]"
                                        class="weight-input w-full rounded-2xl border-2 border-gray-300 bg-white p-3.5 shadow-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-500/50 font-semibold text-lg"
                                        placeholder="es. 0,5"
                                        inputmode="decimal"
                                        @input="normalizeWeight($event); calc()"
                                        required>
                                </div>

                                <!-- Riepilogo riga -->
                                <div class="rounded-xl bg-white/60 p-3.5 space-y-2 border border-amber-100">
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-600">Prezzo unitario:</span>
                                        <span class="font-bold text-amber-600" x-text="selected ? formatPrice(selected.price) + ' €/kg' : '—'"></span>
                                    </div>
                                    <div class="h-px bg-gradient-to-r from-amber-200 via-amber-100 to-transparent"></div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-semibold text-gray-700">Subtotale:</span>
                                        <span class="text-xl font-bold text-amber-600" x-text="formatPrice(subtotal) + ' €'"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Pulsante rimozione -->
                            <button type="button" class="remove-product hidden mt-4 w-full rounded-xl bg-red-50 px-4 py-2.5 text-red-600 font-semibold hover:bg-red-100 transition-colors border border-red-200"
                                    @click="$root.remove(); window.updateOrderTotal?.()">
                                ✖ Rimuovi questo prodotto
                            </button>
                        </div>
                    </div>

                    <!-- Totale ordine -->
                    <div class="mt-6 rounded-2xl bg-gradient-to-r from-amber-500 to-amber-600 px-5 py-4 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div class="text-sm font-semibold opacity-90">💰 Totale provvisorio:</div>
                            <div class="text-2xl font-black">€ <span id="order-total">0,00</span></div>
                        </div>
                    </div>
                </div>

                <!-- SEZIONE DATA & ORA -->
                <div class="rounded-3xl border-2 border-blue-200 bg-white p-6 shadow-lg">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">📅 Quando ritirare?</h3>
                    
                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label for="pickup_date" class="block text-sm font-bold text-gray-700 mb-2">Data ritiro</label>
                            <input type="date" name="pickup_date" id="pickup_date"
                                   class="w-full rounded-2xl border-2 border-gray-300 bg-white p-3.5 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/50 font-semibold"
                                   min="{{ ($settings->min_days_advance ?? 1) ? now()->addDays($settings->min_days_advance ?? 1)->toDateString() : now()->toDateString() }}"
                                   value="{{ $defaultDate ?? now()->addDays($settings->min_days_advance ?? 1)->toDateString() }}"
                                   required>
                            @error('pickup_date') <p class="mt-2 text-sm text-red-600 font-medium">❌ {{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="pickup_time" class="block text-sm font-bold text-gray-700 mb-2">Ora ritiro</label>
                            <select id="pickup_time" name="pickup_time"
                                    class="w-full rounded-2xl border-2 border-gray-300 bg-white p-3.5 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/50 font-semibold"
                                    required>
                                <option value="">— Seleziona orario —</option>
                                @foreach ($timeSlots as $slot)
                                    <option value="{{ $slot }}">{{ $slot }}</option>
                                @endforeach
                            </select>
                            @error('pickup_time') <p class="mt-2 text-sm text-red-600 font-medium">❌ {{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- SEZIONE NOTE -->
                <div class="rounded-3xl border-2 border-green-200 bg-white p-6 shadow-lg">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">📝 Note (facoltative)</h3>
                    <textarea id="notes" name="notes" rows="4"
                              class="w-full rounded-2xl border-2 border-gray-300 bg-white p-3.5 shadow-sm focus:border-green-500 focus:ring-2 focus:ring-green-500/50 font-medium resize-none"
                              placeholder="Es. senza aglio, ben fatto, ecc...">{{ old('notes') }}</textarea>

                    <button type="submit" class="mt-6 w-full rounded-2xl bg-gradient-to-r from-emerald-500 to-emerald-600 px-6 py-4 font-bold text-white shadow-lg hover:from-emerald-600 hover:to-emerald-700 transition-all text-lg">
                        ✅ Invia prenotazione
                    </button>
                </div>
            </form>

            <!-- INFO HELPER -->
            <div class="grid gap-6 mt-8 md:grid-cols-2">
                <div class="rounded-2xl border-2 border-blue-200 bg-blue-50 p-6">
                    <h3 class="mb-3 font-bold text-blue-900 text-lg">💡 Come calcolare il peso</h3>
                    <p class="text-sm text-blue-900/80 font-medium mb-3">Proprio come in macelleria! Se vuoi:</p>
                    <ul class="space-y-2 text-sm text-blue-900/80 font-medium">
                        <li class="flex gap-2"><span>🍔</span> 1 hamburger = 80g</li>
                        <li class="flex gap-2"><span>⚫</span> 1 polpetta = 50g</li>
                        <li class="flex gap-2"><span>🍗</span> 1 cotoletta = 150g</li>
                    </ul>
                    <p class="mt-4 text-sm text-blue-900/80 font-medium border-t border-blue-200 pt-3">
                        <strong>Es:</strong> 1 hamburger + 2 polpette = 80g + 100g = <strong>0,180kg</strong>
                    </p>
                </div>

                <div class="rounded-2xl border-2 border-amber-200 bg-amber-50 p-6">
                    <h3 class="mb-3 font-bold text-amber-900 text-lg">⚡ Come funziona</h3>
                    <ul class="space-y-3 text-sm text-amber-900/80 font-medium">
                        <li class="flex gap-2"><span>1️⃣</span> Scegli uno o più prodotti</li>
                        <li class="flex gap-2"><span>2️⃣</span> Indica il peso totale in kg</li>
                        <li class="flex gap-2"><span>3️⃣</span> Seleziona data e orario</li>
                        <li class="flex gap-2"><span>4️⃣</span> Riceverai conferma subito</li>
                    </ul>
                </div>
            </div>

            <!-- CONTATTI RAPIDI -->
            <div class="mt-8 rounded-2xl border-2 border-emerald-200 bg-emerald-50 p-6">
                <h3 class="mb-3 font-bold text-emerald-900 text-lg">📞 Contatti rapidi</h3>
                <p class="text-sm text-emerald-900/80 font-medium mb-4">Hai bisogno di aiuto o vuoi modificare?</p>
                <div class="flex flex-col gap-3 sm:flex-row">
                    <a href="tel:+08117672400" class="flex-1 rounded-xl border-2 border-emerald-300 bg-white px-4 py-3 text-center text-emerald-700 font-bold hover:bg-emerald-100 transition-colors">
                        ☎️ Chiama
                    </a>
                    <a href="https://wa.me/08117672400?text=Ciao%20Polleria%20Gourmet%2C%20vorrei%20info"
                       class="flex-1 rounded-xl bg-emerald-500 px-4 py-3 text-center text-white font-bold hover:bg-emerald-600 transition-colors">
                        💬 WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Floating WhatsApp -->
    <a href="https://wa.me/08117672400?text=Ciao%20Polleria%20Gourmet%2C%20vorrei%20prenotare"
       class="fixed bottom-6 right-6 z-40 inline-flex items-center gap-2 rounded-full bg-emerald-500 px-5 py-3 text-white font-bold shadow-2xl hover:bg-emerald-600 transition-all sm:hidden">
        💬 WhatsApp
    </a>

    <script>
        window.PRODUCTS = @json($productsPayload);
        window.PRODUCT_PLACEHOLDER = "{{ asset('images/placeholder-product.png') }}";
    </script>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('productPicker', () => ({
                open: false,
                query: '',
                selected: null,
                placeholder: window.PRODUCT_PLACEHOLDER,
                formatPrice(v) { return (v ?? 0).toLocaleString('it-IT', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); },
                filtered() {
                    const q = this.query.trim().toLowerCase();
                    return !q ? window.PRODUCTS : window.PRODUCTS.filter(p => p.name.toLowerCase().includes(q));
                },
                select(p) {
                    this.selected = p;
                    this.$root.querySelector('input.product-id').value = p.id;
                    const row = Alpine.$data(this.$root);
                    row.price = parseFloat(p.price) || 0;
                    row.calc();
                    this.open = false;
                },
            }));

            Alpine.data('productRow', () => ({
                price: 0,
                weight: 0,
                subtotal: 0,
                selected: null,
                placeholder: window.PRODUCT_PLACEHOLDER,
                formatPrice(v) { return (v ?? 0).toLocaleString('it-IT', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); },
                normalizeWeight(e) {
                    e.target.value = e.target.value.replace(',', '.');
                    this.weight = parseFloat(e.target.value) || 0;
                },
                calc() {
                    this.subtotal = (this.price || 0) * (this.weight || 0);
                    this.$root.dataset.subtotal = this.subtotal || 0;
                    window.updateOrderTotal?.();
                }
            }));
        });

        let productIndex = 1;
        const addBtn = document.getElementById('add-product');
        const container = document.getElementById('products-container');

        window.updateOrderTotal = function () {
            let sum = 0;
            container.querySelectorAll('.product-item').forEach(item => {
                sum += parseFloat(item.dataset.subtotal || 0);
            });
            document.getElementById('order-total').textContent =
                sum.toLocaleString('it-IT', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        };

        addBtn.addEventListener('click', () => {
            const clone = container.firstElementChild.cloneNode(true);

            clone.querySelectorAll('input, select').forEach(el => {
                if (el.name?.includes('products')) {
                    el.name = el.name.replace(/\d+/, productIndex);
                }
                if (el.classList.contains('product-id')) el.value = '';
                if (el.classList.contains('weight-input')) el.value = '';
            });

            clone.removeAttribute('data-subtotal');

            const removeBtn = clone.querySelector('.remove-product');
            removeBtn.classList.remove('hidden');
            removeBtn.onclick = () => { clone.remove(); window.updateOrderTotal(); };

            if (window.Alpine?.initTree) Alpine.initTree(clone);

            container.appendChild(clone);
            productIndex++;
        });

        const dateInput = document.getElementById('pickup_date');
        const timeSelect = document.getElementById('pickup_time');

        async function refreshSlots() {
            const date = dateInput.value;
            if (!date) return;
            timeSelect.innerHTML = '<option value="">Caricamento…</option>';

            try {
                const res = await fetch('{{ route('orders.slots') }}?date=' + encodeURIComponent(date), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();
                timeSelect.innerHTML = '<option value="">— Seleziona orario —</option>';
                (data.slots || []).forEach(s => {
                    const opt = document.createElement('option');
                    opt.value = s; opt.textContent = s;
                    timeSelect.appendChild(opt);
                });
            } catch (e) {
                timeSelect.innerHTML = '<option value="">Errore caricamento</option>';
            }
        }

        dateInput.addEventListener('change', refreshSlots);
        document.addEventListener('DOMContentLoaded', refreshSlots);
    </script>
</x-app-layout>