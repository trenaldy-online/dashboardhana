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
        
        // JURUS PAKSA SIMPAN (Mengabaikan aturan $fillable)
        $setting = Setting::where('key', 'form_mode')->first();
        
        // Jika pengaturan belum ada sama sekali di database, buat baru
        if (!$setting) {
            $setting = new Setting();
            $setting->key = 'form_mode';
        }
        
        // Timpa nilainya dengan yang baru dari tombol
        $setting->value = $newMode;
        
        // Paksa simpan ke database detik ini juga!
        $setting->save();

        return redirect()->back()->with('success', 'Berhasil! H.A.N.A sekarang menggunakan Mode: ' . strtoupper($newMode));
    }
}