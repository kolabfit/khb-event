@php $ticket = $custom_ticket_qr['ticket']; @endphp
<div style="display: flex; align-items: flex-start; gap: 32px;">
    <div style="flex: 1;">
        <div><b>Ticket ID:</b> {{ $ticket->id }}</div>
        <div><b>Event:</b> {{ $ticket->event->title }}</div>
        <div><b>Participant:</b> {{ $ticket->participant_name }}</div>
        <div><b>Email:</b> {{ $ticket->participant_email }}</div>
        <div><b>Phone:</b> {{ $ticket->participant_phone }}</div>
        <div><b>Price:</b> Rp {{ number_format($ticket->price_paid, 0, ',', '.') }}</div>
        <div><b>Status:</b> {{ ucfirst($ticket->status) }}</div>
        <div><b>Created At:</b> {{ \Carbon\Carbon::parse($ticket->created_at)->format('d M Y H:i') }}</div>
    </div>
    <div>
        <img src="{{ route('tickets.qr-code', ['ticket' => $ticket->id]) }}" alt="QR Code" width="200" height="200" style="border:1px solid #eee; border-radius:8px;">
        <div style="text-align:center; margin-top:8px;">QR Code</div>
    </div>
</div> 