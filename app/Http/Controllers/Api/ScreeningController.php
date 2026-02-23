<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Screening;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ScreeningController extends Controller
{
    public function store(Request $request)
    {
        // ==========================================
        // 1. CEK IDENTITAS (IP ADDRESS) PENGIRIM
        // ==========================================
        $clientIp = $request->ip();

        // DAFTAR IP VIP (Bebas limitasi)
        $whitelistedIps = [
            '192.168.0.204',        // IP Localhost
            '::1',              // IP Localhost IPv6
            '103.165.42.166',    // (Nanti ganti dengan IP WiFi Rumah Sakit)
        ];

        // JIKA BUKAN VIP, CEK APAKAH SEDANG DIKUNCI
        if (!in_array($clientIp, $whitelistedIps)) {
            if (Cache::has('screening_lock_' . $clientIp)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda telah mencapai batas maksimal pengisian form. Silakan hubungi Patient Advisor kami atau coba lagi dalam 48 jam.'
                ], 429); // 429: Too Many Requests
            }
        }

        // ==========================================
        // 2. VALIDASI DATA DARI REACT
        // ==========================================
        $validated = $request->validate([
            'userData.name' => 'required|string',
            'userData.whatsapp' => 'required|string',
            'userData.email' => 'required|email',
            'userData.infoSource' => 'nullable|string',
            'userData.marketingOptIn' => 'boolean',
            'cancerType' => 'required|string',
            'riskLevel' => 'required|string',
            'summary' => 'required|string',
        ]);

        // ==========================================
        // 3. SIMPAN KE DATABASE MYSQL
        // ==========================================
        $screening = Screening::create([
            'name' => $validated['userData']['name'],
            'whatsapp' => $validated['userData']['whatsapp'],
            'email' => $validated['userData']['email'],
            'info_source' => $validated['userData']['infoSource'] ?? null,
            'marketing_opt_in' => $validated['userData']['marketingOptIn'] ?? false,
            'cancer_type' => $validated['cancerType'],
            'risk_level' => $validated['riskLevel'],
            'summary' => $validated['summary'],
        ]);

        // ==========================================
        // 4. KUNCI IP PENGIRIM (JIKA BUKAN VIP)
        // ==========================================
        if (!in_array($clientIp, $whitelistedIps)) {
            // Kunci IP ini di dalam Cache selama 48 Jam ke depan
            Cache::put('screening_lock_' . $clientIp, true, now()->addHours(48));
        }

        // ==========================================
        // 5. BERIKAN RESPON SUKSES KE REACT
        // ==========================================
        return response()->json([
            'status' => 'success',
            'message' => 'Data screening berhasil disimpan!',
            'data' => $screening
        ], 201);
    }
}