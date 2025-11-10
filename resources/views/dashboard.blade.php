<x-app-layout>
    @php
        // Prodotti in evidenza = ultimi attivi
        $featuredProducts = ($featuredProducts ?? null)
            ?: \App\Models\Product::where('is_active', true)->latest()->take(8)->get();

        // Categorie con prodotti attivi
        $categories = ($categories ?? null)
            ?: \App\Models\Category::with(['products' => function ($q) {
                $q->where('is_active', true)->orderBy('name');
            }])->orderBy('name')->get();

        // Helper URL foto sicuro (accetta URL assoluti o path di Storage)
        $photoUrl = function ($path) {
            if (!$path) return null;
            if (filter_var($path, FILTER_VALIDATE_URL)) return $path;
            return \Illuminate\Support\Facades\Storage::exists($path)
                ? \Illuminate\Support\Facades\Storage::url($path)
                : null;
        };

        $placeholder = asset('images/placeholder-product.png');
    @endphp

    <div class="mx-auto max-w-6xl px-4 py-10">
        {{-- HERO --}}
        <div class="rounded-2xl relative overflow-hidden shadow-lg">
    <div class="absolute inset-0">
        <img src="{{ asset('storage/img/bg.jpg') }}" alt="Polleria Gourmet background"
             class="h-full w-full object-cover opacity-80">
        <div class="absolute inset-0 bg-black/40"></div>
    </div>

            <div class="relative z-10 p-8 text-white">
                <div class="flex flex-col items-start gap-6 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h1 class="text-2xl font-bold">Benvenuto nella tua area personale, {{ Auth::user()->name }} 👋</h1>
                        <p class="mt-2 max-w-2xl text-white/90">
                            Prenota tagli e preparazioni dalla <span class="font-semibold">Polleria Gourmet</span>.
                            Scegli prodotti, peso, data e ora di ritiro. Facile e veloce!
                        </p>
                    </div>
<div class="flex gap-4 flex-nowrap">
    <a href="{{ route('orders.create') }}"
       class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-amber-400 to-yellow-500 px-6 py-3 text-base font-semibold text-gray-900 shadow-lg shadow-amber-500/30 transition-all duration-200 hover:from-amber-500 hover:to-yellow-400 hover:scale-[1.03] whitespace-nowrap">
        🛒 <span>Nuova Prenotazione</span>
    </a>

    <a href="{{ route('orders.index') }}"
       class="inline-flex items-center gap-2 rounded-xl border border-amber-400/60 bg-black/40 px-6 py-3 text-base font-medium text-amber-300 shadow-md transition-all duration-200 hover:bg-amber-500/10 hover:border-amber-300 hover:text-amber-200 hover:scale-[1.03] whitespace-nowrap">
        📄 <span>Le mie prenotazioni</span>
    </a>
