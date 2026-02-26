<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\Models\ScreeningReport;
use Illuminate\Support\Facades\Cache;

class ChatbotController extends Controller
{
    public function chat(Request $request)
    {
        $request->validate([
            'userData' => 'required|array',
            'chatHistory' => 'required|array',
            'isFinalTurn' => 'required|boolean'
        ]);

        $userData = $request->userData;
        $chatHistory = $request->chatHistory; 
        $isFinalTurn = $request->isFinalTurn;

        // MENGHITUNG SUDAH BERAPA KALI TEKTOK (Satu pasang = 2 elemen di array)
        // Jika array kosong, berarti ini pertanyaan pertama.
        $turnCount = floor(count($chatHistory) / 2) + 1;

        // ==============================================================
        // 🛡️ SISTEM KEAMANAN GANDA (Hanya dieksekusi di pertanyaan pertama)
        // ==============================================================
        if ($turnCount === 1) {
            $clientIp = $request->ip();
            $whatsapp = $userData['whatsapp'] ?? null;

            // DAFTAR IP VIP (Bebas limitasi)
            $whitelistedIps = ['192.168.0.204', '::1', '103.165.42.166'];

            // 1. GEMBOK IP ADDRESS (Anti-Bot / Spam Klik)
            if (!in_array($clientIp, $whitelistedIps)) {
                // Hitung berapa kali IP ini mencoba memulai chat hari ini
                $ipAttempts = Cache::get('chat_attempts_' . $clientIp, 0);
                
                if ($ipAttempts >= 2) { // Maksimal 2 kali percobaan form dari 1 IP
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Terlalu banyak permintaan dari jaringan Anda. Silakan coba lagi besok.'
                    ], 429);
                }
                // Tambah hitungan percobaan untuk IP ini (disimpan selama 24 jam)
                Cache::put('chat_attempts_' . $clientIp, $ipAttempts + 1, now()->addHours(24));
            }

