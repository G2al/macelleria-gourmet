<x-app-layout>
    @php
        $productsPayload = $products->map(function ($p) {
            $url = $p->photo ?? null;
            if ($url && !filter_var($url, FILTER_VALIDATE_URL)) {
                try { $url = \Illuminate\Support\Facades\Storage::url($url); } catch (\Throwable $e) { $url = null; }
            }
            return [
                'id'            => $p->id,
                'name'          => $p->name,
                'photo'         => $url,
                'purchase_type' => $p->purchase_type,
            ];
        });
    @endphp

    <div class="min-h-screen bg-gradient-to-b from-amber-50 to-white">
        <div class="mx-auto max-w-5xl px-4 py-6 sm:py-10">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">📋 Nuova prenotazione</h1>
                <p class="mt-2 text-sm text-gray-600">Scegli i prodotti, indica la quantità e seleziona l'orario</p>
            </div>

            @if (session('success'))
                <div class="mb-6 rounded-2xl bg-emerald-50 p-4 text-emerald-700 border border-emerald-200">
                    ✅ {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('orders.store') }}" class="space-y-6">
                @csrf

                <!-- PRODOTTI -->
                <div class="rounded-3xl border-2 border-amber-200 bg-white p-6 shadow-lg overflow-visible">
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

                    <div id="products-container" class="space-y-5"></div>

                    <!-- TEMPLATE PULITO -->
                    <template id="product-row-template">
                        <div class="product-item rounded-2xl bg-gradient-to-br from-gray-50 to-gray-100 p-5 border border-gray-200 transition-all relative"
                             x-data="productRow()">
                            <div class="space-y-4">
                                <!-- Picker -->
                                <div class="relative" x-data="productPicker()">
                                    <!-- name indicizzato via dataset.index -->
                                    <input type="hidden" :name="'products[' + $root.dataset.index + '][id]'" class="product-id">
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Seleziona prodotto</label>

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
                                                <div class="text-sm text-amber-600 font-semibold" x-show="selected" x-text="selected ? getPurchaseLabel(selected.purchase_type) : ''"></div>
                                            </div>

                                            <svg class="h-5 w-5 text-gray-400 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.24 4.5a.75.75 0 01-1.08 0l-4.24-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                    </button>

                                    <!-- Dropdown -->
                                    <div x-show="open" x-transition @click.outside="open = false" @keydown.escape.window="open = false"
                                         class="absolute z-50 mt-2 w-full rounded-2xl border-2 border-amber-200 bg-white shadow-2xl">
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
                                                            <div class="text-sm text-amber-600 font-semibold" x-text="getPurchaseLabel(p.purchase_type)"></div>
                                                        </div>
                                                    </button>
                                                </li>
                                            </template>
                                            <li x-show="filtered().length === 0" class="p-4 text-center text-sm text-gray-500">Nessun risultato</li>
                                        </ul>
                                    </div>
                                </div>

                                <!-- QUANTITÀ: un solo input (x-if) -->
                                <template x-if="selected && selected.purchase_type === 'weight'">
                                    <div class="space-y-2">
                                        <label class="block text-sm font-bold text-gray-700">
                                            ⚖️ Peso (Kg)
                                            <span class="text-xs font-normal text-gray-500">(es. 0,5 o 0.5)</span>
                                        </label>
                                        <input type="text"
                                               :name="'products[' + $root.dataset.index + '][quantity]'"
                                               class="quantity-input w-full rounded-2xl border-2 border-gray-300 bg-white p-3.5 shadow-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-500/50 font-semibold text-lg"
                                               placeholder="es. 0,5"
                                               inputmode="decimal"
                                               required
                                               @input="normalizeQuantity($event)">
                                    </div>
                                </template>

                                <template x-if="selected && selected.purchase_type === 'unit'">
                                    <div class="space-y-2">
                                        <label class="block text-sm font-bold text-gray-700">Quantità (pezzi)</label>
                                        <div class="flex items-center gap-3">
                                            <button type="button" @click="decrementQuantity()" class="rounded-lg bg-red-100 p-2 text-red-600 hover:bg-red-200 transition-colors">
                                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"/></svg>
                                            </button>
                                            <input type="text"
                                                   :name="'products[' + $root.dataset.index + '][quantity]'"
                                                   class="quantity-input flex-1 rounded-lg border-2 border-gray-300 bg-white p-2.5 text-center font-bold text-lg"
                                                   value="1"
                                                   inputmode="numeric"
                                                   required
                                                   @input="normalizeQuantity($event)">
                                            <button type="button" @click="incrementQuantity()" class="rounded-lg bg-emerald-100 p-2 text-emerald-600 hover:bg-emerald-200 transition-colors">
                                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"/></svg>
                                            </button>
                                        </div>
                                    </div>
                                </template>

                                <template x-if="selected && selected.purchase_type === 'package'">
                                    <div class="space-y-2">
                                        <label class="block text-sm font-bold text-gray-700">📦 Quantità (confezioni)</label>
                                        <div class="flex items-center gap-3">
                                            <button type="button" @click="decrementQuantity()" class="rounded-lg bg-red-100 p-2 text-red-600 hover:bg-red-200 transition-colors">
                                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"/></svg>
                                            </button>
                                            <input type="text"
                                                   :name="'products[' + $root.dataset.index + '][quantity]'"
                                                   class="quantity-input flex-1 rounded-lg border-2 border-gray-300 bg-white p-2.5 text-center font-bold text-lg"
                                                   value="1"
                                                   inputmode="numeric"
                                                   required
                                                   @input="normalizeQuantity($event)">
                                            <button type="button" @click="incrementQuantity()" class="rounded-lg bg-emerald-100 p-2 text-emerald-600 hover:bg-emerald-200 transition-colors">
                                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"/></svg>
                                            </button>
                                        </div>
                                    </div>
                                </template>

                                <!-- Info -->
                                <template x-if="selected">
                                    <div class="rounded-xl bg-white/60 p-3.5 border border-amber-100">
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm font-semibold text-gray-700">Prodotto:</span>
                                            <span class="font-bold text-amber-600" x-text="selected.name"></span>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <!-- Rimuovi -->
                            <button type="button"
                                class="remove-product absolute -top-3 -right-3 hidden rounded-full bg-red-500 hover:bg-red-600 text-white p-2 shadow-lg hover:shadow-xl transition-all hover:scale-110"
                                title="Rimuovi prodotto">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                        </div>
                    </template>
                </div>

                <!-- DATA & ORA -->
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

                <!-- NOTE -->
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

            <!-- Info & contatti (come prima) -->
            <div class="grid gap-6 mt-8 md:grid-cols-2">
                <div class="rounded-2xl border-2 border-blue-200 bg-blue-50 p-6">
                    <h3 class="mb-3 font-bold text-blue-900 text-lg">💡 Tipi di quantità</h3>
                    <ul class="space-y-2 text-sm text-blue-900/80 font-medium">
                        <li class="flex gap-2"><span></span> <strong>Peso:</strong> Indica i kg (es. 0,5)</li>
                        <li class="flex gap-2"><span></span> <strong>Pezzi:</strong> Numero di articoli (1, 2, 3...)</li>
                        <li class="flex gap-2"><span></span> <strong>Confezioni:</strong> Numero di confezioni</li>
                    </ul>
                </div>

                <div class="rounded-2xl border-2 border-amber-200 bg-amber-50 p-6">
                    <h3 class="mb-3 font-bold text-amber-900 text-lg">⚡ Come funziona</h3>
                    <ul class="space-y-3 text-sm text-amber-900/80 font-medium">
                        <li class="flex gap-2"><span>1️⃣</span> Scegli uno o più prodotti</li>
                        <li class="flex gap-2"><span>2️⃣</span> Indica la quantità (peso, pezzi, confezioni)</li>
                        <li class="flex gap-2"><span>3️⃣</span> Seleziona data e orario</li>
                        <li class="flex gap-2"><span>4️⃣</span> Riceverai conferma subito</li>
                    </ul>
                </div>
            </div>

            <div class="mt-8 rounded-2xl border-2 border-emerald-200 bg-emerald-50 p-6">
                <h3 class="mb-3 font-bold text-emerald-900 text-lg">📞 Contatti rapidi</h3>
                <p class="text-sm text-emerald-900/80 font-medium mb-4">Hai bisogno di aiuto o vuoi modificare?</p>
                <div class="flex flex-col gap-3 sm:flex-row">
                    <a href="tel:+08117672400" class="flex-1 rounded-xl border-2 border-emerald-300 bg-white px-4 py-3 text-center text-emerald-700 font-bold hover:bg-emerald-100 transition-colors">☎️ Chiama</a>
                    <a href="https://wa.me/08117672400?text=Ciao%20Polleria%20Gourmet%2C%20vorrei%20info"
                       class="flex-1 rounded-xl bg-emerald-500 px-4 py-3 text-center text-white font-bold hover:bg-emerald-600 transition-colors">💬 WhatsApp</a>
                </div>
            </div>
        </div>
    </div>

    <a href="https://wa.me/08117672400?text=Ciao%20Polleria%20Gourmet%2C%20vorrei%20prenotare"
       class="fixed bottom-6 right-6 z-40 inline-flex items-center gap-2 rounded-full bg-emerald-500 px-5 py-3 text-white font-bold shadow-2xl hover:bg-emerald-600 transition-all sm:hidden">
        💬 WhatsApp
    </a>

