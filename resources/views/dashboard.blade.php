<x-app-layout>
    @php
        $featuredProducts = ($featuredProducts ?? null)
            ?: \App\Models\Product::where('is_active', true)->latest()->take(8)->get();

        $categories = ($categories ?? null)
            ?: \App\Models\Category::with(['products' => function ($q) {
                $q->where('is_active', true)->orderBy('name');
            }])->orderBy('name')->get();

        $photoUrl = function ($path) {
            if (!$path) return null;
            if (filter_var($path, FILTER_VALIDATE_URL)) {
                return $path;
            }
            $relative = 'public/' . ltrim($path, '/');
            if (\Illuminate\Support\Facades\Storage::exists($relative)) {
                return \Illuminate\Support\Facades\Storage::url($relative);
            }
            if (\Illuminate\Support\Facades\Storage::exists($path)) {
                return \Illuminate\Support\Facades\Storage::url($path);
            }
            return null;
        };

        $placeholder = asset('images/placeholder-product.png');

        $getPurchaseLabel = function ($type) {
            return match($type) {
                'weight'   => 'Al peso',
                'unit'     => 'A pezzi',
                'package'  => 'A confezioni',
                default    => $type,
            };
        };
    @endphp

    <div class="min-h-screen bg-gradient-to-b from-amber-50 via-white to-gray-50">
        <!-- HERO SECTION -->
        <div class="relative overflow-hidden bg-gradient-to-r from-amber-600 via-amber-500 to-orange-500 px-4 py-12 sm:py-16">
            <div class="absolute inset-0 opacity-10">
                <svg class="h-full w-full" viewBox="0 0 100 100"><circle cx="20" cy="20" r="30" fill="currentColor"/><circle cx="80" cy="80" r="40" fill="currentColor"/></svg>
            </div>
            <div class="relative z-10 mx-auto max-w-6xl">
                <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                    <div class="text-white">
                        <h1 class="text-4xl font-black leading-tight">Ciao, {{ Auth::user()->name }}! 👋</h1>
                        <p class="mt-3 max-w-2xl text-lg text-amber-50">
                            Benvenuto nella <span class="font-bold">Polleria Gourmet</span>. Prenota i tuoi prodotti preferiti e ritirali quando vuoi!
                        </p>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row">
                        <a href="{{ route('orders.create') }}"
                           class="inline-flex items-center justify-center gap-2 rounded-2xl bg-white px-6 py-3.5 font-bold text-amber-600 shadow-xl hover:shadow-2xl hover:scale-105 transition-all whitespace-nowrap">
                            🛒 Nuova Prenotazione
                        </a>
                        <a href="{{ route('orders.index') }}"
                           class="inline-flex items-center justify-center gap-2 rounded-2xl border-2 border-white bg-amber-600/20 px-6 py-3.5 font-bold text-white shadow-lg hover:bg-amber-600/30 transition-all whitespace-nowrap">
                            📋 Le mie Prenotazioni
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="mx-auto max-w-6xl px-4 py-10 sm:py-14">

            <!-- SEZIONE IN EVIDENZA -->
            <section class="mb-14">
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-900">⭐ In evidenza</h2>
                        <p class="mt-1 text-gray-600">I nostri ultimi prodotti attivi</p>
                    </div>
                </div>

                @if ($featuredProducts->isEmpty())
                    <div class="rounded-3xl border-2 border-dashed border-gray-300 bg-white p-12 text-center">
                        <p class="text-lg text-gray-500">📭 Nessun prodotto disponibile al momento</p>
                    </div>
                @else
                    <link rel="stylesheet" href="https://unpkg.com/swiper@9/swiper-bundle.min.css" />
                    <div class="swiper featured-swiper">
                        <div class="swiper-wrapper">
                            @foreach ($featuredProducts as $p)
                                @php
                                    $url = $photoUrl($p->photo) ?: $placeholder;
                                    $desc = $p->description ? \Illuminate\Support\Str::limit($p->description, 100) : null;
                                @endphp
                                <div class="swiper-slide">
                                    <div class="group overflow-hidden rounded-3xl border-2 border-gray-200 bg-white shadow-lg hover:shadow-2xl transition-all h-full flex flex-col">
                                        <div class="aspect-video bg-gradient-to-br from-gray-100 to-gray-200 overflow-hidden relative">
                                            <img src="{{ $url }}" alt="{{ $p->name }}"
                                                 class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-300"
                                                 onerror="this.src='{{ $placeholder }}'; this.onerror=null;">
                                            <div class="absolute top-3 right-3 rounded-2xl bg-amber-500 px-4 py-2 font-bold text-white shadow-lg">
                                                {{ $getPurchaseLabel($p->purchase_type) }}
                                            </div>
                                        </div>
                                        <div class="flex flex-col flex-1 p-5">
                                            <h3 class="font-bold text-gray-900 text-lg line-clamp-2">{{ $p->name }}</h3>
                                            @if($desc)
                                                <p class="mt-2 text-sm text-gray-600 line-clamp-2">{{ $desc }}</p>
                                            @endif
                                            <div class="mt-auto pt-4">
                                                <a href="{{ route('orders.create') }}"
                                                   class="w-full rounded-2xl bg-gradient-to-r from-amber-500 to-amber-600 px-4 py-3 text-center font-bold text-white shadow-md hover:from-amber-600 hover:to-amber-700 transition-all">
                                                    Prenota ora
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="swiper-button-prev featured-swiper-prev"></div>
                        <div class="swiper-button-next featured-swiper-next"></div>
                        <div class="swiper-pagination featured-pagination"></div>
                    </div>

                    <script src="https://unpkg.com/swiper@9/swiper-bundle.min.js"></script>
                    <script>
                        document.addEventListener('DOMContentLoaded', () => {
                            new Swiper('.featured-swiper', {
                                slidesPerView: 1.1,
                                spaceBetween: 20,
                                loop: true,
                                pagination: { el: '.featured-pagination', clickable: true },
                                navigation: { nextEl: '.featured-swiper-next', prevEl: '.featured-swiper-prev' },
                                breakpoints: {
                                    640: { slidesPerView: 2 },
                                    1024: { slidesPerView: 3 },
                                },
                            });
                        });
                    </script>
                @endif
            </section>

            <!-- SEZIONE CATEGORIE -->
            <section>
                <div class="mb-6">
                    <h2 class="text-3xl font-bold text-gray-900">🏪 Tutti i prodotti</h2>
                    <p class="mt-1 text-gray-600">Sfoglia per categoria</p>
                </div>

                @if ($categories->isNotEmpty())
                    <!-- TAB BUTTONS -->
                    <div class="mb-8 overflow-x-auto pb-2">
                        <div class="flex gap-2 min-w-min sm:flex-wrap">
                            @foreach ($categories as $index => $cat)
                                @php $prods = $cat->products; @endphp
                                @if ($prods->isNotEmpty())
                                    <button 
                                        onclick="showCategory({{ $cat->id }}, event)"
                                        class="category-tab px-6 py-3 rounded-2xl font-bold transition-all whitespace-nowrap shadow-md
                                            @if($index === 0) 
                                                bg-gradient-to-r from-amber-500 to-amber-600 text-white 
                                            @else 
                                                bg-white text-gray-700 border-2 border-gray-200 hover:border-amber-400 
                                            @endif"
                                        data-category-id="{{ $cat->id }}">
                                        {{ $cat->name }}
                                    </button>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    <!-- CATEGORIA CONTENUTO -->
                    @foreach ($categories as $index => $cat)
                        @php $prods = $cat->products; @endphp
                        @if ($prods->isNotEmpty())
                            <div id="category-{{ $cat->id }}" class="category-content @if($index !== 0) hidden @endif">
                                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                                    @foreach ($prods as $p)
                                        @php
                                            $url = $photoUrl($p->photo) ?: $placeholder;
                                            $desc = $p->description ? \Illuminate\Support\Str::limit($p->description, 100) : null;
                                        @endphp
                                        <article class="group overflow-hidden rounded-2xl border-2 border-gray-200 bg-white shadow-md hover:shadow-xl transition-all flex flex-col h-full">
                                            <div class="aspect-square bg-gradient-to-br from-gray-100 to-gray-200 overflow-hidden relative">
                                                <img src="{{ $url }}" alt="{{ $p->name }}"
                                                     class="h-full w-full object-cover group-hover:scale-110 transition-transform duration-300"
                                                     loading="lazy"
                                                     onerror="this.src='{{ $placeholder }}'; this.onerror=null;">
                                                <div class="absolute top-3 right-3 rounded-xl bg-white px-3 py-1.5 text-sm font-bold text-amber-600 shadow-lg">
                                                    {{ $getPurchaseLabel($p->purchase_type) }}
                                                </div>
                                            </div>
                                            <div class="flex flex-col flex-1 p-4">
                                                <h3 class="font-bold text-gray-900 line-clamp-2">{{ $p->name }}</h3>
                                                @if($desc)
                                                    <p class="mt-2 text-xs text-gray-600 line-clamp-2">{{ $desc }}</p>
                                                @endif
                                                <div class="mt-auto pt-4">
                                                    <a href="{{ route('orders.create') }}"
                                                       class="block rounded-xl bg-amber-600 px-3 py-2.5 text-center text-sm font-bold text-white hover:bg-amber-700 transition-colors">
                                                        Prenota
                                                    </a>
                                                </div>
                                            </div>
                                        </article>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach

                    <script>
                        function showCategory(categoryId, event) {
                            document.querySelectorAll('.category-content').forEach(el => el.classList.add('hidden'));
                            document.querySelectorAll('.category-tab').forEach(btn => {
                                btn.classList.remove('bg-gradient-to-r', 'from-amber-500', 'to-amber-600', 'text-white');
                                btn.classList.add('bg-white', 'text-gray-700', 'border-2', 'border-gray-200');
                            });
                            document.getElementById('category-' + categoryId).classList.remove('hidden');
                            event.target.classList.remove('bg-white', 'text-gray-700', 'border-2', 'border-gray-200');
                            event.target.classList.add('bg-gradient-to-r', 'from-amber-500', 'to-amber-600', 'text-white');
                        }
                    </script>
                @else
                    <div class="rounded-3xl border-2 border-dashed border-gray-300 bg-white p-12 text-center">
                        <p class="text-lg text-gray-500">📭 Nessuna categoria disponibile</p>
                    </div>
                @endif
            </section>

            <!-- CONTATTI SECTION -->
            <section class="mt-16 mb-10">
                <h2 class="mb-6 text-3xl font-bold text-gray-900">📞 Contatti & Orari</h2>
                <div class="grid gap-6 md:grid-cols-3">
                    <!-- TELEFONO -->
                    <div class="rounded-3xl border-2 border-emerald-200 bg-gradient-to-br from-emerald-50 to-white p-7 shadow-lg hover:shadow-xl transition-shadow">
                        <div class="text-4xl mb-3">☎️</div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Telefono</h3>
                        <p class="text-gray-700 font-semibold mb-4">+39 375 737 2335</p>
                        <a href="https://wa.me/393757372335" class="inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-emerald-500 to-emerald-600 px-5 py-2.5 font-bold text-white hover:from-emerald-600 hover:to-emerald-700 transition-all">
                            💬 WhatsApp
                        </a>
                    </div>

                    <!-- INDIRIZZO -->
                    <div class="rounded-3xl border-2 border-blue-200 bg-gradient-to-br from-blue-50 to-white p-7 shadow-lg hover:shadow-xl transition-shadow">
                        <div class="text-4xl mb-3">📍</div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Indirizzo</h3>
                        <p class="text-gray-700 font-semibold mb-1">Via Esempio 123</p>
                        <p class="text-gray-600 text-sm">80100 Napoli</p>
                        <p class="mt-3 text-xs text-gray-500">Ritiro in negozio negli orari disponibili</p>
                    </div>

                    <!-- ORARI -->
                    <div class="rounded-3xl border-2 border-amber-200 bg-gradient-to-br from-amber-50 to-white p-7 shadow-lg hover:shadow-xl transition-shadow">
                        <div class="text-4xl mb-3">🕐</div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Orari di apertura</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="font-semibold text-gray-700">Lunedì-Giovedì:</span>
                                <span class="text-gray-900 font-bold">10:30-14:30 | 17:00-21:00</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-semibold text-gray-700">Ven-Dom:</span>
                                <span class="text-gray-900 font-bold">10:30-14:30 | 17:00-23:00</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <!-- FAB WhatsApp (mobile) -->
    <a href="https://wa.me/393757372335?text=Ciao%20Polleria%20Gourmet%2C%20vorrei%20prenotare"
       class="fixed bottom-6 right-6 z-40 inline-flex items-center gap-2 rounded-full bg-emerald-500 px-5 py-3 text-white font-bold shadow-2xl hover:bg-emerald-600 transition-all sm:hidden">
        💬 WhatsApp
    </a>

    <style>
        .swiper-button-next::after,
        .swiper-button-prev::after {
            font-size: 24px;
            font-weight: bold;
            color: #d97706;
        }
        .swiper-pagination-bullet-active {
            background-color: #d97706;
        }
        .swiper-pagination-bullet {
            background-color: #e5e7eb;
        }
    </style>
</x-app-layout>