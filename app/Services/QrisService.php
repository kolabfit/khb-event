<?php

namespace App\Services;

use App\Models\QrisSetting;
use Illuminate\Support\Facades\Storage;

class QrisService
{
    public function getActiveQris()
    {
        return QrisSetting::where('is_active', true)->first();
    }

    public function generateQrisPayload($amount, $merchantName, $merchantCity)
    {
        // Format: 00020101021229370016A0000006770101110215ID12345678901234580214ID12345678901234550303UKE520459995802ID5913Merchant Name6007Jakarta61051234562070703A016304
        $payload = [
            '000201010212', // Static header
            '2937', // Static header
            '0016A000000677010111', // Static header
            '52045999', // Static header
            '5802ID', // Country code
            '59' . strlen($merchantName) . $merchantName, // Merchant Name
            '60' . strlen($merchantCity) . $merchantCity, // Merchant City
            '62' . strlen($amount) . $amount, // Transaction Amount
            '0703A01', // Static footer
            '6304', // CRC
        ];

        return implode('', $payload);
    }

    public function storeQrisImage($image)
    {
        $path = $image->store('qris', 'public');
        return $path;
    }

    public function deleteQrisImage($path)
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }
} 