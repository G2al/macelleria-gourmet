<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\OpeningHour;
use App\Models\BookingSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\TelegramService;
use Carbon\Carbon;

class OrderController extends Controller
{
    /**
     * Capienza massima per slot orario (per giorno).
     */
    private const SLOT_CAPACITY = 3;

    /**
     * Mostra tutti gli ordini del cliente loggato.
     */
    public function index()
    {
        $orders = Order::with('items.product')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('orders.index', compact('orders'));
    }

    /**
     * Mostra il form per creare una nuova prenotazione.
     */
    public function create()
    {
        $products    = Product::where('is_active', true)->get();
        $settings    = BookingSetting::first();
        $defaultDate = Carbon::now()->addDays($settings->min_days_advance ?? 1);

        // Genera gli slot disponibili per la data di default (min prenotabile)
        $timeSlots = $this->generateSlotsForDate($defaultDate);

        return view('orders.create', [
            'products'    => $products,
            'settings'    => $settings,
            'timeSlots'   => $timeSlots,
            'defaultDate' => $defaultDate->toDateString(),
        ]);
    }

    /**
     * Salva un nuovo ordine multiprodotto nel database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'products'            => ['required', 'array', 'min:1'],
            'products.*.id'       => ['required', 'exists:products,id'],
            'products.*.weight'   => ['required', 'string'], // accettiamo "0,5" o "0.5"
            'pickup_date'         => ['required', 'date'],
            'pickup_time'         => ['required', 'string'],
        ]);

        $date = Carbon::parse($request->pickup_date)->toDateString();
        $time = $request->pickup_time;

        // 1) Validazione server: l'orario scelto deve essere uno slot ancora disponibile per quella data
        $availableSlots = $this->generateSlotsForDate(Carbon::parse($date));
        if (!in_array($time, $availableSlots, true)) {
            return back()
                ->withErrors(['pickup_time' => 'Lo slot selezionato non è più disponibile. Scegli un altro orario.'])
                ->withInput();
        }

        // 2) (Extra robustezza) ricontrollo capacità attuale prima di creare l'ordine
        $currentCount = Order::where('pickup_date', $date)
            ->where('pickup_time', $time)
            ->where('status', '!=', 'cancelled')
            ->count();

        if ($currentCount >= self::SLOT_CAPACITY) {
            return back()
                ->withErrors(['pickup_time' => 'Lo slot selezionato ha raggiunto la capienza massima.'])
                ->withInput();
        }

        // 3) Crea ordine
        $order = Order::create([
            'user_id'     => Auth::id(),
            'pickup_date' => $date,
            'pickup_time' => $time,
            'status'      => 'pending',
            'notes'       => $request->notes,
            'total_price' => 0,
        ]);

        $total = 0;

        // 4) Righe ordine
        foreach ($request->products as $productData) {
            // Normalizza peso "0,5" -> 0.5
            $weight  = (float) str_replace(',', '.', $productData['weight']);
            $product = Product::findOrFail($productData['id']);
            $itemTotal = $product->price_per_kg * $weight;

            OrderItem::create([
                'order_id'      => $order->id,
                'product_id'    => $product->id,
                'weight'        => $weight,
                'price_per_kg'  => $product->price_per_kg,
                'total_price'   => $itemTotal,
            ]);

            $total += $itemTotal;
        }

        $order->update(['total_price' => $total]);

        // 5) Telegram admin
        $msg = "🧾 *Nuova Prenotazione Ricevuta!*\n\n"
            . "👤 *Cliente:* " . Auth::user()->name . " " . Auth::user()->surname . "\n"
            . "📦 *Prodotti:*\n";

        foreach ($order->items as $item) {
            $msg .= "• " . $item->product->name . " (" . number_format($item->weight, 3) . "kg) - €" . number_format($item->total_price, 2) . "\n";
        }

        $msg .= "\n💰 *Totale:* €" . number_format($total, 2)
            . "\n📅 *Ritiro:* " . $order->pickup_date . " alle " . $order->pickup_time
            . "\n\n🕒 Stato: *In attesa di conferma*"
            . "\n\n🏪 [Apri pannello admin](http://127.0.0.1:8000/admin)";

        app(TelegramService::class)->sendMessage($msg);

        return redirect()
            ->route('orders.index')
            ->with('success', 'Prenotazione inviata con successo!');
    }

    /**
     * Endpoint AJAX: ritorna gli slot disponibili per una data (YYYY-MM-DD).
     */
    public function slots(Request $request)
    {
        $request->validate(['date' => ['required', 'date']]);

        $date  = Carbon::parse($request->get('date'));
        $slots = $this->generateSlotsForDate($date);

        return response()->json(['slots' => $slots]);
    }

    /**
     * Genera gli slot (ogni 30 minuti) per una data, filtrando quelli pieni.
     * - Usa opening_hours per le fasce (mattina/pomeriggio)
     * - Esclude slot che hanno già raggiunto SLOT_CAPACITY (conteggia tutti tranne 'cancelled')
     * - Inclusivo sull'orario di chiusura (es. 13:00 e 19:30 possono comparire)
     */
    private function generateSlotsForDate(Carbon $date): array
    {
        // 0=Lun … 6=Dom (il DB usa questo mapping)
        $dow = $date->dayOfWeekIso - 1;

        // fasce orarie attive per il giorno
        $ranges = OpeningHour::where('is_active', true)
            ->where('day_of_week', $dow)
            ->orderBy('opening_time')
            ->get();

        // conteggi per la data (tutti gli status tranne 'cancelled')
        $countsByTime = Order::select('pickup_time', DB::raw('COUNT(*) as c'))
            ->where('pickup_date', $date->toDateString())
            ->where('status', '!=', 'cancelled')
            ->groupBy('pickup_time')
            ->pluck('c', 'pickup_time'); // es. ['11:00:00' => 2, '12:30:00' => 3]

        $slots = collect();

        foreach ($ranges as $r) {
            $start = Carbon::createFromTimeString($r->opening_time);
            $end   = Carbon::createFromTimeString($r->closing_time);

            // Inclusivo sull'end
            while ($start <= $end) {
                $timeHhmm = $start->format('H:i');
                $timeDb   = $start->format('H:i:s'); // i record in DB sono 'H:i:s'

                $used = (int) ($countsByTime[$timeDb] ?? 0);

                if ($used < self::SLOT_CAPACITY) {
                    $slots->push($timeHhmm);
                }

                $start->addMinutes(30);
            }
        }

        return $slots->unique()->sort()->values()->all();
    }
}
