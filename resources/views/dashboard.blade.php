<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel AHCC - Data Screening</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />
    
    <style>
        /* Mengatur default font ke sans-serif modern dan memuluskan ikon */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; }
        .material-symbols-outlined { font-variation-settings: 'FILL' 1, 'wght' 600, 'GRAD' 0, 'opsz' 24; }
        
        /* Custom scrollbar untuk tabel */
        ::-webkit-scrollbar { height: 8px; width: 8px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.4); border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(255, 255, 255, 0.6); }
    </style>
</head>
<body class="text-slate-800 antialiased min-h-screen relative overflow-x-hidden selection:bg-teal-500 selection:text-white">

    <div class="fixed inset-0 z-[-1] bg-[#e6f4f1] overflow-hidden">
        <div class="absolute -top-[10%] -left-[10%] w-[60%] h-[60%] rounded-full bg-indigo-300/40 mix-blend-multiply filter blur-[120px]"></div>
        <div class="absolute top-[20%] -right-[10%] w-[50%] h-[50%] rounded-full bg-emerald-300/40 mix-blend-multiply filter blur-[120px]"></div>
        <div class="absolute -bottom-[20%] left-[20%] w-[60%] h-[60%] rounded-full bg-sky-300/40 mix-blend-multiply filter blur-[120px]"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex flex-col gap-6">

        <nav class="bg-white/40 backdrop-blur-xl border border-white/50 rounded-full px-4 py-2 flex justify-between items-center shadow-[0_8px_32px_0_rgba(31,38,135,0.05)]">
            <div class="flex items-center gap-3 pl-2">
                <div class="bg-[#388e3c] text-white p-1.5 rounded-full flex items-center justify-center">
                    <span class="material-symbols-outlined text-lg">health_metrics</span>
                </div>
                <h1 class="text-xl font-extrabold tracking-tight text-slate-800">Admin Panel <span class="text-teal-700">AHCC</span></h1>
            </div>

            <div class="hidden md:flex items-center gap-8 font-semibold text-sm">
                <a href="/dashboard" class="text-teal-800 border-b-2 border-teal-600 pb-1 px-2 font-bold">Data Pasien</a>
                <a href="/analytics" class="text-slate-500 hover:text-teal-800 pb-1 px-2 transition-colors">Analitik AI</a>
                <a href="/marketing" class="text-slate-500 hover:text-teal-800 pb-1 px-2 transition-colors">Marketing AI</a>
            </div>

            <div class="flex items-center gap-4 pr-2">
                <div class="text-right hidden sm:block leading-tight">
                    <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest">Selamat Datang</p>
                    <p class="text-sm font-bold text-slate-800">Halo, {{ Auth::user()->name ?? 'Admin AHCC' }}</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-slate-200 border-2 border-white shadow-sm overflow-hidden flex items-center justify-center">
                    <img src="https://ui-avatars.com/api/?name=Admin&background=0D8ABC&color=fff" alt="Avatar">
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button class="bg-rose-100/80 hover:bg-rose-200 text-rose-600 px-4 py-2 rounded-full text-sm font-bold transition-colors backdrop-blur-sm border border-rose-200/50">
                        logout
                    </button>
                </form>
            </div>
        </nav>

        <div class="mt-2 px-2">
            <h2 class="text-3xl font-black text-slate-800 tracking-tight">Data Screening Pasien</h2>
            <p class="text-slate-600 font-medium mt-1">Mengelola dan memantau hasil screening kesehatan pasien secara real-time.</p>
        </div>

        <form action="/dashboard" method="GET" class="bg-white/40 backdrop-blur-xl border border-white/50 rounded-full p-2 flex flex-col md:flex-row items-center gap-2 shadow-[0_8px_32px_0_rgba(31,38,135,0.05)]">
            
            <div class="flex-1 w-full bg-white/50 border border-white/60 rounded-full px-4 py-2.5 flex items-center gap-2 transition-all focus-within:bg-white/80 focus-within:ring-2 focus-within:ring-teal-500/50">
                <span class="material-symbols-outlined text-slate-400">search</span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama Pasien atau WhatsApp..." class="bg-transparent border-none outline-none w-full text-sm font-medium text-slate-700 placeholder-slate-400">
            </div>

            <div class="w-full md:w-48 bg-white/50 border border-white/60 rounded-full px-4 py-2.5 flex items-center gap-2 hover:bg-white/70 transition-colors">
                <select name="risk" class="bg-transparent border-none outline-none w-full text-sm font-medium text-slate-700 cursor-pointer appearance-none">
                    <option value="">Semua Risiko</option>
                    <option value="Tinggi" {{ request('risk') == 'Tinggi' ? 'selected' : '' }}>Risiko Tinggi</option>
                    <option value="Sedang" {{ request('risk') == 'Sedang' ? 'selected' : '' }}>Risiko Sedang</option>
                    <option value="Rendah" {{ request('risk') == 'Rendah' ? 'selected' : '' }}>Risiko Rendah</option>
                </select>
                <span class="material-symbols-outlined text-slate-500 pointer-events-none text-sm">expand_more</span>
            </div>

            <div class="w-full md:w-auto bg-white/50 border border-white/60 rounded-full px-4 py-2.5 flex items-center gap-2 text-sm font-medium text-slate-700">
                <span class="material-symbols-outlined text-slate-400 text-lg">calendar_month</span>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="bg-transparent border-none outline-none text-slate-600 text-xs sm:text-sm">
                <span class="text-slate-400">-</span>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="bg-transparent border-none outline-none text-slate-600 text-xs sm:text-sm">
            </div>

            <div class="flex gap-2 w-full md:w-auto">
                @if(request()->hasAny(['search', 'risk', 'start_date', 'end_date']) && (request('search') != '' || request('risk') != '' || request('start_date') != '' || request('end_date') != ''))
                    <a href="/dashboard" class="bg-slate-100/50 hover:bg-slate-200 text-slate-600 p-2.5 rounded-full flex items-center justify-center transition-colors backdrop-blur-sm border border-white/50" title="Reset Filter">
                        <span class="material-symbols-outlined text-lg">close</span>
                    </a>
                @endif
                <button type="submit" class="bg-[#388e3c] hover:bg-green-700 text-white rounded-full px-6 py-2.5 text-sm font-bold flex items-center justify-center gap-1.5 shadow-md shadow-green-900/20 transition-all w-full md:w-auto">
                    <span class="material-symbols-outlined text-sm">filter_list</span> Terapkan
                </button>
            </div>
        </form>

        <div class="bg-white/40 backdrop-blur-xl border border-white/60 rounded-[2rem] p-6 shadow-[0_8px_32px_0_rgba(31,38,135,0.05)] overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-white/50">
                            <th class="pb-4 pl-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Tanggal Masuk</th>
                            <th class="pb-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Nama Pasien</th>
                            <th class="pb-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Usia / Kelamin</th>
                            <th class="pb-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Tingkat Risiko</th>
                            <th class="pb-4 pr-4 text-xs font-bold text-slate-500 uppercase tracking-widest text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/40">
                        @forelse($reports as $report)
                        <tr class="hover:bg-white/30 transition-colors group">
                            <td class="py-4 pl-4 text-sm font-medium text-slate-600">
                                {{ $report->created_at->format('d M Y, H:i') }}
                            </td>
                            <td class="py-4">
                                <p class="font-bold text-slate-800 text-base">{{ $report->user_data['name'] ?? 'Tidak diketahui' }}</p>
                                @php
                                    $wa = $report->user_data['whatsapp'] ?? '';
                                    if(str_starts_with($wa, '0')) { $wa = '+62 ' . substr($wa, 1); }
                                @endphp
                                <p class="text-xs text-slate-500 font-medium">{{ $wa }}</p>
                            </td>
                            <td class="py-4 text-sm font-medium text-slate-600">
                                {{ $report->user_data['age'] ?? '-' }} thn / {{ $report->user_data['gender'] ?? '-' }}
                            </td>
                            <td class="py-4">
                                @php
                                    $risk = $report->report_data['risk_level'] ?? 'Tidak diketahui';
                                    if($risk == 'Tinggi') {
                                        $bg = 'bg-rose-100/80'; $text = 'text-rose-600'; $dot = 'bg-rose-500';
                                    } elseif($risk == 'Sedang') {
                                        $bg = 'bg-amber-100/80'; $text = 'text-amber-600'; $dot = 'bg-amber-500';
                                    } else {
                                        $bg = 'bg-emerald-100/80'; $text = 'text-emerald-600'; $dot = 'bg-emerald-500';
                                    }
                                @endphp
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold {{ $bg }} {{ $text }} backdrop-blur-sm border border-white/50">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $dot }}"></span>
                                    Risiko {{ $risk }}
                                </span>
                            </td>
                            <td class="py-4 pr-4 flex items-center justify-center gap-2 opacity-70 group-hover:opacity-100 transition-opacity">
                                <a href="/dashboard/{{ $report->id }}" class="w-8 h-8 flex items-center justify-center bg-white/50 hover:bg-sky-500 hover:text-white text-sky-600 rounded-full shadow-sm backdrop-blur-sm transition-all border border-white/60" title="Lihat Detail Pasien">
                                    <span class="material-symbols-outlined text-sm">visibility</span>
                                </a>
                                <a href="http://localhost:3000/report/{{ $report->id }}" target="_blank" class="w-8 h-8 flex items-center justify-center bg-white/50 hover:bg-slate-800 hover:text-white text-slate-700 rounded-full shadow-sm backdrop-blur-sm transition-all border border-white/60" title="Buka PDF">
                                    <span class="material-symbols-outlined text-sm">picture_as_pdf</span>
                                </a>
                                @php
                                    $cleanWa = $report->user_data['whatsapp'] ?? '';
                                    if(str_starts_with($cleanWa, '0')) { $cleanWa = '62' . substr($cleanWa, 1); }
                                @endphp
                                <a href="https://wa.me/{{ $cleanWa }}" target="_blank" class="w-8 h-8 flex items-center justify-center bg-white/50 hover:bg-emerald-500 hover:text-white text-emerald-600 rounded-full shadow-sm backdrop-blur-sm transition-all border border-white/60" title="Hubungi via WA">
                                    <span class="material-symbols-outlined text-sm">chat</span>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-500 font-medium">
                                Belum ada data laporan pasien yang sesuai kriteria.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($reports->hasPages())
            <div class="mt-6 flex items-center justify-between border-t border-white/50 pt-4">
                <p class="text-xs text-slate-500 font-medium">Menampilkan {{ $reports->count() }} dari {{ $reports->total() }} pasien</p>
                <div class="flex gap-1">
                    {{ $reports->links('pagination::tailwind') }}
                </div>
            </div>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            
            @php
                // Menghitung statistik halaman saat ini untuk tampilan visual
                $countTinggi = $reports->where('report_data.risk_level', 'Tinggi')->count();
                $countSedang = $reports->where('report_data.risk_level', 'Sedang')->count();
                $countRendah = $reports->where('report_data.risk_level', 'Rendah')->count();
            @endphp

            <div class="bg-white/40 backdrop-blur-xl border-2 border-rose-200/50 rounded-3xl p-6 shadow-[0_8px_32px_0_rgba(31,38,135,0.05)] flex justify-between items-center">
                <div>
                    <p class="text-xs font-extrabold text-slate-500 uppercase tracking-widest mb-1">Total Risiko Tinggi</p>
                    <p class="text-4xl font-black text-slate-800">{{ $countTinggi }}</p>
                </div>
                <div class="bg-rose-100/80 text-rose-500 p-3 rounded-2xl backdrop-blur-sm">
                    <span class="material-symbols-outlined text-3xl">priority_high</span>
                </div>
            </div>

            <div class="bg-white/40 backdrop-blur-xl border-2 border-amber-200/50 rounded-3xl p-6 shadow-[0_8px_32px_0_rgba(31,38,135,0.05)] flex justify-between items-center">
                <div>
                    <p class="text-xs font-extrabold text-slate-500 uppercase tracking-widest mb-1">Total Risiko Sedang</p>
                    <p class="text-4xl font-black text-slate-800">{{ $countSedang }}</p>
                </div>
                <div class="bg-amber-100/80 text-amber-500 p-3 rounded-2xl backdrop-blur-sm">
                    <span class="material-symbols-outlined text-3xl">warning</span>
                </div>
            </div>

            <div class="bg-white/40 backdrop-blur-xl border-2 border-emerald-200/50 rounded-3xl p-6 shadow-[0_8px_32px_0_rgba(31,38,135,0.05)] flex justify-between items-center">
                <div>
                    <p class="text-xs font-extrabold text-slate-500 uppercase tracking-widest mb-1">Total Risiko Rendah</p>
                    <p class="text-4xl font-black text-slate-800">{{ $countRendah }}</p>
                </div>
                <div class="bg-emerald-100/80 text-emerald-500 p-3 rounded-2xl backdrop-blur-sm">
                    <span class="material-symbols-outlined text-3xl">check_circle</span>
                </div>
            </div>

        </div>

        <p class="text-center text-xs font-semibold text-slate-500 pb-8 opacity-70">
            &copy; 2026 AHCC Healthcare Intelligence. All rights reserved.
        </p>

    </div>

</body>
</html>