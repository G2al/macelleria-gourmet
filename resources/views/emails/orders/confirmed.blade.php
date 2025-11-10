@component('mail::message')
# 🧾 Conferma Prenotazione

Ciao **{{ $order->user->name }}**,  
la tua prenotazione è stata **confermata da Polleria Gourmet**! 🎉  

---

### 📦 Dettagli Ordine

@foreach ($order->items as $item)
- **Prodotto:** {{ $item->product->name }}
- **Peso:** {{ number_format($item->weight, 3, ',', '.') }} kg  
- **Prezzo unitario:** € {{ number_format($item->price_per_kg, 2) }}  
- **Totale:** € {{ number_format($item->total_price, 2) }}

---
@endforeach

💰 **Totale complessivo:** € {{ number_format($order->total_price, 2) }}  
📅 **Data di Ritiro:** {{ \Carbon\Carbon::parse($order->pickup_date)->format('d/m/Y') }}  
🕒 **Orario:** {{ \Carbon\Carbon::parse($order->pickup_time)->format('H:i') }}

@if($order->notes)
📝 **Note cliente:**  
{{ $order->notes }}
@endif

Grazie per aver scelto **Polleria Gourmet** 🐔  
A presto!  
@endcomponent
