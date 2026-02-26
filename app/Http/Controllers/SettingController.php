<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    // Menampilkan halaman dashboard toggle
    public function index()
    {
        // Ambil setting saat ini, jika tidak ada default ke 'strict'
        $mode = Setting::where('key', 'form_mode')->value('value') ?? 'strict';
        
        return view('admin.settings', compact('mode'));
    }

    // Memproses perubahan saat admin mengklik toggle
    public function toggle(Request $request)
    {
        $newMode = $request->input('mode'); // Akan berisi 'strict' atau 'relaxed'
        
        Setting::updateOrCreate(
            ['key' => 'form_mode'],
            ['value' => $newMode]
        );

        return redirect()->back()->with('success', 'Berhasil! H.A.N.A sekarang menggunakan Mode: ' . strtoupper($newMode));
    }
}