<script>
    window.PRODUCTS = @json($productsPayload);
    window.PRODUCT_PLACEHOLDER = "{{ asset('images/placeholder-product.png') }}";
    function getPurchaseLabel(type) {
        const labels = { weight:' Al peso (kg)', unit:' A pezzi', package:' A confezioni' };
        return labels[type] || type;
    }
</script>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('productPicker', () => ({
        open:false, query:'', selected:null, placeholder: window.PRODUCT_PLACEHOLDER,
        filtered() {
            const q = this.query.trim().toLowerCase();
            return !q ? window.PRODUCTS : window.PRODUCTS.filter(p => p.name.toLowerCase().includes(q));
        },
        select(p) {
            this.selected = p;
            this.$root.querySelector('input.product-id').value = p.id;

            const rowEl = this.$root.closest('.product-item');
            const row = Alpine.$data(rowEl);
            row.selected = p;

            this.$nextTick(() => row.resetQuantity()); // l'input è creato da x-if

            this.open = false;
        },
    }));

    Alpine.data('productRow', () => ({
        selected:null,
        getQuantityInput() {
            return this.$root.querySelector('.quantity-input'); // con x-if ce n'è solo uno
        },
        resetQuantity() {
            const input = this.getQuantityInput();
            if (!input) return;
            input.value = (this.selected?.purchase_type === 'weight') ? '' : '1';
        },
        normalizeQuantity(e) {
            if (this.selected?.purchase_type === 'weight') e.target.value = e.target.value.replace(',', '.');
            else e.target.value = e.target.value.replace(/[^0-9]/g, '');
        },
        incrementQuantity() {
            const i = this.getQuantityInput(); if (!i) return;
            const n = parseInt(i.value) || 1; i.value = n + 1;
        },
        decrementQuantity() {
            const i = this.getQuantityInput(); if (!i) return;
            const n = parseInt(i.value) || 1; if (n > 1) i.value = n - 1;
        },
    }));
});

