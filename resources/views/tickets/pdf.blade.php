<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <style>
    /** Reset & Umum **/
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
      font-family: Arial, sans-serif;
      color: #333;
      padding: 20px;
      line-height: 1.4;
      background: #f5f5f5;
    }
    h1, h2 { color: #444; }
    p { margin: 4px 0; }

    /** Container utama tiket **/
    .ticket-container {
      border: 2px solid #8A2BE2;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      background: white;
      max-width: 800px;
      margin: 0 auto;
    }

    /** Header **/
    .ticket-header {
      background: linear-gradient(135deg, #8A2BE2, #6A1B9A);
      color: #ffffff;
      padding: 24px 16px;
      text-align: center;
      position: relative;
    }
    .ticket-header h1 {
      font-size: 28px;
      margin-bottom: 8px;
      text-transform: uppercase;
      letter-spacing: 1px;
    }
    .ticket-header .subtitle {
      font-size: 14px;
      opacity: 0.9;
    }

    /** Isi detail: dua kolom **/
    .ticket-body {
      display: flex;
      padding: 24px;
      gap: 24px;
    }
    .details {
      flex: 2;
    }
    .details h2 {
      font-size: 20px;
      margin-bottom: 16px;
      color: #8A2BE2;
      border-bottom: 2px solid #8A2BE2;
      padding-bottom: 8px;
    }
    .details table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 8px;
    }
    .details th, .details td {
      text-align: left;
      padding: 8px 4px;
      border-bottom: 1px solid #eee;
    }
    .details th {
      width: 35%;
      color: #666;
      font-weight: 600;
    }
    .details td {
      color: #222;
      font-weight: 500;
    }
    .details tr:last-child td {
      border-bottom: none;
    }

    /** QR Code Section **/
    .qr-section {
      flex: 1;
      text-align: center;
      border-left: 1px solid #eee;
      padding-left: 24px;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
    }
    .qr-section img {
      width: 160px;
      height: 160px;
      margin-bottom: 12px;
      border: 1px solid #eee;
      padding: 8px;
      border-radius: 8px;
    }
    .qr-section small {
      display: block;
      font-size: 12px;
      color: #666;
      margin-top: 8px;
    }
    .qr-section .status-badge {
      display: inline-block;
      padding: 4px 12px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 600;
      margin-top: 12px;
      text-transform: uppercase;
    }
    .status-badge.pending { background: #fff3cd; color: #856404; }
    .status-badge.paid { background: #d4edda; color: #155724; }
    .status-badge.cancelled { background: #f8d7da; color: #721c24; }

    /** Footer **/
    .ticket-footer {
      background: #f8f9fa;
      padding: 16px;
      text-align: center;
      font-size: 12px;
      color: #666;
      border-top: 1px solid #eee;
    }
    .ticket-footer .disclaimer {
      margin-top: 8px;
      font-size: 11px;
      color: #999;
    }

    /** Event Details Section **/
    .event-details {
      background: #f8f9fa;
      padding: 16px;
      margin-bottom: 24px;
      border-radius: 8px;
    }
    .event-details h3 {
      color: #8A2BE2;
      margin-bottom: 12px;
      font-size: 18px;
    }
    .event-details p {
      margin: 4px 0;
      color: #555;
    }
  </style>
</head>
<body>
  <div class="ticket-container">
    {{-- HEADER --}}
    <div class="ticket-header">
      <h1>E-Ticket #{{ $ticket->id }}</h1>
      <div class="subtitle">{{ $ticket->event->title }}</div>
    </div>

    {{-- BODY: Detail & QR --}}
    <div class="ticket-body">
      {{-- Kolom Detail --}}
      <div class="details">
        {{-- Event Details --}}
        <div class="event-details">
          <h3>Event Information</h3>
          <p><strong>Title:</strong> {{ $ticket->event->title }}</p>
          <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($ticket->event->start_date)->format('d M Y') }}</p>
          <p><strong>Time:</strong> {{ \Carbon\Carbon::parse($ticket->event->start_date)->format('H:i') }} - {{ \Carbon\Carbon::parse($ticket->event->end_date)->format('H:i') }} WIB</p>
          <p><strong>Location:</strong> {{ $ticket->event->location }}</p>
        </div>

        <h2>Participant Details</h2>
        <table>
          <tr>
            <th>Name</th>
            <td>{{ $ticket->participant_name }}</td>
          </tr>
          <tr>
            <th>Email</th>
            <td>{{ $ticket->participant_email }}</td>
          </tr>
          <tr>
            <th>Phone</th>
            <td>{{ $ticket->participant_phone }}</td>
          </tr>
          <tr>
            <th>Price Paid</th>
            <td>Rp {{ number_format($ticket->price_paid, 0, ',', '.') }}</td>
          </tr>
          <tr>
            <th>Issued At</th>
            <td>{{ $ticket->created_at->format('d M Y H:i') }}</td>
          </tr>
        </table>
      </div>

      {{-- Kolom QR Code --}}
      <div class="qr-section mt-10">
        <img
          src="data:image/png;base64,{{ $qrBase64 }}"
          alt="QR Code Ticket #{{ $ticket->id }}"
        />
        <small>Scan untuk verifikasi tiket</small>
        <div class="status-badge {{ $ticket->status }}">
          {{ ucfirst($ticket->status) }}
        </div>
      </div>
    </div>

    {{-- FOOTER --}}
    <div class="ticket-footer">
      <div>Generated on {{ now()->format('d M Y H:i') }}</div>
      <div class="disclaimer">
        This is an official e-ticket. Please keep it safe and present it at the event venue.
      </div>
    </div>
  </div>
</body>
</html>
