<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel AHCC - Pengaturan AI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; }
        .material-symbols-outlined { font-variation-settings: 'FILL' 1, 'wght' 600, 'GRAD' 0, 'opsz' 24; }
        
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
                <a href="/dashboard" class="text-slate-500 hover:text-teal-800 pb-1 px-2 transition-colors">Data Pasien</a>
                <a href="/analytics" class="text-slate-500 hover:text-teal-800 pb-1 px-2 transition-colors">Analitik AI</a>
                <a href="/marketing" class="text-slate-500 hover:text-teal-800 pb-1 px-2 transition-colors">Marketing AI</a>
                <a href="{{ route('admin.settings') }}" class="text-teal-800 border-b-2 border-teal-600 pb-1 px-2 font-bold">Pengaturan AI</a>
            </div>

            <div class="flex items-center gap-4 pr-2">
                <div class="text-right hidden sm:block leading-tight">
                    <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest">Selamat Datang</p>
                    <p class="text-sm font-bold text-slate-800">Halo, {{ Auth::user()->name ?? 'Admin AHCC' }}</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-slate-200 border-2 border-white shadow-sm overflow-hidden flex items-center justify-center">
                    <img src="https://ui-avatars.com/api/?name=Admin&background=0D8ABC&color=fff" alt="Avatar">
                </div>
                <form action="{{ route('logout') ?? '#' }}" method="POST">
                    @csrf
                    <button class="bg-rose-100/80 hover:bg-rose-200 text-rose-600 px-4 py-2 rounded-full text-sm font-bold transition-colors backdrop-blur-sm border border-rose-200/50">
                        logout
                    </button>
                </form>
            </div>
        </nav>

        <div class="mt-2 px-2">
            <h2 class="text-3xl font-black text-slate-800 tracking-tight">Pengaturan H.A.N.A</h2>
            <p class="text-slate-600 font-medium mt-1">Pilih strategi pengumpulan data (Leads) pasien saat memulai skrining.</p>
        </div>

        @if(session('success'))
            <div class="bg-emerald-100/80 backdrop-blur-md text-emerald-800 px-6 py-4 rounded-2xl font-bold border border-emerald-200/50 flex items-center gap-3 shadow-sm mx-2 animate-pulse">
                <span class="material-symbols-outlined">check_circle</span> 
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white/40 backdrop-blur-xl border border-white/60 rounded-[2rem] p-6 sm:p-8 shadow-[0_8px_32px_0_rgba(31,38,135,0.05)] mx-2">
            <form action="{{ route('admin.settings.toggle') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <label class="relative flex flex-col p-6 rounded-3xl cursor-pointer transition-all duration-300 {{ $mode === 'relaxed' ? 'bg-white/70 border-2 border-teal-500 shadow-lg shadow-teal-900/5 ring-4 ring-teal-500/10' : 'bg-white/30 border-2 border-white/50 hover:bg-white/50 hover:border-teal-200' }}">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-full {{ $mode === 'relaxed' ? 'bg-teal-100 text-teal-600' : 'bg-slate-100/50 text-slate-500' }} flex items-center justify-center transition-colors">
                                    <span class="material-symbols-outlined text-2xl">forum</span>
                                </div>
                                <div>
                                    <p class="font-bold text-slate-800 text-lg">Mode Santai</p>
                                    <p class="text-xs text-slate-500 font-medium tracking-wide">(Minta Data di Akhir)</p>
                                </div>
                            </div>
                            <input type="radio" name="mode" value="relaxed" class="w-6 h-6 accent-teal-600" {{ $mode === 'relaxed' ? 'checked' : '' }} onchange="this.form.submit()">
                        </div>
                        <p class="text-sm text-slate-600 font-medium leading-relaxed mb-6">
                            Pasien bisa langsung mengobrol dengan AI. Nomor WhatsApp dan Email baru ditagih di tengah jalan sebelum mereka bisa melihat hasil akhir.
                        </p>
                        <div class="mt-auto inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-amber-100/80 text-amber-700 w-fit backdrop-blur-sm border border-amber-200/50">
                            <span class="material-symbols-outlined text-[16px]">campaign</span> Cocok untuk promosi & cari Leads
                        </div>
                    </label>

                    <label class="relative flex flex-col p-6 rounded-3xl cursor-pointer transition-all duration-300 {{ $mode === 'strict' ? 'bg-white/70 border-2 border-teal-500 shadow-lg shadow-teal-900/5 ring-4 ring-teal-500/10' : 'bg-white/30 border-2 border-white/50 hover:bg-white/50 hover:border-teal-200' }}">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-full {{ $mode === 'strict' ? 'bg-teal-100 text-teal-600' : 'bg-slate-100/50 text-slate-500' }} flex items-center justify-center transition-colors">
                                    <span class="material-symbols-outlined text-2xl">shield_lock</span>
                                </div>
                                <div>
                                    <p class="font-bold text-slate-800 text-lg">Mode Ketat</p>
                                    <p class="text-xs text-slate-500 font-medium tracking-wide">(Minta Data di Awal)</p>
                                </div>
                            </div>
                            <input type="radio" name="mode" value="strict" class="w-6 h-6 accent-teal-600" {{ $mode === 'strict' ? 'checked' : '' }} onchange="this.form.submit()">
                        </div>
                        <p class="text-sm text-slate-600 font-medium leading-relaxed mb-6">
                            Pasien wajib mengisi formulir lengkap (termasuk WhatsApp & Email) di halaman depan sebelum bisa mengakses fitur Chat AI sama sekali.
                        </p>
                        <div class="mt-auto inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-sky-100/80 text-sky-700 w-fit backdrop-blur-sm border border-sky-200/50">
                            <span class="material-symbols-outlined text-[16px]">security</span> Hemat kuota & anti-spam
                        </div>
                    </label>

                </div>
            </form>
        </div>

        <p class="text-center text-xs font-semibold text-slate-500 pb-8 mt-4 opacity-70">
            &copy; 2026 AHCC Healthcare Intelligence. All rights reserved.
        </p>

    </div>

</body>
</html>