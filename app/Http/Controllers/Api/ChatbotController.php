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
        $isFinalTurn = $request->input('isFinalTurn', false);
        
        // ==============================================================
        // 1. CEK MODE SAAT INI DARI DATABASE (Fitur Toggle Admin)
        // ==============================================================
        // Jika tabel/setting belum ada, otomatis default ke 'strict' (aman)
        $mode = \App\Models\Setting::where('key', 'form_mode')->value('value') ?? 'strict';

        // ==============================================================
        // 2. ATURAN VALIDASI DINAMIS (Satpam Pintar)
        // ==============================================================
        // Jika mode Strict ATAU ini adalah Turn Terakhir, maka WA & Email WAJIB!
        $isWaEmailRequired = ($mode === 'strict' || $isFinalTurn) ? 'required' : 'nullable';

        $request->validate([
            'userData' => 'required|array',
            'userData.name' => 'required|string',
            'userData.age' => 'required|string',
            'userData.gender' => 'required|string',
            // Tambahan batas maksimal keluhan 1000 karakter agar token tidak dikuras hacker
            'userData.chiefComplaint' => 'required|string|max:1000', 
            'userData.whatsapp' => "$isWaEmailRequired|string", // <-- Dinamis mengikuti mode
            'userData.email' => "$isWaEmailRequired|email",     // <-- Dinamis mengikuti mode
            'chatHistory' => 'required|array',
            'isFinalTurn' => 'required|boolean'
        ]);

        $userData = $request->userData;
        $chatHistory = $request->chatHistory; 

        // MENGHITUNG SUDAH BERAPA KALI TEKTOK
        $turnCount = floor(count($chatHistory) / 2) + 1;

        // ==============================================================
        // 🛡️ 3. SISTEM KEAMANAN GANDA (Hanya dieksekusi di pertanyaan pertama)
        // ==============================================================
        if ($turnCount === 1) {
            $clientIp = $request->ip();
            $whatsapp = isset($userData['whatsapp']) ? trim($userData['whatsapp']) : null;

            // DAFTAR IP VIP (Kosongkan atau isikan IP Localhost saja saat testing)
            $whitelistedIps = ['192.168.0.204', '::1'];

            // A. GEMBOK IP ADDRESS (Anti-Bot / Spam Klik)
            if (!in_array($clientIp, $whitelistedIps)) {
                $ipAttempts = \Illuminate\Support\Facades\Cache::get('chat_attempts_' . $clientIp, 0);
                
                if ($ipAttempts >= 2) { 
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Terlalu banyak permintaan dari jaringan Anda. Silakan coba lagi besok.'
                    ], 429);
                }
                \Illuminate\Support\Facades\Cache::put('chat_attempts_' . $clientIp, $ipAttempts + 1, now()->addHours(24));
            }

            // B. GEMBOK NOMOR WHATSAPP 
            // Mengecek WA HANYA JIKA pasien memasukkannya di awal (saat mode Strict)
            if ($whatsapp) {
                $isLocked = \App\Models\ScreeningReport::where('status', 'valid')
                    ->where('created_at', '>=', now()->subHours(48))
                    // Perbaikan query WA (Tidak pakai JsonContains karena string murni)
                    ->where('user_data->whatsapp', $whatsapp) 
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

        // 4. Instruksi Sistem Super Ketat (IDENTITAS H.A.N.A DITAMBAHKAN DI SINI)
        $bukuPanduanAHCC = "PROFIL AHCC:
        AHCC adalah pusat kanker terintegrasi pertama di Indonesia Timur yang memiliki standar Internasional. Kami menyediakan penanganan kanker berkualitas dengan 3 modalitas utama: radioterapi, kemoterapi, dan pembedahan. Kami didukung oleh dokter spesialis profesional serta memberikan layanan personal dengan sentuhan kehangatan bagi setiap pasien.

        DATA WAJIB TENTANG AHCC (HAFALKAN INI):

        1. ALAMAT & JAM OPERASIONAL:
           - Alamat: Jl. Undaan Wetan No.40–44, Ketabang, Kec. Genteng, Surabaya, Jawa Timur.
           - Petunjuk Lokasi: Menjadi satu area dengan RS Adi Husada Undaan Wetan. Pasien dapat masuk melalui lobi utama RS, kemudian menuju area Cancer Center yang berada di dalam kompleks.
           - Jam Operasional: Senin s/d Jumat (08.00-18.00 WIB) | Sabtu (dengan janji temu) | Minggu & Hari Libur Nasional (TUTUP).

        2. LAYANAN & FASILITAS:
           - AHCC menyediakan terapi: Radioterapi (sinar energi tinggi), Kemoterapi (obat infus/tablet), Imunoterapi (peningkatan imun), Targeted Therapy, dan Pembedahan.
           - Fasilitas Kemoterapi dan Radioterapi di AHCC berstandar internasional, didukung dokter profesional, dan TANPA ANTREAN. Semua terapi sesuai anjuran dokter.

        3. JADWAL DOKTER SPESIALIS:
           [Hematologi Onkologi (Kemoterapi & Konsul Penyakit Dalam)]
           - Prof. Dr. dr. S. Ugroseno Y. Bintoro, Sp.PD, KHOM: Selasa & Kamis (13.00-15.00 WIB).
           - dr. Putu Niken Ayu Amrita, Sp.PD, KHOM: Senin & Rabu (15.00-17.00 WIB).
           
           [Hematologi Onkologi Anak]
           - dr. I Dewa Ayu Agung Shinta Kamaya, Sp.A(K): Selasa & Jumat (16.00-17.00 WIB).

           [Onkologi Kandungan / Obgyn Onco]
           - Dr. dr. Primandono Perbowo, Sp.OG., Subsp.Onk (K): Rabu & Jumat (16.00-18.00 WIB).

           [Spesialis Paru]
           - dr. Prayudi Tetanto, Sp.P,FCCP,FISR: Jumat (08.00-10.00 WIB).

           [Onkologi Radiasi (Tindakan Radioterapi & Konsultasi Biaya)]
           - dr. Dyah Erawati, Sp.Rad (K) Onk.Rad: Selasa (10.00-15.00 WIB) | Senin, Rabu, Jumat (16.00-19.00 WIB).
           - dr. Lulus Handayani, Sp.Rad (K) Onk.Rad: Senin & Rabu (11.00-16.00 WIB) | Selasa & Kamis (15.00-17.00 WIB).
           - dr. Ulinta Purwati, Sp.Rad (K) Onk.Rad: Rabu (10.00-12.00 WIB) | Selasa & Kamis (16.00-18.00 WIB).
           - dr. Yoke Marlina, Sp.Rad Onk.Rad: Kamis (10.00-12.00 WIB) | Senin & Jumat (16.00-19.00 WIB).
           - dr. Yoseph Adi Kristian, Sp.Rad Onk.Rad: Jumat (10.00-12.00 WIB) | Rabu (16.00-18.00 WIB).
           - dr. Yoga Dwi Oktavianda, Sp.Onk.Rad: Rabu & Jumat (15.30-18.30 WIB).

        4. ATURAN PEMBIAYAAN, ASURANSI & BIAYA:
           - BIAYA KONSULTASI DOKTER: Berkisar antara Rp 420.000 hingga Rp 561.000 (tergantung dokter spesialis yang menangani).
           - ESTIMASI BIAYA TINDAKAN: Tidak bisa memberikan estimasi pasti secara online untuk tindakan medis/kemoterapi. Biaya bervariasi tergantung jenis obat, teknik, dosis, dan kondisi pasien. Wajib konsultasi dokter.
           - BPJS KESEHATAN: Saat ini AHCC BELUM bekerja sama dengan BPJS. Layanan menggunakan sistem pembiayaan pribadi (umum).
           - ASURANSI SWASTA: AHCC menerima SELURUH asuransi swasta, KECUALI 7 asuransi ini: Admedika AIA, Allianz, Manulife Halodoc, Ramayana, Mandiri Inhealth, Chubb Life, dan Reliance.

        ATURAN KETAT DAN FALLBACK (PENTING):
        1. Jika pasien menanyakan tentang EVENT, HARGA PROMO, atau informasi APAPUN terkait AHCC yang tidak ada di dalam data wajib di atas, KAMU WAJIB membalas dengan sopan dan arahkan pasien untuk berkonsultasi langsung dengan Patient Advisor kami, Anggi, melalui WhatsApp di nomor: 0822296600.
        2. Jika pasien bertanya hal di luar konteks medis, kanker, atau AHCC (misal: coding, resep masakan, politik), tolak dengan sopan dan ingatkan bahwa kamu adalah Asisten Medis AHCC.";

        $systemInstruction = "Nama Anda adalah H.A.N.A (Health Assessment & Navigation AHCC), asisten virtual medis dan navigator pasien di Rumah Sakit Kanker AHCC. 
        ATURAN MUTLAK IDENTITAS: Anda DILARANG KERAS menyebut diri Anda sebagai AI, kecerdasan buatan, Gemini, atau buatan Google. Jika ditanya identitas, perkenalkan diri Anda dengan ramah sebagai HANA.
        
        INFORMASI RUMAH SAKIT UNTUK DIJAWAB JIKA DITANYA:
        {$bukuPanduanAHCC}

        Nama pasien: {$userData['name']}, Usia: {$userData['age']}, Kelamin: {$userData['gender']}. 
        Keluhan awal: {$userData['chiefComplaint']}.

        ATURAN MUTLAK FORMAT TEKS: 
        Pisahkan poin-poin dengan spasi baris ganda (\\n\\n). JANGAN menulis paragraf panjang yang menyambung.

        ATURAN MUTLAK MEDIS (ANAMNESIS):
        Sebagai sistem AI standar rumah sakit, Anda DILARANG KERAS menarik kesimpulan klinis hanya dari keluhan awal. Anda WAJIB melakukan anamnesis (tanya jawab) yang mendalam. Tanyakan hal-hal seperti: riwayat penyakit keluarga, sudah berapa lama gejala muncul, faktor pemicu, atau keluhan penyerta lainnya. Bertanyalah 1 atau 2 pertanyaan saja pada setiap giliran agar pasien tidak merasa diinterogasi. Jika pasien justru bertanya tentang AHCC, jawablah sesuai data di atas dan lanjutkan anamnesis dengan luwes.

        ATURAN MUTLAK RESPON: Anda WAJIB merespons HANYA dengan format JSON murni.
        Pilih salah satu 'type' berikut:
        1. 'rejected' -> JIKA keluhan pasien iseng/bercanda di luar konteks.
        2. 'ask_image' -> JIKA pasien menyebutkan hasil lab/rontgen tapi belum mengirimkannya.
        3. 'chat' -> JIKA Anda sedang melakukan anamnesis atau menjawab pertanyaan pasien seputar fasilitas AHCC.";

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

        // 5. Menyusun format pesan (Mendukung Teks + BANYAK Gambar)
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

        // 6. Panggil Gemini 3 Flash Preview (Mode Aman)
        $apiKey = trim(env('GEMINI_API_KEY'));
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-3-flash-preview:generateContent?key={$apiKey}";

        $response = \Illuminate\Support\Facades\Http::post($url, [
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
                \App\Models\ScreeningReport::create([
                    'id' => (string) \Illuminate\Support\Str::uuid(),
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
                $reportId = (string) \Illuminate\Support\Str::uuid();
                \App\Models\ScreeningReport::create([
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