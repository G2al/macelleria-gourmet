<x-app-layout>
    @php
        // Prepara il payload prodotti per il dropdown (id, name, price_per_kg, photo URL assoluto se serve)
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

    <div class="mx-auto max-w-6xl px-4 py-10">
        <h1 class="mb-6 text-2xl font-bold">Nuova prenotazione</h1>

        @if (session('success'))
            <div class="mb-6 rounded-xl bg-emerald-50 p-3 text-emerald-700">{{ session('success') }}</div>
        @endif

        <div class="grid gap-6 md:grid-cols-3">
            <!-- Form -->
            <div class="md:col-span-2">
                <form method="POST" action="{{ route('orders.store') }}" class="space-y-6">
                    @csrf

                    <!-- prodotti -->
                    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                        <div class="mb-4 flex items-center justify-between">
                            <h3 class="font-semibold text-gray-900">Prodotti</h3>
                            <button type="button" id="add-product"
                                    class="inline-flex items-center rounded-xl bg-amber-500 px-3 py-2 text-white hover:bg-amber-600">
                                ➕ Aggiungi prodotto
                            </button>
                        </div>

                        <div id="products-container" class="space-y-4">
                            <!-- RIGA PRODOTTO (template iniziale) -->
                            <div class="product-item rounded-xl bg-gray-50 p-4" x-data="productRow()">
                                <div class="flex items-start gap-4">
                                    <div class="grid w-full grid-cols-1 gap-4 sm:grid-cols-2">
                                        <!-- Custom Select con immagine -->
                                        <div class="relative" x-data="productPicker()">
                                            <input type="hidden" name="products[0][id]" class="product-id">
                                            <label class="mb-1 block text-sm font-medium text-gray-700">Seleziona prodotto</label>

                                            <!-- Trigger -->
                                            <button type="button" @click="open = !open"
                                                    class="w-full rounded-xl border border-gray-300 bg-white p-2.5 text-left shadow-sm focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500">
                                                <div class="flex items-center gap-3">
                                                    <!-- IMMAGINE o ICONA di fallback -->
                                                    <div class="h-10 w-10 overflow-hidden rounded-lg bg-gray-100 grid place-items-center">
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

                                                    <div class="min-w-0">
                                                        <div class="truncate font-medium" x-text="selected ? selected.name : '— Seleziona —'"></div>
                                                        <div class="text-sm text-gray-500" x-show="selected" x-text="formatPrice(selected.price) + ' €/kg'"></div>
                                                    </div>

                                                    <svg class="ml-auto h-5 w-5 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.24 4.5a.75.75 0 01-1.08 0l-4.24-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/>
                                                    </svg>
                                                </div>
                                            </button>

                                            <!-- Dropdown -->
                                            <div x-show="open" x-transition @click.outside="open = false" @keydown.escape.window="open = false"
                                                 class="absolute z-20 mt-2 w-full rounded-xl border border-gray-200 bg-white shadow-lg">
                                                <div class="p-2">
                                                    <input x-model="query" type="text" placeholder="Cerca prodotto…"
                                                           class="w-full rounded-lg border border-gray-300 p-2 focus:border-amber-500 focus:ring-amber-500">
                                                </div>
                                                <ul class="max-h-64 divide-y divide-gray-100 overflow-auto">
                                                    <template x-for="p in filtered()" :key="p.id">
                                                        <li>
                                                            <button type="button" @click="select(p)"
                                                                    class="flex w-full items-center gap-3 p-2 hover:bg-amber-50">
                                                                <img :src="p.photo || placeholder" class="h-10 w-10 rounded-lg object-cover bg-gray-100" alt="">
                                                                <div class="min-w-0 text-left">
                                                                    <div class="truncate font-medium" x-text="p.name"></div>
                                                                    <div class="text-sm text-gray-500" x-text="formatPrice(p.price) + ' €/kg'"></div>
                                                                </div>
                                                            </button>
                                                        </li>
                                                    </template>
                                                    <li x-show="filtered().length === 0" class="p-3 text-sm text-gray-500">Nessun risultato</li>
                                                </ul>
                                            </div>
                                        </div>

                                        <!-- Peso + subtotale -->
                                        <div>
                                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                                Peso (Kg) <span class="text-xs text-gray-400">(usa . oppure ,)</span>
                                            </label>
                                            <input type="text" 
       name="products[0][weight]"
       class="weight-input w-full rounded-xl border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500"
       placeholder="es. 0,5"
       inputmode="decimal"
       @input="normalizeWeight($event); calc()"
       required>
                                            <div class="mt-2 flex flex-wrap items-center gap-4 text-sm text-gray-600">
                                                <span>Prezzo: <strong x-text="selected ? formatPrice(selected.price) : '—'"></strong> €/kg</span>
                                                <span>Subtotale: <strong x-text="formatPrice(subtotal)"></strong> €</span>
                                            </div>
                                        </div>
                                    </div>

                                    <button type="button" class="remove-product mt-7 hidden rounded-lg p-2 text-red-600 hover:bg-red-50"
                                            @click="$root.remove(); window.updateOrderTotal?.()">
                                        ✖
                                    </button>
                                </div>
                            </div>
                            <!-- /RIGA PRODOTTO -->
                        </div>

                        <!-- Totale ordine -->
                        <div class="mt-5 flex items-center justify-end rounded-xl bg-amber-50 px-4 py-3 text-amber-900">
                            <div class="text-sm">Totale provvisorio:</div>
                            <div class="ml-3 text-lg font-bold">€ <span id="order-total">0,00</span></div>
                        </div>
                    </div>

                    <!-- data & ora -->
                    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="pickup_date" class="mb-1 block text-sm font-medium text-gray-700">Data ritiro</label>
                                <input type="date" name="pickup_date" id="pickup_date"
                                       class="w-full rounded-xl border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500"
                                       min="{{ ($settings->min_days_advance ?? 1) ? now()->addDays($settings->min_days_advance ?? 1)->toDateString() : now()->toDateString() }}"
                                       value="{{ $defaultDate ?? now()->addDays($settings->min_days_advance ?? 1)->toDateString() }}"
                                       required>
                                @error('pickup_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="pickup_time" class="mb-1 block text-sm font-medium text-gray-700">Ora ritiro</label>
                                <select id="pickup_time" name="pickup_time"
                                        class="w-full rounded-xl border border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500" required>
                                    <option value="">— Seleziona un orario —</option>
                                    @foreach ($timeSlots as $slot)
                                        <option value="{{ $slot }}">{{ $slot }}</option>
                                    @endforeach
                                </select>
                                @error('pickup_time') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- note + submit -->
                    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                        <label for="notes" class="mb-1 block text-sm font-medium text-gray-700">Note (facoltative)</label>
                        <textarea id="notes" name="notes" rows="3"
                                  class="w-full rounded-xl border border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500"
                                  placeholder="Eventuali richieste o indicazioni...">{{ old('notes') }}</textarea>

                        <div class="mt-6 flex justify-end">
                            <button class="inline-flex items-center rounded-xl bg-amber-600 px-5 py-3 font-semibold text-white shadow hover:bg-amber-700">
                                Invia prenotazione
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Side info -->
            <aside class="space-y-6">
                <div class="rounded-2xl border border-amber-200 bg-amber-50 p-6">
                    <h3 class="mb-2 font-semibold text-amber-900">Come funziona</h3>
                    <ul class="list-disc space-y-1 pl-5 text-amber-900/80">
                        <li>Scegli uno o più prodotti e indica il peso (kg).</li>
                        <li>Seleziona data e orario di ritiro disponibili.</li>
                        <li>Riceverai conferma appena l’ordine viene preso in carico.</li>
                    </ul>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                    <h3 class="mb-2 font-semibold text-gray-900">Contatti rapidi</h3>
                    <p class="text-gray-600">Hai bisogno di aiuto?</p>
                    <div class="mt-3 flex gap-3">
                        <a href="tel:+393757372335" class="rounded-xl border border-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-50">Chiama</a>
                        <a href="https://wa.me/393757372335?text=Ciao%20Polleria%20Gourmet%2C%20vorrei%20info"
                           class="rounded-xl bg-emerald-500 px-4 py-2 text-white hover:bg-emerald-600">WhatsApp</a>
                    </div>
                </div>
            </aside>
        </div>
    </div>

    <!-- Floating WhatsApp -->
    <a href="https://wa.me/393757372335?text=Ciao%20Polleria%20Gourmet%2C%20vorrei%20prenotare"
       class="fixed bottom-6 right-6 z-50 inline-flex items-center rounded-full bg-emerald-500 px-5 py-3 text-white shadow-lg hover:bg-emerald-600">
        WhatsApp
    </a>

    <!-- JS: dataset + Alpine components + repeater + slot AJAX -->
    <script>
        // Dataset prodotti per il picker (iniettato da PHP)
        window.PRODUCTS = @json($productsPayload);
        // Placeholder per le immagini mancanti (solo nella lista)
        window.PRODUCT_PLACEHOLDER = "{{ asset('images/placeholder-product.png') }}";
    </script>

    {{-- <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script> --}}

    <script>
        // ---------- Componenti Alpine ----------
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
                    // scrive l'id nel relativo input hidden della riga
                    this.$root.querySelector('input.product-id').value = p.id;
                    // salva il prezzo nel row e ricalcola
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
                selected: null, // referenziata dal picker solo per i testi/valori
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

        // ---------- Repeater + Totale ----------
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

            // reset campi e nomi
            clone.querySelectorAll('input, select').forEach(el => {
                if (el.name?.includes('products')) {
                    el.name = el.name.replace(/\d+/, productIndex);
                }
                if (el.classList.contains('product-id')) el.value = '';
                if (el.classList.contains('weight-input')) el.value = '';
            });

            // reset stato e subtotal
            clone.removeAttribute('data-subtotal');

            // mostra pulsante rimozione
            const removeBtn = clone.querySelector('.remove-product');
            removeBtn.classList.remove('hidden');
            removeBtn.onclick = () => { clone.remove(); window.updateOrderTotal(); };

            // Reinizializza Alpine nel clone
            if (window.Alpine?.initTree) Alpine.initTree(clone);

            container.appendChild(clone);
            productIndex++;
        });

        // ---------- Slot orari AJAX ----------
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
                timeSelect.innerHTML = '<option value="">— Seleziona un orario —</option>';
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