</div>

                </div>
            </div>
        </div>


        {{-- CAROSELLO: Prodotti in evidenza --}}
        <div class="mt-10">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-xl font-bold text-gray-900">In evidenza</h2>
                <div class="text-sm text-gray-500">Ultimi prodotti attivi</div>
            </div>

            @if ($featuredProducts->isEmpty())
                <div class="rounded-2xl border border-dashed border-gray-300 p-12 text-center text-gray-500">
                    Nessun prodotto disponibile al momento.
                </div>
            @else
                {{-- Swiper CSS --}}
                <link rel="stylesheet" href="https://unpkg.com/swiper@9/swiper-bundle.min.css" />
                <div class="swiper">
                    <div class="swiper-wrapper">
                        @foreach ($featuredProducts as $p)
                            @php
                                $url = $photoUrl($p->photo) ?: $placeholder;
                                $desc = $p->description ? \Illuminate\Support\Str::limit($p->description, 110) : null;
                            @endphp
                            <div class="swiper-slide">
                                <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                                    <div class="aspect-[16/9] bg-gray-100">
                                        <img src="{{ $url }}" alt="{{ $p->name }}"
                                             class="h-full w-full object-cover"
                                             onerror="this.src='{{ $placeholder }}'; this.onerror=null;">
                                    </div>
                                    <div class="p-4">
                                        <div class="flex items-start justify-between gap-4">
                                            <h3 class="font-semibold text-gray-900 truncate">{{ $p->name }}</h3>
                                            <span class="shrink-0 rounded-lg bg-amber-100 px-2 py-1 text-sm font-medium text-amber-800">
                                                € {{ number_format($p->price_per_kg, 2, ',', '.') }} /kg
                                            </span>
                                        </div>
                                        @if($desc)
                                            <p class="mt-1 text-sm text-gray-600">{{ $desc }}</p>
                                        @endif
                                        <div class="mt-3">
                                            <a href="{{ route('orders.create') }}"
                                               class="inline-flex items-center rounded-xl bg-amber-600 px-3 py-2 text-white hover:bg-amber-700">
                                                Prenota
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Controls --}}
                    <div class="swiper-button-prev"></div>
                    <div class="swiper-button-next"></div>
                    <div class="swiper-pagination"></div>
                </div>
                {{-- Swiper JS --}}
                <script src="https://unpkg.com/swiper@9/swiper-bundle.min.js"></script>
                <script>
                    document.addEventListener('DOMContentLoaded', () => {
                        new Swiper('.swiper', {
                            slidesPerView: 1.15,
                            spaceBetween: 16,
                            loop: true,
                            centeredSlides: false,
                            pagination: { el: '.swiper-pagination', clickable: true },
                            navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
                            breakpoints: {
                                640: { slidesPerView: 2.15 },
                                1024: { slidesPerView: 3.15 },
                            },
                        });
                    });
                </script>
            @endif
        </div>

        {{-- GRID per CATEGORIA --}}
        <div class="mt-10">
            <h2 class="mb-4 text-xl font-bold text-gray-900">Tutti i prodotti per categoria</h2>

            @forelse ($categories as $cat)
                @php $prods = $cat->products; @endphp
                @if ($prods->isNotEmpty())
                    <div class="mt-8">
                        <h3 class="mb-3 text-lg font-semibold text-gray-900">{{ $cat->name }}</h3>
                        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach ($prods as $p)
                                @php
                                    $url = $photoUrl($p->photo) ?: $placeholder;
                                    $desc = $p->description ? \Illuminate\Support\Str::limit($p->description, 120) : null;
                                @endphp
                                <article class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition">
                                    <div class="aspect-[4/3] bg-gray-100">
                                        <img src="{{ $url }}" alt="{{ $p->name }}"
                                             class="h-full w-full object-cover"
                                             loading="lazy"
                                             onerror="this.src='{{ $placeholder }}'; this.onerror=null;">
                                    </div>
                                    <div class="p-4">
                                        <div class="flex items-start justify-between gap-4">
                                            <h4 class="font-semibold text-gray-900 truncate">{{ $p->name }}</h4>
                                            <span class="shrink-0 rounded-lg bg-amber-100 px-2 py-1 text-sm font-medium text-amber-800">
                                                € {{ number_format($p->price_per_kg, 2, ',', '.') }} /kg
                                            </span>
                                        </div>
                                        @if($desc)
                                            <p class="mt-1 text-sm text-gray-600">{{ $desc }}</p>
                                        @endif
                                        <div class="mt-3">
                                            <a href="{{ route('orders.create') }}"
                                               class="inline-flex items-center rounded-xl bg-amber-600 px-3 py-2 text-white hover:bg-amber-700">
                                                Prenota
                                            </a>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </div>
                @endif
            @empty
                <div class="rounded-2xl border border-dashed border-gray-300 p-12 text-center text-gray-500">
                    Nessuna categoria disponibile.
                </div>
            @endforelse
        </div>

        {{-- CONTATTI --}}
        <div id="contatti" class="mt-12 grid gap-6 md:grid-cols-3">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <h3 class="mb-2 font-semibold text-gray-900">Telefono</h3>
                <p class="text-gray-600">+39 375 737 2335</p>
                <a href="https://wa.me/393757372335" class="mt-3 inline-flex items-center rounded-lg bg-emerald-500 px-3 py-2 text-white hover:bg-emerald-600">
                    WhatsApp
                </a>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <h3 class="mb-2 font-semibold text-gray-900">Indirizzo</h3>
                <p class="text-gray-600">Via Esempio 123, 80100 Napoli</p>
                <p class="text-sm text-gray-500">Ritiro in negozio negli orari disponibili.</p>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <h3 class="mb-2 font-semibold text-gray-900">Orari</h3>
                <p class="text-gray-600">Gio: 10:00–13:00</p>
                <p class="text-gray-600">Lun–Mer, Ven–Sab: 10:00–13:00, 16:30–19:30</p>
            </div>
        </div>
    </div>

    {{-- FAB WhatsApp --}}
    <a href="https://wa.me/393757372335?text=Ciao%20Polleria%20Gourmet%2C%20vorrei%20prenotare"
       class="fixed bottom-6 right-6 z-50 inline-flex items-center rounded-full bg-emerald-500 px-5 py-3 text-white shadow-lg hover:bg-emerald-600">
        WhatsApp
    </a>
</x-app-layout>
