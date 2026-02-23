<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ScreeningReport;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // 1. Mulai dengan Query Builder kosong
        $query = ScreeningReport::query();

        // 2. Fitur Pencarian (Nama / WA)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('user_data->name', 'like', "%{$search}%")
                  ->orWhere('user_data->whatsapp', 'like', "%{$search}%");
            });
        }

        // 3. Fitur Filter Risiko
        if ($request->filled('risk')) {
            $risk = $request->risk;
            $query->where('report_data->risk_level', $risk);
        }

        // 4. Fitur Filter Tanggal (Start Date & End Date)
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // 5. Eksekusi query & Pagination
        $reports = $query->orderBy('created_at', 'desc')
                         ->paginate(10)
                         ->appends($request->query()); // Simpan parameter URL
        
        return view('dashboard', compact('reports'));
    }

    public function show($id)
    {
        // Cari laporan berdasarkan ID, jika tidak ada munculkan error 404
        $report = ScreeningReport::findOrFail($id);
        
        // Buka halaman detail
        return view('dashboard-detail', compact('report'));
    }
}