// gestione righe
let productIndex = 0;
const container = document.getElementById('products-container');
const tmpl = document.getElementById('product-row-template');
const addBtn = document.getElementById('add-product');

function setIndexNames(row, idx){
    row.dataset.index = String(idx);
    const idInput = row.querySelector('input.product-id');
    if (idInput) idInput.setAttribute('name', `products[${idx}][id]`);
    // se è già stato selezionato un prodotto e l'input quantità esiste, aggiorna il name
    row.querySelectorAll('.quantity-input').forEach(el => {
        el.setAttribute('name', `products[${idx}][quantity]`);
    });
}

function reindexRows(){
    const rows = [...container.querySelectorAll('.product-item')];
    rows.forEach((row, i) => setIndexNames(row, i));
}

function wireRemove(row){
    const btn = row.querySelector('.remove-product');
    btn.onclick = () => { row.remove(); reindexRows(); updateRemoveButtons(); };
}

function addRow(){
    const clone = tmpl.content.firstElementChild.cloneNode(true);
    if (window.Alpine?.initTree) Alpine.initTree(clone);
    setIndexNames(clone, productIndex);
    wireRemove(clone);
    container.appendChild(clone);
    productIndex++;
    updateRemoveButtons();
}

function updateRemoveButtons(){
    const items = container.querySelectorAll('.product-item');
    items.forEach(item => {
        const btn = item.querySelector('.remove-product');
        if (items.length > 1) btn.classList.remove('hidden'); else btn.classList.add('hidden');
    });
}

addBtn.addEventListener('click', addRow);
document.addEventListener('DOMContentLoaded', () => { addRow(); updateRemoveButtons(); });

// SLOTS orari
const dateInput  = document.getElementById('pickup_date');
const timeSelect = document.getElementById('pickup_time');

async function refreshSlots() {
    const date = dateInput?.value; if (!date) return;
    timeSelect.innerHTML = '<option value="">Caricamento…</option>';
    try {
        const res  = await fetch('{{ route('orders.slots') }}?date=' + encodeURIComponent(date), {
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
dateInput?.addEventListener('change', refreshSlots);
document.addEventListener('DOMContentLoaded', refreshSlots);
</script>
</x-app-layout>
