<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <style>
      table { width:100%; border-collapse:collapse; }
      th, td { padding:4px; border:1px solid #ddd; text-align:left; }
    </style>
  </head>
  <body>
    <h2>Laporan Pembayaran</h2>
    <table>
      <thead>
        <tr>
          <th>Ticket ID</th>
          <th>Event</th>
          <th>EO</th>
          <th>Amount</th>
          <th>Method</th>
          <th>Status</th>
          <th>Tanggal</th>
        </tr>
      </thead>
      <tbody>
        @foreach($payments as $p)
          <tr>
            <td>{{ $p->ticket_id }}</td>
            <td>{{ $p->ticket->event->title }}</td>
            <td>{{ $p->ticket->user->name }}</td>
            <td>Rp {{ number_format($p->amount,0,',','.') }}</td>
            <td>{{ ucfirst($p->method) }}</td>
            <td>{{ ucfirst($p->status) }}</td>
            <td>{{ $p->created_at->format('d M Y H:i') }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </body>
</html>
