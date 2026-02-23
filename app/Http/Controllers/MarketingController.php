<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ScreeningReport;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class MarketingController extends Controller
{
    // 1. Menampilkan Halaman
    public function index()
    {
        // Ambil riwayat percakapan dan tanggal aktif dari Session
        $history = Session::get('marketing_history', []);
        $activeDates = Session::get('marketing_dates', null);

        return view('marketing', compact('history', 'activeDates'));
    }

    // 2. Generate Laporan Awal (Berdasarkan Rentang Tanggal)
    public function generate(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        // Ambil data berdasarkan rentang tanggal
        $query = ScreeningReport::where('status', 'valid');
        if ($request->start_date) $query->whereDate('created_at', '>=', $request->start_date);
        if ($request->end_date) $query->whereDate('created_at', '<=', $request->end_date);
        
        $reports = $query->orderBy('created_at', 'desc')->get();

        if ($reports->isEmpty()) {
            return back()->with('error', 'Belum ada data pasien yang valid pada rentang tanggal tersebut.');
        }

        // Kumpulkan data menjadi teks
        $dataTeks = [];
        foreach ($reports as $report) {
            $keluhan = $report->user_data['chiefComplaint'] ?? '-';
            $usia = $report->user_data['age'] ?? '-';
            $suspek = implode(", ", $report->report_data['suspected_conditions'] ?? []);
            $dataTeks[] = "Pasien Usia {$usia} thn. Keluhan: {$keluhan}. Suspek AI: {$suspek}";
        }
        $gabunganData = implode("\n", $dataTeks);

        // Prompt Sistem yang Disembunyikan
        $prompt = "Anda adalah Chief Marketing Officer (CMO) dan Data Analyst medis di rumah sakit kanker AHCC. \n\n"
                . "Berikut adalah data keluhan dan kecurigaan diagnosa (suspek) pasien dari aplikasi skrining kita pada periode yang dipilih admin:\n"
                . $gabunganData . "\n\n"
                . "Tugas Pertama Anda:\n"
                . "Buatkan 'Marketing & Content Insights' berdasarkan tren dari data di atas. Gunakan format HTML bersih (hanya gunakan tag <h3>, <p>, <ul>, <li>, dan <strong>). Jangan gunakan styling CSS, jangan gunakan tag <html> atau ```html.\n\n"
                . "Struktur Wajib:\n"
                . "<h3>1. Tren Keluhan Utama Pasien</h3>\n"
                . "<p>(Jelaskan tren utamanya)</p>\n"
                . "<h3>2. Profil Keresahan (Pain Points)</h3>\n"
                . "<p>(Apa yang sebenarnya ditakutkan oleh pasien-pasien ini?)</p>\n"
                . "<h3>3. Rekomendasi Kampanye</h3>\n"
                . "<ul><li>(Ide konten atau promo)</li></ul>";

        // Tembak API
        $apiKey = trim(env('GEMINI_API_KEY'));
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-3-flash-preview:generateContent?key={$apiKey}";

        // Mulai riwayat percakapan baru
        $history = [
            ["role" => "user", "parts" => [["text" => $prompt]]]
        ];

        $response = Http::post($url, ["contents" => $history]);

        if ($response->successful()) {
            $reply = $response->json('candidates.0.content.parts.0.text');
            $cleanHtml = trim(preg_replace('/^```html\s*|```\s*$/i', '', $reply));

            // Simpan jawaban AI ke dalam riwayat
            $history[] = ["role" => "model", "parts" => [["text" => $cleanHtml]]];

            // Simpan ke Session
            Session::put('marketing_history', $history);
            Session::put('marketing_dates', [
                'start' => $request->start_date, 
                'end' => $request->end_date
            ]);

            return back()->with('success', 'Analisis awal berhasil dibuat! Silakan tanyakan hal lebih spesifik di bawah.');
        }

        return back()->with('error', 'Gagal menghubungi AI Server.');
    }

    // 3. Follow Up Chat (Tanya Jawab Lanjutan)
    public function chat(Request $request)
    {
        $request->validate(['message' => 'required|string']);
        
        $history = Session::get('marketing_history', []);
        if (empty($history)) return back();

        // Tambahkan instruksi agar AI tetap menjawab dengan format HTML rapi
        $userMessage = $request->message . "\n\n(Tolong jawab menggunakan tag HTML dasar seperti <p>, <ul>, <li>, <strong>, atau <br> agar rapi saat ditampilkan).";

        // Masukkan pertanyaan admin ke riwayat
        $history[] = ["role" => "user", "parts" => [["text" => $userMessage]]];

        $apiKey = trim(env('GEMINI_API_KEY'));
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-3-flash-preview:generateContent?key={$apiKey}";

        $response = Http::post($url, ["contents" => $history]);

        if ($response->successful()) {
            $reply = $response->json('candidates.0.content.parts.0.text');
            $cleanHtml = trim(preg_replace('/^```html\s*|```\s*$/i', '', $reply));

            // Simpan jawaban AI ke riwayat
            $history[] = ["role" => "model", "parts" => [["text" => $cleanHtml]]];
            
            // Perbarui Session
            Session::put('marketing_history', $history);

            return back();
        }

        return back()->with('error', 'Gagal memproses pertanyaan Anda.');
    }

    // 4. Reset Percakapan
    public function reset()
    {
        Session::forget(['marketing_history', 'marketing_dates']);
        return redirect('/marketing');
    }
}