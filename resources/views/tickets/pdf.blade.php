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
    }
    h1, h2 { color: #444; }
    p { margin: 4px 0; }

    /** Container utama tiket **/
    .ticket-container {
      border: 2px solid #8A2BE2;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }

    /** Header **/
    .ticket-header {
      background: #8A2BE2;
      color: #ffffff;
      padding: 16px;
      text-align: center;
    }
    .ticket-header h1 {
      font-size: 24px;
    }

    /** Isi detail: dua kolom **/
    .ticket-body {
      display: flex;
      padding: 16px;
    }
    .details {
      flex: 2;
      padding-right: 16px;
    }
    .details h2 {
      font-size: 18px;
      margin-bottom: 8px;
      color: #8A2BE2;
    }
    .details table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 8px;
    }
    .details th, .details td {
      text-align: left;
      padding: 6px 4px;
    }
    .details th {
      width: 35%;
      color: #555;
    }
    .details td {
      color: #222;
    }

    /** QR Code Section **/
    .qr-section {
      flex: 1;
      text-align: center;
      border-left: 1px solid #eee;
      padding-left: 16px;
    }
    .qr-section img {
      width: 140px;
      height: 140px;
      margin-bottom: 8px;
    }
    .qr-section small {
      display: block;
      font-size: 10px;
      color: #666;
    }

    /** Footer **/
    .ticket-footer {
      background: #f7f7f7;
      padding: 10px 16px;
      text-align: right;
      font-size: 10px;
      color: #999;
    }
  </style>
</head>
<body>
  <div class="ticket-container">
    {{-- HEADER --}}
    <div class="ticket-header">
      <h1>E-Ticket #{{ $ticket->id }}</h1>
    </div>

    {{-- BODY: Detail & QR --}}
    <div class="ticket-body">
      {{-- Kolom Detail --}}
      <div class="details">
        <h2>Event Details</h2>
        <table>
          <tr>
            <th>Event</th>
            <td>{{ $ticket->event->title }}</td>
          </tr>
          <tr>
            <th>User</th>
            <td>{{ $ticket->user->name }}</td>
          </tr>
          <!-- <tr>
            <th>Ticket Type</th>
            <td>{{ $ticket->ticketType->name }}</td>
          </tr> -->
          <tr>
            <th>Price Paid</th>
            <td>Rp {{ number_format($ticket->price_paid, 0, ',', '.') }}</td>
          </tr>
          <tr>
            <th>Status</th>
            <td>{{ ucfirst($ticket->status) }}</td>
          </tr>
          <tr>
            <th>Issued At</th>
            <td>{{ $ticket->created_at->format('d M Y H:i') }}</td>
          </tr>
        </table>
      </div>

      {{-- Kolom QR Code --}}
      <div class="qr-section">
        <img
          src="{{ $qrUrl }}"
          alt="QR Code Ticket #{{ $ticket->id }}"
        />
        <small>Scan untuk verifikasi</small>
      </div>
    </div>

    {{-- FOOTER --}}
    <div class="ticket-footer">
      Generated on {{ now()->format('d M Y H:i') }}
    </div>
  </div>
</body>
</html>
