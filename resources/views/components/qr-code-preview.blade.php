@php
    $qrData = route('tickets.download', $ticket->id);
    $qr = new \Endroid\QrCode\QrCode($qrData);
    $writer = new \Endroid\QrCode\Writer\PngWriter();
    $qrResult = $writer->write($qr);
    $qrBase64 = base64_encode($qrResult->getString());
@endphp

<div class="p-4 text-center">
    <img 
        src="data:image/png;base64,{{ $qrBase64 }}" 
        alt="QR Code Ticket #{{ $ticket->id }}"
        class="mx-auto"
        style="width: 300px; height: 300px;"
    />
    <p class="mt-4 text-sm text-gray-500">
        Scan QR code untuk verifikasi tiket
    </p>
    <div class="mt-2">
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
            @if($ticket->status === 'paid') bg-green-100 text-green-800
            @elseif($ticket->status === 'pending') bg-yellow-100 text-yellow-800
            @elseif($ticket->status === 'cancelled') bg-red-100 text-red-800
            @else bg-gray-100 text-gray-800
            @endif">
            {{ ucfirst($ticket->status) }}
        </span>
    </div>
</div> 