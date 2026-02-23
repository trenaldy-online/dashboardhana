<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ScreeningReport;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    // --- FUNGSI KAMUS CERDAS UNTUK KATEGORISASI POLI / DOKTER ---
    private function getDepartment($suspectName)
    {
        $suspect = strtolower($suspectName);

        // 1. Onkologi Anak (Pediatrik) - Murni deteksi dari jenis kanker khas anak
        if (str_contains($suspect, 'anak') || str_contains($suspect, 'pediatrik') || str_contains($suspect, 'pediatric') || str_contains($suspect, 'neuroblastoma') || str_contains($suspect, 'wilms') || str_contains($suspect, 'nefroblastoma') || str_contains($suspect, 'hepatoblastoma') || str_contains($suspect, 'retinoblastoma') || str_contains($suspect, 'rhabdomyosarcoma')) return 'Onkologi Anak (Pediatrik)';
        
        // 2. Breast Center
        if (str_contains($suspect, 'payudara') || str_contains($suspect, 'mammae') || str_contains($suspect, 'breast') || str_contains($suspect, 'fibroadenoma') || str_contains($suspect, 'fam') || str_contains($suspect, 'paget')) return 'Breast Center';
        
        // 3. Pulmonologi 
        if (str_contains($suspect, 'paru') || str_contains($suspect, 'pulmo') || str_contains($suspect, 'lung') || str_contains($suspect, 'bronkus') || str_contains($suspect, 'pleura') || str_contains($suspect, 'mesothelioma') || str_contains($suspect, 'mediastinum')) return 'Pulmonologi';
        
        // 4. Gastrointestinal 
        if (str_contains($suspect, 'kolorektal') || str_contains($suspect, 'usus') || str_contains($suspect, 'lambung') || str_contains($suspect, 'gastro') || str_contains($suspect, 'hepar') || str_contains($suspect, 'hati') || str_contains($suspect, 'pankreas') || str_contains($suspect, 'empedu') || str_contains($suspect, 'rektum') || str_contains($suspect, 'kolon') || str_contains($suspect, 'colon') || str_contains($suspect, 'esofagus') || str_contains($suspect, 'gaster') || str_contains($suspect, 'biliary')) return 'Gastrointestinal';
        
        // 5. Urologi 
        if (str_contains($suspect, 'prostat') || str_contains($suspect, 'ginjal') || str_contains($suspect, 'testis') || str_contains($suspect, 'kemih') || str_contains($suspect, 'buli') || str_contains($suspect, 'bladder') || str_contains($suspect, 'ureter') || str_contains($suspect, 'renal') || str_contains($suspect, 'penis')) return 'Urologi';
        
        // 6. Ginekologi
        if (str_contains($suspect, 'serviks') || str_contains($suspect, 'ovarium') || str_contains($suspect, 'rahim') || str_contains($suspect, 'kandungan') || str_contains($suspect, 'endometrium') || str_contains($suspect, 'cervix') || str_contains($suspect, 'uterus') || str_contains($suspect, 'vagina') || str_contains($suspect, 'vulva') || str_contains($suspect, 'trofoblas')) return 'Ginekologi';
        
        // 7. Head & Neck
        if (str_contains($suspect, 'kepala') || str_contains($suspect, 'leher') || str_contains($suspect, 'tiroid') || str_contains($suspect, 'nasofaring') || str_contains($suspect, 'laring') || str_contains($suspect, 'faring') || str_contains($suspect, 'mulut') || str_contains($suspect, 'lidah') || str_contains($suspect, 'tonsil') || str_contains($suspect, 'oral') || str_contains($suspect, 'parotis') || str_contains($suspect, 'liur')) return 'Head & Neck';
        
        // 8. Neurologi
        if (str_contains($suspect, 'otak') || str_contains($suspect, 'saraf') || str_contains($suspect, 'neuro') || str_contains($suspect, 'meningioma') || str_contains($suspect, 'glioma') || str_contains($suspect, 'glioblastoma') || str_contains($suspect, 'spinal') || str_contains($suspect, 'medula')) return 'Neurologi';
        
        // 9. Hematologi
        if (str_contains($suspect, 'darah') || str_contains($suspect, 'leukemia') || str_contains($suspect, 'limfoma') || str_contains($suspect, 'myeloma') || str_contains($suspect, 'mieloma') || str_contains($suspect, 'hodgkin')) return 'Hematologi';
        
        // 10. Sarkoma / Ortopedi
        if (str_contains($suspect, 'tulang') || str_contains($suspect, 'sarkoma') || str_contains($suspect, 'sarcoma') || str_contains($suspect, 'osteosarkoma') || str_contains($suspect, 'ewing') || str_contains($suspect, 'jaringan') || str_contains($suspect, 'soft tissue')) return 'Sarkoma & Ortopedi';
        
        // 11. Onkologi Kulit
        if (str_contains($suspect, 'kulit') || str_contains($suspect, 'melanoma') || str_contains($suspect, 'basal') || str_contains($suspect, 'skuamosa') || str_contains($suspect, 'squamous') || str_contains($suspect, 'bcc') || str_contains($suspect, 'scc')) return 'Onkologi Kulit';

        // 12. Onkologi Mata 
        if (str_contains($suspect, 'mata') || str_contains($suspect, 'retina')) return 'Onkologi Mata';

        // 13. Tumor Endokrin
        if (str_contains($suspect, 'endokrin') || str_contains($suspect, 'adrenal') || str_contains($suspect, 'pituitari') || str_contains($suspect, 'hipofisis')) return 'Endokrinologi';
        
        return 'General / Lainnya';
    }

    public function index(Request $request)
    {
        $query = ScreeningReport::query();

        // 1. Filter Rentang Waktu
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $reports = $query->get();

        // 2. Hitung Valid vs Non Valid
        $totalValid = $reports->where('status', 'valid')->count();
        $totalInvalid = $reports->where('status', 'invalid')->count();

        // 3. Hitung Tingkat Risiko
        $riskHigh = 0; $riskMedium = 0; $riskLow = 0;
        
        // 4. Hitung Suspek Penyakit & Mapping ke Departemen
        $allSuspects = [];
        $departmentStats = []; // Menyimpan jumlah per Poliklinik/Kamus

        foreach ($reports->where('status', 'valid') as $report) {
            $level = $report->report_data['risk_level'] ?? '';
            if ($level == 'Tinggi') $riskHigh++;
            elseif ($level == 'Sedang') $riskMedium++;
            elseif ($level == 'Rendah') $riskLow++;

            // Ekstrak Diagnosa Suspek AI
            $suspects = $report->report_data['suspected_conditions'] ?? [];
            foreach ($suspects as $suspect) {
                $dept = $this->getDepartment($suspect);

                // Hitung per penyakit
                if (!isset($allSuspects[$suspect])) {
                    $allSuspects[$suspect] = ['count' => 0, 'dept' => $dept];
                }
                $allSuspects[$suspect]['count']++;

                // Hitung per Poliklinik (Kamus)
                if (!isset($departmentStats[$dept])) {
                    $departmentStats[$dept] = 0;
                }
                $departmentStats[$dept]++;
            }
        }

        // Urutkan Penyakit dari yang terbanyak
        uasort($allSuspects, function($a, $b) {
            return $b['count'] <=> $a['count'];
        });

        // Urutkan Poliklinik dari yang terbanyak
        arsort($departmentStats);

        // 5. Hitung Token dan Estimasi Biaya
        $totalTokens = $reports->sum('tokens_used');
        $estimasiBiayaRp = $totalTokens * 0.003; 

        // 6. Data untuk Grafik (Tren Harian & Biaya)
        $chartDates = [];
        $chartTokens = [];
        $chartCosts = []; 

        // PERBAIKAN: Kelompokkan berdasarkan 'Y-m-d' agar bisa diurutkan secara alfabetis yang benar, lalu gunakan ->sortKeys()
        $groupedByDate = $reports->groupBy(function($item) {
            return \Carbon\Carbon::parse($item->created_at)->format('Y-m-d');
        })->sortKeys();

        foreach ($groupedByDate as $date => $items) {
            $tokens = $items->sum('tokens_used');
            
            // Ubah formatnya kembali menjadi 'd M' (misal: 21 Feb) untuk teks di bawah grafik
            $chartDates[] = \Carbon\Carbon::parse($date)->format('d M');
            $chartTokens[] = $tokens;
            $chartCosts[] = $tokens * 0.003; 
        }

        return view('analytics', compact(
            'totalValid', 'totalInvalid', 'riskHigh', 'riskMedium', 'riskLow',
            'allSuspects', 'departmentStats', 'totalTokens', 'estimasiBiayaRp', 
            'chartDates', 'chartTokens', 'chartCosts'
        ));
    }
}