@component('mail::message')
# 🧾 Conferma Prenotazione

Ciao **{{ $order->user->name }}**,  
la tua prenotazione è stata **confermata da Polleria Gourmet**! 🎉  

---

### 📦 Dettagli Ordine

@foreach ($order->items as $item)
**{{ $item->product->name }}**
@if ($item->quantity_type === 'weight')
{{ number_format($item->quantity, 3, ',', '.') }} kg
@elseif ($item->quantity_type === 'unit')
{{ (int)$item->quantity }} pezzi
@elseif ($item->quantity_type === 'package')
{{ (int)$item->quantity }} confezioni
@else
{{ $item->quantity }}
@endif

@endforeach

---

📅 **Data di Ritiro:** {{ \Carbon\Carbon::parse($order->pickup_date)->format('d/m/Y') }}  
🕒 **Orario:** {{ \Carbon\Carbon::parse($order->pickup_time)->format('H:i') }}

@if($order->notes)
📝 **Note cliente:**  
{{ $order->notes }}
@endif

Grazie per aver scelto **Polleria Gourmet** 🐔  
A presto!  
@endcomponent