            // 2. GEMBOK NOMOR WHATSAPP (Anti-Incognito & Anti Cross-Domain)
            if ($whatsapp) {
                $isLocked = ScreeningReport::where('created_at', '>=', now()->subHours(48))
                    ->whereJsonContains('user_data->whatsapp', $whatsapp)
                    ->where('status', 'valid') // Hanya kunci jika skrining sebelumnya berhasil sampai selesai
                    ->exists();

                if ($isLocked) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Nomor WhatsApp ini telah menyelesaikan skrining dalam 48 jam terakhir. Silakan coba lagi nanti atau hubungi Patient Advisor kami.'
                    ], 429);
                }
            }
        }
        // ==============================================================

        // 1. Instruksi Sistem Super Ketat (IDENTITAS H.A.N.A DITAMBAHKAN DI SINI)
        $systemInstruction = "Nama Anda adalah H.A.N.A (Health Assessment & Navigation AHCC), asisten virtual medis dan navigator pasien di Rumah Sakit Kanker AHCC. 
        ATURAN MUTLAK IDENTITAS: Anda DILARANG KERAS menyebut diri Anda sebagai AI, kecerdasan buatan, Gemini, atau buatan Google. Jika ditanya identitas, perkenalkan diri Anda dengan ramah sebagai HANA.
        
        Nama pasien: {$userData['name']}, Usia: {$userData['age']}, Kelamin: {$userData['gender']}. 
        Keluhan awal: {$userData['chiefComplaint']}.

        ATURAN MUTLAK FORMAT TEKS: 
        Pisahkan poin-poin dengan spasi baris ganda (\\n\\n). JANGAN menulis paragraf panjang yang menyambung.

        ATURAN MUTLAK MEDIS (ANAMNESIS):
        Sebagai sistem AI standar rumah sakit, Anda DILARANG KERAS menarik kesimpulan klinis hanya dari keluhan awal. Anda WAJIB melakukan anamnesis (tanya jawab) yang mendalam. Tanyakan hal-hal seperti: riwayat penyakit keluarga, sudah berapa lama gejala muncul, faktor pemicu, atau keluhan penyerta lainnya. Bertanyalah 1 atau 2 pertanyaan saja pada setiap giliran agar pasien tidak merasa diinterogasi.

        ATURAN MUTLAK RESPON: Anda WAJIB merespons HANYA dengan format JSON murni.
        Pilih salah satu 'type' berikut:
        1. 'rejected' -> JIKA keluhan pasien iseng/bercanda.
        2. 'ask_image' -> JIKA pasien menyebutkan hasil lab/rontgen tapi belum mengirimkannya.
        3. 'chat' -> JIKA Anda sedang melakukan anamnesis/menggali gejala.";

        // --- LOGIKA PENGUNCIAN KESIMPULAN (MINIMAL 4 TURN) ---
        if ($isFinalTurn) {
            $systemInstruction .= " \n\nINI ADALAH GILIRAN TERAKHIR. Anda WAJIB menghentikan anamnesis dan menggunakan type: 'final_report'.";
        } elseif ($turnCount < 4) {
            $systemInstruction .= " \n\nSTATUS SAAT INI: Giliran ke-{$turnCount}. Anda MASIH TAHAP AWAL ANAMNESIS. Anda DILARANG KERAS menggunakan type 'final_report'. Anda WAJIB membalas dengan type 'chat' atau 'ask_image'.";
        } else {
            $systemInstruction .= " \n\nSTATUS SAAT INI: Giliran ke-{$turnCount}. Anda HANYA BOLEH menggunakan type: 'final_report' JIKA informasi anamnesis sudah SANGAT LENGKAP dan solid. Jika masih ragu, tetap gunakan type 'chat'.";
        }

        $systemInstruction .= "\n\nSTRUKTUR JSON YANG WAJIB DIKEMBALIKAN:
        {
            \"type\": \"(isi dengan rejected / ask_image / chat / final_report)\",
            \"message\": \"(Teks pertanyaan anamnesis Anda. Kosongkan jika type adalah final_report)\",
            \"report\": {
                \"risk_score\": (angka 1-100),
                \"risk_level\": \"(Rendah/Sedang/Tinggi)\",
                \"summary\": \"(Rangkuman keluhan)\",
                \"anamnesis_reasoning\": \"(Penjelasan klinis naratif)\",
                \"identified_symptoms\": [\"gejala1\"],
                \"suspected_conditions\": [\"suspek1\"],
                \"recommendations\": [\"saran1\"]
            }
        }";

        if ($isFinalTurn) {
            $systemInstruction .= " INI ADALAH GILIRAN TERAKHIR. Anda WAJIB menggunakan type: 'final_report'.";
        }

        // 2. Menyusun format pesan (Mendukung Teks + BANYAK Gambar)
        $contents = [];
        foreach ($chatHistory as $chat) {
            $parts = [];
            
            // Masukkan Teks
            if (!empty($chat['text'])) {
                $parts[] = ["text" => $chat['text']];
            }
            
            // Masukkan Gambar (Mendukung Array / Multiple Images)
            if (!empty($chat['images']) && is_array($chat['images'])) {
                foreach ($chat['images'] as $imageStr) {
                    $imageParts = explode(';', $imageStr);
                    $mimeType = str_replace('data:', '', $imageParts[0]);
                    $base64Data = explode(',', $imageParts[1])[1];

                    $parts[] = [
                        "inlineData" => [
                            "mimeType" => $mimeType,
                            "data" => $base64Data
                        ]
                    ];
                }
            }

            $contents[] = [
                "role" => $chat['role'] === 'ai' ? 'model' : 'user',
                "parts" => $parts
            ];
        }

        // 3. Panggil Gemini 3 Flash Preview (Mode Aman)
        $apiKey = trim(env('GEMINI_API_KEY'));
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-3-flash-preview:generateContent?key={$apiKey}";

        $response = Http::post($url, [
            "system_instruction" => [
                "parts" => [["text" => $systemInstruction]]
            ],
            "contents" => $contents
        ]);

        if ($response->successful()) {
            $replyJsonString = $response->json('candidates.0.content.parts.0.text');
            $cleanJson = trim(preg_replace('/^```json\s*|```\s*$/i', '', $replyJsonString));
            $aiData = json_decode($cleanJson, true);
            
            // --- TANGKAP JUMLAH TOKEN YANG DIGUNAKAN ---
            $tokensUsed = $response->json('usageMetadata.totalTokenCount') ?? 0;

            $reportId = null;

            // 1. JIKA PASIEN ISENG (NON-VALID), KITA TETAP SIMPAN SEBAGAI LOG ANALITIK
            if (isset($aiData['type']) && $aiData['type'] === 'rejected') {
                ScreeningReport::create([
                    'id' => (string) Str::uuid(),
                    'user_data' => $userData,
                    'status' => 'invalid', // Status Non-Valid
                    'ip_address' => $request->ip(),
                    'tokens_used' => $tokensUsed,
                    'chat_history' => $chatHistory,
                    'report_data' => ['message' => $aiData['message']]
                ]);
            }

            // 2. JIKA LAPORAN FINAL (VALID), SIMPAN SEPERTI BIASA
            if (isset($aiData['type']) && $aiData['type'] === 'final_report') {
                $reportId = (string) Str::uuid();
                ScreeningReport::create([
                    'id' => $reportId,
                    'user_data' => $userData,
                    'status' => 'valid', // Status Valid
                    'ip_address' => $request->ip(),
                    'tokens_used' => $tokensUsed,
                    'chat_history' => $chatHistory,
                    'report_data' => $aiData['report']
                ]);
            }

            return response()->json([
                'status' => 'success',
                'data' => $aiData,
                'report_id' => $reportId
            ]);
        } else {
            return response()->json([
                'status' => 'error', 
                'message' => 'Gagal mendapatkan respon dari model AI'
            ], 500);
        }
    }
        

    // FUNGSI BARU UNTUK MENGAMBIL LAPORAN DARI DATABASE
    public function getReport($id)
    {
        $report = ScreeningReport::find($id);
        
        if (!$report) {
            return response()->json([
                'status' => 'error', 
                'message' => 'Laporan tidak ditemukan'
            ], 404);
        }
        
        return response()->json([
            'status' => 'success',
            'user_data' => $report->user_data,
            'chat_history' => $report->chat_history,
            'report_data' => $report->report_data
        ]);
    }
}