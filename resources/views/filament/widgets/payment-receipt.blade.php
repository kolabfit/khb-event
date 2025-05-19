<div style="text-align:center;">
    @php
        $isImage = false;
        $isPdf = false;
        if ($receiptUrl) {
            $ext = strtolower(pathinfo($receiptUrl, PATHINFO_EXTENSION));
            $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
            $isPdf = $ext === 'pdf';
        }
    @endphp

    @if ($isImage)
        <img src="{{ $receiptUrl }}" alt="Bukti Pembayaran" style="max-width:100%; max-height:500px; border-radius:8px;" />
    @elseif ($isPdf)
        <a href="{{ $receiptUrl }}" target="_blank" style="display:block; margin-bottom:1rem;">Download PDF</a>
        <iframe src="{{ $receiptUrl }}" style="width:100%; height:500px; border:none;"></iframe>
    @else
        <a href="{{ $receiptUrl }}" target="_blank">Lihat Bukti Pembayaran</a>
    @endif
</div> 