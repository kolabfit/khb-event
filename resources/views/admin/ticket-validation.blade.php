<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Validasi Tiket</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://unpkg.com/html5-qrcode"></script>
    <style>
        .scanner-container {
            max-width: 500px;
            margin: 0 auto;
            padding: 20px;
        }
        #reader {
            width: 100%;
            border: 2px solid #8A2BE2;
            border-radius: 8px;
            overflow: hidden;
        }
        .result-container {
            margin-top: 20px;
            padding: 15px;
            border-radius: 8px;
            display: none;
        }
        .result-container.success {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        .result-container.error {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        .result-container.used-ticket {
            background-color: #e9ecef;
            border: 1px solid #dee2e6;
            color: #495057;
        }
        .ticket-info {
            margin-top: 15px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        .ticket-info p {
            margin: 5px 0;
        }
        .back-button {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1000;
        }
        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <a href="{{ route('filament.admin.pages.dashboard') }}" class="btn btn-outline back-button" style="background-color: #8A2BE2; color: white;">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left me-2" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
        </svg>
        Kembali ke Dashboard
    </a>

    <div class="container mt-5">
        <div class="scanner-container">
            <h2 class="text-center mb-4">Validasi Tiket</h2>
            
            <div id="reader"></div>
            
            <div class="action-buttons">
                <button onclick="resetScanner()" class="btn" style="background-color: #8A2BE2; color: white;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-clockwise me-2" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2v1z"/>
                        <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466z"/>
                    </svg>
                    Scan Lagi
                </button>
            </div>
            
            <div id="result" class="result-container">
                <h4 id="result-title"></h4>
                <p id="result-message"></p>
                <div id="ticket-details" class="ticket-info" style="display: none;">
                    <p><strong>ID Tiket:</strong> <span id="ticket-id"></span></p>
                    <p><strong>Event:</strong> <span id="event-name"></span></p>
                    <p><strong>Peserta:</strong> <span id="participant-name"></span></p>
                    <p><strong>Status:</strong> <span id="ticket-status"></span></p>
                    <p><strong>Validasi:</strong> <span id="validated-at"></span></p>
                </div>
            </div>
        </div>
    </div>

    <script>
        let html5QrcodeScanner;

        function initScanner() {
            html5QrcodeScanner = new Html5QrcodeScanner(
                "reader",
                { fps: 10, qrbox: {width: 250, height: 250} },
                false
            );
            html5QrcodeScanner.render(onScanSuccess, onScanFailure);
        }

        function resetScanner() {
            if (html5QrcodeScanner) {
                html5QrcodeScanner.clear().then(() => {
                    document.getElementById('result').style.display = 'none';
                    document.getElementById('ticket-details').style.display = 'none';
                    initScanner();
                }).catch((err) => {
                    console.warn('Failed to reset scanner:', err);
                });
            } else {
                initScanner();
            }
        }

        function onScanSuccess(decodedText, decodedResult) {
            html5QrcodeScanner.pause(true);
            
            // Ambil ID tiket dari URL
            const parts = decodedText.split('/');
            let ticketId = parts[parts.length - 2];
            if (isNaN(ticketId)) {
                ticketId = parts.find(p => /^\d+$/.test(p));
            }
            
            // Validate ticket
            fetch('/admin/validate-ticket', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ ticket_id: ticketId })
            })
            .then(response => {
                if (!response.ok) throw new Error('Network error');
                return response.json();
            })
            .then(data => {
                const resultDiv = document.getElementById('result');
                const resultTitle = document.getElementById('result-title');
                const resultMessage = document.getElementById('result-message');
                const ticketDetails = document.getElementById('ticket-details');
                
                resultDiv.style.display = 'block';
                
                if (data.success) {
                    resultDiv.className = 'result-container success';
                    resultTitle.textContent = 'Validasi Berhasil';
                    resultMessage.textContent = data.message;
                } else if (data.success === 'used') {
                    resultDiv.className = 'result-container used-ticket';
                    resultTitle.textContent = 'Tiket Sudah Digunakan';
                    resultMessage.textContent = data.message;
                } else {
                    resultDiv.className = 'result-container error';
                    resultTitle.textContent = 'Validasi Gagal';
                    resultMessage.textContent = data.message;
                }
                
                if (data.ticket) {
                    ticketDetails.style.display = 'block';
                    document.getElementById('ticket-id').textContent = data.ticket.id;
                    document.getElementById('event-name').textContent = data.ticket.event;
                    document.getElementById('participant-name').textContent = data.ticket.participant;
                    document.getElementById('ticket-status').textContent = data.ticket.status;
                    document.getElementById('validated-at').textContent = data.ticket.validated_at;
                } else {
                    ticketDetails.style.display = 'none';
                }
            })
            .catch(error => {
                const resultDiv = document.getElementById('result');
                const resultTitle = document.getElementById('result-title');
                const resultMessage = document.getElementById('result-message');
                
                resultDiv.style.display = 'block';
                resultDiv.className = 'result-container error';
                resultTitle.textContent = 'Error';
                resultMessage.textContent = error.message;
            });
        }

        function onScanFailure(error) {
            console.warn(`QR Code scan error: ${error}`);
        }

        // Initialize scanner when page loads
        initScanner();
    </script>
</body>
</html> 