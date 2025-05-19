<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Detail Pembayaran #{{ $payment->id }}</title>
    <style>
        @page {
            margin: 2cm;
            size: A4 portrait;
        }
        
        /** Reset & Umum **/
        * { 
            margin: 0; 
            padding: 0; 
            box-sizing: border-box; 
        }
        
        body {
            font-family: Arial, sans-serif;
            color: #333;
            line-height: 1.4;
            background: #f5f5f5;
        }

        /** Container utama **/
        .container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        /** Header **/
        .header {
            background: linear-gradient(135deg, #8A2BE2, #6A1B9A);
            color: #ffffff;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
            margin-bottom: 20px;
        }
        
        .header h1 {
            font-size: 24px;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .header p {
            font-size: 14px;
            opacity: 0.9;
        }

        /** Content **/
        .content {
            padding: 0 20px 20px 20px;
        }

        /** Payment Info Section **/
        .payment-info {
            background: #f8f9fa;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            page-break-inside: avoid;
        }

        /** Section Headers **/
        .section-title {
            font-size: 18px;
            color: #8A2BE2;
            border-bottom: 2px solid #8A2BE2;
            padding-bottom: 8px;
            margin-bottom: 15px;
            page-break-after: avoid;
        }

        /** Grid Layout **/
        .info-grid {
            display: flex;
            gap: 20px;
            margin-bottom: 15px;
        }
        
        .info-column {
            flex: 1;
        }

        /** Table Styles **/
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        th, td {
            text-align: left;
            padding: 8px;
            border-bottom: 1px solid #eee;
        }
        
        th {
            width: 35%;
            color: #666;
            font-weight: 600;
        }
        
        td {
            color: #333;
        }

        /** Status Styles **/
        .status {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-paid { background: #d4edda; color: #155724; }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-used { background: #e2e8f0; color: #475569; }
        .status-failed { background: #f8d7da; color: #721c24; }

        /** Price Display **/
        .price {
            font-size: 18px;
            font-weight: 700;
            color: #8A2BE2;
        }

        /** Ticket Section **/
        .ticket-header-group {
            page-break-before: always;
            margin-top: 30px;
        }

        .ticket-list {
            margin-top: 20px;
        }

        .ticket {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            margin-bottom: 20px;
            page-break-inside: avoid;
        }

        .ticket:last-child {
            margin-bottom: 0;
        }

        .ticket-header {
            background: linear-gradient(135deg, #8A2BE2, #6A1B9A);
            color: white;
            padding: 15px;
            border-radius: 8px 8px 0 0;
        }

        .ticket-id {
            font-size: 14px;
            opacity: 0.9;
            margin-bottom: 4px;
        }

        .event-title {
            font-size: 18px;
            font-weight: 700;
        }

        .ticket-content {
            padding: 15px;
        }

        /** Event Details **/
        .event-details {
            background: #f8f9fa;
            padding: 15px;
            margin: 15px 0;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }

        .event-details h3 {
            color: #8A2BE2;
            margin-bottom: 10px;
            font-size: 16px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 5px;
        }

        /** QR Code Section **/
        .qr-container {
            text-align: center;
            margin-top: 15px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }

        .qr-code {
            background: white;
            padding: 10px;
            display: inline-block;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
        }

        .qr-code img {
            width: 150px;
            height: 150px;
            display: block;
        }

        .qr-label {
            margin-top: 8px;
            font-size: 12px;
            color: #666;
        }

        /** Footer **/
        .ticket-footer {
            background: #f8f9fa;
            padding: 15px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #e2e8f0;
            border-radius: 0 0 8px 8px;
            margin-top: 20px;
        }

        .ticket-footer .disclaimer {
            margin-top: 8px;
            font-size: 11px;
            color: #999;
        }

        /** Page Break Controls **/
        .page-break-after {
            page-break-after: always;
            break-after: always;
        }

        .no-break {
            page-break-inside: avoid;
            break-inside: avoid;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Detail Pembayaran</h1>
            <p>Transaction ID: {{ $payment->transaction_id }}</p>
        </div>

        <div class="content">
            <div class="payment-info no-break">
                <h2 class="section-title">Informasi Pembayaran</h2>
                <div class="info-grid">
                    <div class="info-column">
                        <table>
                            <tr>
                                <th>Status</th>
                                <td>
                                    <span class="status status-{{ $payment->status }}">
                                        @if($payment->status === 'paid')
                                            Lunas
                                        @elseif($payment->status === 'pending')
                                            Menunggu Konfirmasi
                                        @elseif($payment->status === 'used')
                                            Sudah Digunakan
                                        @else
                                            Gagal
                                        @endif
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Total Pembayaran</th>
                                <td class="price">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <th>Metode Pembayaran</th>
                                <td>{{ $payment->method }}</td>
                            </tr>
                            <tr>
                                <th>Tanggal Pembelian</th>
                                <td>{{ $payment->created_at->format('d M Y H:i') }}</td>
                            </tr>
                            @if($payment->paid_at)
                            <tr>
                                <th>Tanggal Pembayaran</th>
                                <td>{{ \Carbon\Carbon::parse($payment->paid_at)->format('d M Y H:i') }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                    <div class="info-column">
                        <table>
                            <tr>
                                <th>Nama Pembeli</th>
                                <td>{{ $payment->buyer_name }}</td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td>{{ $payment->buyer_email }}</td>
                            </tr>
                            <tr>
                                <th>Telepon</th>
                                <td>{{ $payment->buyer_phone }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="ticket-header-group">
                <h2 class="section-title">Detail Tiket</h2>
                <div class="ticket-list">
                    @foreach($payment->tickets as $ticket)
                    <div class="ticket no-break">
                        <div class="ticket-header">
                            <div class="ticket-id">Ticket #{{ $ticket->id }}</div>
                            <div class="event-title">{{ $ticket->event->title }}</div>
                        </div>
                        
                        <div class="ticket-content">
                            <table>
                                <tr>
                                    <th>Status Tiket</th>
                                    <td>
                                        <span class="status status-{{ $ticket->status }}">
                                            @if($ticket->status === 'paid')
                                                Lunas
                                            @elseif($ticket->status === 'pending')
                                                Menunggu Konfirmasi
                                            @elseif($ticket->status === 'used')
                                                Sudah Digunakan
                                            @else
                                                Gagal
                                            @endif
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Nama Peserta</th>
                                    <td>{{ $ticket->participant_name }}</td>
                                </tr>
                                <tr>
                                    <th>Email Peserta</th>
                                    <td>{{ $ticket->participant_email }}</td>
                                </tr>
                                <tr>
                                    <th>Telepon Peserta</th>
                                    <td>{{ $ticket->participant_phone }}</td>
                                </tr>
                                <tr>
                                    <th>Harga Tiket</th>
                                    <td class="price">Rp {{ number_format($ticket->price_paid, 0, ',', '.') }}</td>
                                </tr>
                            </table>

                            <div class="event-details">
                                <h3>Informasi Event</h3>
                                <table>
                                    <tr>
                                        <th>Tanggal</th>
                                        <td>{{ \Carbon\Carbon::parse($ticket->event->start_date)->format('d M Y') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Waktu</th>
                                        <td>{{ \Carbon\Carbon::parse($ticket->event->start_date)->format('H:i') }} - {{ \Carbon\Carbon::parse($ticket->event->end_date)->format('H:i') }} WIB</td>
                                    </tr>
                                    <tr>
                                        <th>Lokasi</th>
                                        <td>{{ $ticket->event->location }}</td>
                                    </tr>
                                </table>
                            </div>

                            @if($ticket->qr_code)
                            <div class="qr-container">
                                <div class="qr-code">
                                    <img src="data:image/png;base64,{{ $ticket->qr_code }}" alt="QR Code">
                                </div>
                                <div class="qr-label">Scan QR code untuk validasi tiket</div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="ticket-footer">
            <div>Generated on {{ now()->format('d M Y H:i') }}</div>
            <div class="disclaimer">
                This is an official e-ticket. Please keep it safe and present it at the event venue.
            </div>
        </div>
    </div>
</body>
</html> 