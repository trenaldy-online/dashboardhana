<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Analytics Dashboard - AHCC</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        /* Mengatur default font ke sans-serif modern dan memuluskan ikon */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; }
        .material-symbols-outlined { font-variation-settings: 'FILL' 1, 'wght' 600, 'GRAD' 0, 'opsz' 24; }
        
        /* Custom scrollbar */
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
                <a href="/analytics" class="text-teal-800 border-b-2 border-teal-600 pb-1 px-2 font-bold">Analitik AI</a>
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

        <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 mt-2 px-2">
            <div>
                <h2 class="text-3xl font-black text-slate-800 tracking-tight">Laporan Kinerja AI</h2>
                <p class="text-slate-600 font-medium mt-1">Pantau efisiensi token, tren diagnosa, dan interaksi pasien.</p>
            </div>

            <form action="/analytics" method="GET" class="bg-white/40 backdrop-blur-xl border border-white/50 rounded-full p-2 flex items-center gap-2 shadow-[0_8px_32px_0_rgba(31,38,135,0.05)] w-full md:w-auto">
                <div class="w-full md:w-auto bg-white/50 border border-white/60 rounded-full px-4 py-2.5 flex items-center gap-2 text-sm font-medium text-slate-700">
                    <span class="material-symbols-outlined text-slate-400 text-lg">calendar_month</span>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="bg-transparent border-none outline-none text-slate-600 text-xs sm:text-sm">
                    <span class="text-slate-400">-</span>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="bg-transparent border-none outline-none text-slate-600 text-xs sm:text-sm">
                </div>
                
                <div class="flex gap-2 shrink-0">
                    @if(request()->hasAny(['start_date', 'end_date']) && (request('start_date') != '' || request('end_date') != ''))
                        <a href="/analytics" class="bg-slate-100/50 hover:bg-slate-200 text-slate-600 p-2.5 rounded-full flex items-center justify-center transition-colors backdrop-blur-sm border border-white/50" title="Reset Filter">
                            <span class="material-symbols-outlined text-lg">close</span>
                        </a>
                    @endif
                    <button type="submit" class="bg-[#388e3c] hover:bg-green-700 text-white rounded-full px-5 py-2.5 text-sm font-bold flex items-center justify-center gap-1.5 shadow-md shadow-green-900/20 transition-all">
                        <span class="material-symbols-outlined text-sm">filter_list</span> Terapkan
                    </button>
                </div>
            </form>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 mt-2">
            <div class="bg-white/40 backdrop-blur-xl border-2 border-emerald-200/50 rounded-[2rem] p-6 shadow-[0_8px_32px_0_rgba(31,38,135,0.05)] flex flex-col justify-between relative overflow-hidden group">
                <div class="absolute -right-4 -top-4 opacity-10 transform group-hover:scale-110 transition-transform duration-500">
                    <span class="material-symbols-outlined text-[100px] text-emerald-600">verified_user</span>
                </div>
                <div class="flex items-center gap-2 text-emerald-600 mb-4 relative z-10">
                    <span class="material-symbols-outlined">verified_user</span>
                    <p class="text-xs font-extrabold uppercase tracking-widest">Pasien Valid</p>
                </div>
                <p class="text-4xl font-black text-slate-800 relative z-10">{{ $totalValid }}</p>
            </div>

            <div class="bg-white/40 backdrop-blur-xl border-2 border-rose-200/50 rounded-[2rem] p-6 shadow-[0_8px_32px_0_rgba(31,38,135,0.05)] flex flex-col justify-between relative overflow-hidden group">
                <div class="absolute -right-4 -top-4 opacity-10 transform group-hover:scale-110 transition-transform duration-500">
                    <span class="material-symbols-outlined text-[100px] text-rose-600">gpp_bad</span>
                </div>
                <div class="flex items-center gap-2 text-rose-600 mb-4 relative z-10">
                    <span class="material-symbols-outlined">gpp_bad</span>
                    <p class="text-xs font-extrabold uppercase tracking-widest">Chat Iseng / Invalid</p>
                </div>
                <p class="text-4xl font-black text-slate-800 relative z-10">{{ $totalInvalid }}</p>
            </div>

            <div class="bg-white/40 backdrop-blur-xl border-2 border-sky-200/50 rounded-[2rem] p-6 shadow-[0_8px_32px_0_rgba(31,38,135,0.05)] flex flex-col justify-between relative overflow-hidden group">
                <div class="absolute -right-4 -top-4 opacity-10 transform group-hover:scale-110 transition-transform duration-500">
                    <span class="material-symbols-outlined text-[100px] text-sky-600">memory</span>
                </div>
                <div class="flex items-center gap-2 text-sky-600 mb-4 relative z-10">
                    <span class="material-symbols-outlined">memory</span>
                    <p class="text-xs font-extrabold uppercase tracking-widest">Total Token</p>
                </div>
                <p class="text-4xl font-black text-slate-800 relative z-10">{{ number_format($totalTokens) }}</p>
            </div>

            <div class="bg-white/40 backdrop-blur-xl border-2 border-amber-200/50 rounded-[2rem] p-6 shadow-[0_8px_32px_0_rgba(31,38,135,0.05)] flex flex-col justify-between relative overflow-hidden group">
                <div class="absolute -right-4 -top-4 opacity-10 transform group-hover:scale-110 transition-transform duration-500">
                    <span class="material-symbols-outlined text-[100px] text-amber-600">account_balance_wallet</span>
                </div>
                <div class="flex items-center gap-2 text-amber-600 mb-4 relative z-10">
                    <span class="material-symbols-outlined">account_balance_wallet</span>
                    <p class="text-xs font-extrabold uppercase tracking-widest">Estimasi Biaya</p>
                </div>
                <p class="text-3xl font-black text-slate-800 relative z-10">Rp {{ number_format($estimasiBiayaRp, 0, ',', '.') }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            
            <div class="lg:col-span-2 flex flex-col gap-6">
                
                <div class="bg-white/40 backdrop-blur-xl border border-white/60 rounded-[2rem] p-6 shadow-[0_8px_32px_0_rgba(31,38,135,0.05)]">
                    <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-2">
                        <span class="material-symbols-outlined text-teal-600">monitoring</span>
                        Tren Penggunaan Token AI
                    </h3>
                    <div class="relative w-full h-[300px]">
                        <canvas id="tokenChart"></canvas>
                    </div>
                </div>

                <div class="bg-white/40 backdrop-blur-xl border border-white/60 rounded-[2rem] p-6 shadow-[0_8px_32px_0_rgba(31,38,135,0.05)]">
                    <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-2">
                        <span class="material-symbols-outlined text-amber-500">payments</span>
                        Tren Estimasi Biaya (Rupiah)
                    </h3>
                    <div class="relative w-full h-[250px]">
                        <canvas id="costChart"></canvas>
                    </div>
                </div>

            </div>

            <div class="flex flex-col gap-6">
                
                <div class="bg-white/40 backdrop-blur-xl border border-white/60 rounded-[2rem] p-6 shadow-[0_8px_32px_0_rgba(31,38,135,0.05)]">
                    <h3 class="text-lg font-bold text-slate-800 mb-4">Distribusi Risiko</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center bg-white/50 p-3 rounded-2xl border border-white/60 backdrop-blur-sm">
                            <span class="text-sm font-bold text-rose-600 flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-rose-500"></span> Tinggi</span>
                            <span class="font-black text-rose-700 bg-rose-100/80 px-3 py-1 rounded-xl">{{ $riskHigh }}</span>
                        </div>
                        <div class="flex justify-between items-center bg-white/50 p-3 rounded-2xl border border-white/60 backdrop-blur-sm">
                            <span class="text-sm font-bold text-amber-600 flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-amber-500"></span> Sedang</span>
                            <span class="font-black text-amber-700 bg-amber-100/80 px-3 py-1 rounded-xl">{{ $riskMedium }}</span>
                        </div>
                        <div class="flex justify-between items-center bg-white/50 p-3 rounded-2xl border border-white/60 backdrop-blur-sm">
                            <span class="text-sm font-bold text-emerald-600 flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> Rendah</span>
                            <span class="font-black text-emerald-700 bg-emerald-100/80 px-3 py-1 rounded-xl">{{ $riskLow }}</span>
                        </div>
                    </div>
                </div>


                <div class="bg-white/40 backdrop-blur-xl border border-white/60 rounded-[2rem] p-6 shadow-[0_8px_32px_0_rgba(31,38,135,0.05)] flex flex-col">
                    
                    <div class="flex justify-between items-center mb-4 border-b border-white/50 pb-2">
                        <h3 class="text-lg font-bold text-slate-800">Top Suspect Klinis</h3>
                        <button onclick="openModal()" class="bg-teal-100/80 hover:bg-teal-200 text-teal-700 px-3 py-1.5 rounded-full text-xs font-bold transition-all flex items-center gap-1 shadow-sm border border-teal-200/50">
                            Detail <span class="material-symbols-outlined text-[14px]">open_in_new</span>
                        </button>
                    </div>
                    
                    <div class="overflow-y-auto pr-2 max-h-[380px]">
                        <ul class="space-y-3 mt-1">
                            {{-- Hanya tampilkan 5 teratas di Dashboard luar --}}
                            @forelse(array_slice($allSuspects, 0, 5, true) as $suspectName => $data)
                                <li class="flex justify-between items-center group bg-white/30 hover:bg-white/60 p-3 rounded-2xl border border-white/50 backdrop-blur-sm transition-all shadow-[0_2px_10px_0_rgba(0,0,0,0.02)]">
                                    <div class="flex flex-col gap-1 pr-4">
                                        <span class="text-[9px] font-black text-teal-700 uppercase tracking-widest bg-teal-100/60 self-start px-2 py-0.5 rounded border border-teal-200/50">
                                            {{ $data['dept'] }}
                                        </span>
                                        <span class="text-sm font-medium text-slate-700 leading-tight group-hover:text-teal-900 transition-colors">
                                            {{ $suspectName }}
                                        </span>
                                    </div>
                                    <span class="bg-white/80 text-slate-800 border border-white text-xs font-extrabold px-3 py-1.5 rounded-xl shrink-0 shadow-sm">
                                        {{ $data['count'] }}x
                                    </span>
                                </li>
                            @empty
                                <div class="h-full min-h-[150px] flex flex-col items-center justify-center text-slate-400">
                                    <span class="material-symbols-outlined text-4xl mb-2 opacity-50">data_alert</span>
                                    <p class="text-sm font-medium">Belum ada data diagnosa.</p>
                                </div>
                            @endforelse
                        </ul>
                    </div>
                </div>

            </div>
        </div>

        <p class="text-center text-xs font-semibold text-slate-500 pb-8 opacity-70">
            &copy; 2026 AHCC Healthcare Intelligence. All rights reserved.
        </p>

    </div>

    <div id="suspectModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6 opacity-0 pointer-events-none transition-opacity duration-300">
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="closeModal()"></div>
        
        <div class="relative bg-white/80 backdrop-blur-2xl border border-white/60 rounded-[2rem] shadow-2xl w-full max-w-4xl max-h-[90vh] flex flex-col overflow-hidden transform scale-95 transition-transform duration-300" id="modalContent">
            
            <div class="p-6 border-b border-white/50 flex justify-between items-center bg-white/40">
                <div class="flex items-center gap-3">
                    <div class="bg-teal-100 text-teal-600 p-2 rounded-xl"><span class="material-symbols-outlined">analytics</span></div>
                    <h3 class="text-xl font-black text-slate-800 tracking-tight">Detail Data Suspek & Poliklinik</h3>
                </div>
                <button onclick="closeModal()" class="w-10 h-10 flex items-center justify-center bg-white/50 hover:bg-rose-100 text-slate-500 hover:text-rose-600 rounded-full transition-colors border border-white/60 shadow-sm">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <div class="p-6 overflow-y-auto grid grid-cols-1 md:grid-cols-2 gap-8 bg-slate-50/30">
                
                <div>
                    <h4 class="font-bold text-slate-700 mb-4 flex items-center gap-2 border-b border-slate-200/50 pb-2">
                        <span class="material-symbols-outlined text-amber-500">domain</span> Rekapitulasi Rujukan Poliklinik
                    </h4>
                    <ul class="space-y-3">
                        @forelse($departmentStats as $dept => $count)
                            <li class="flex justify-between items-center bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
                                <span class="font-bold text-slate-700">{{ $dept }}</span>
                                <span class="bg-amber-100 text-amber-700 text-sm font-black px-4 py-1.5 rounded-xl">{{ $count }} Kasus</span>
                            </li>
                        @empty
                            <p class="text-sm text-slate-500 text-center italic">Tidak ada data.</p>
                        @endforelse
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold text-slate-700 mb-4 flex items-center gap-2 border-b border-slate-200/50 pb-2">
                        <span class="material-symbols-outlined text-teal-600">list_alt</span> Daftar Lengkap Seluruh Suspek AI
                    </h4>
                    <ul class="space-y-3">
                        @forelse($allSuspects as $suspectName => $data)
                            <li class="flex justify-between items-center bg-white p-3 rounded-2xl border border-slate-100 shadow-sm">
                                <div class="flex flex-col pr-4">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $data['dept'] }}</span>
                                    <span class="text-sm font-semibold text-slate-800">{{ $suspectName }}</span>
                                </div>
                                <span class="bg-slate-100 text-slate-600 text-xs font-bold px-3 py-1.5 rounded-lg shrink-0">{{ $data['count'] }}x</span>
                            </li>
                        @empty
                            <p class="text-sm text-slate-500 text-center italic">Tidak ada data.</p>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openModal() {
            const modal = document.getElementById('suspectModal');
            const content = document.getElementById('modalContent');
            modal.classList.remove('opacity-0', 'pointer-events-none');
            content.classList.remove('scale-95');
            content.classList.add('scale-100');
        }

        function closeModal() {
            const modal = document.getElementById('suspectModal');
            const content = document.getElementById('modalContent');
            modal.classList.add('opacity-0', 'pointer-events-none');
            content.classList.remove('scale-100');
            content.classList.add('scale-95');
        }
    </script>
    
    <script>
        // ================= GRAFIK BATANG (TOKEN DIGUNAKAN) =================
        const ctx = document.getElementById('tokenChart').getContext('2d');
        
        // Buat gradien untuk batang chart agar lebih estetik
        let gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(13, 148, 136, 0.9)');   // Teal atas
        gradient.addColorStop(1, 'rgba(13, 148, 136, 0.2)');   // Transparan bawah

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($chartDates) !!},
                datasets: [{
                    label: 'Token Digunakan',
                    data: {!! json_encode($chartTokens) !!},
                    backgroundColor: gradient,
                    hoverBackgroundColor: 'rgba(13, 148, 136, 1)',
                    borderRadius: 8,
                    borderWidth: 1,
                    borderColor: 'rgba(255, 255, 255, 0.5)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false, // Aman karena sudah dibungkus relative h-[300px]
                plugins: { 
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(255, 255, 255, 0.9)',
                        titleColor: '#1e293b',
                        bodyColor: '#0f766e',
                        borderColor: 'rgba(255,255,255,0.5)',
                        borderWidth: 1,
                        padding: 12,
                        cornerRadius: 12,
                        displayColors: false,
                        titleFont: { family: 'Inter', size: 13 },
                        bodyFont: { family: 'Inter', size: 14, weight: 'bold' }
                    }
                },
                scales: { 
                    y: { 
                        beginAtZero: true,
                        grid: { color: 'rgba(255, 255, 255, 0.4)', drawBorder: false },
                        ticks: { color: '#64748b', font: { family: 'Inter' } }
                    },
                    x: {
                        grid: { display: false, drawBorder: false },
                        ticks: { color: '#64748b', font: { family: 'Inter', weight: '500' } }
                    }
                },
                animation: {
                    duration: 1500,
                    easing: 'easeOutQuart'
                }
            }
        });

        // ================= GRAFIK GARIS (ESTIMASI BIAYA) =================
        const costCtx = document.getElementById('costChart').getContext('2d');
        
        // Buat efek gradien kuning/oranye di bawah garis
        let costGradient = costCtx.createLinearGradient(0, 0, 0, 300);
        costGradient.addColorStop(0, 'rgba(245, 158, 11, 0.4)'); // Amber transparan
        costGradient.addColorStop(1, 'rgba(245, 158, 11, 0.0)'); // Transparan penuh

        new Chart(costCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartDates) !!},
                datasets: [{
                    label: 'Estimasi Biaya (Rp)',
                    data: {!! json_encode($chartCosts) !!},
                    borderColor: '#f59e0b', // Amber-500
                    backgroundColor: costGradient,
                    borderWidth: 3,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#f59e0b',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    fill: true,
                    tension: 0.4 // Membuat garis melengkung mulus (smooth curve)
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { 
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(255, 255, 255, 0.9)',
                        titleColor: '#1e293b',
                        bodyColor: '#d97706',
                        borderColor: 'rgba(255,255,255,0.5)',
                        borderWidth: 1,
                        padding: 12,
                        cornerRadius: 12,
                        displayColors: false,
                        titleFont: { family: 'Inter', size: 13 },
                        bodyFont: { family: 'Inter', size: 14, weight: 'bold' },
                        callbacks: {
                            label: function(context) {
                                // PERBAIKAN: Batasi desimal maksimal 2 angka (misal Rp 14,95)
                                return 'Rp ' + context.parsed.y.toLocaleString('id-ID', { 
                                    minimumFractionDigits: 0, 
                                    maximumFractionDigits: 2 
                                });
                            }
                        }
                    }
                },
                scales: { 
                    y: { 
                        beginAtZero: true,
                        grid: { color: 'rgba(255, 255, 255, 0.4)', drawBorder: false },
                        ticks: { 
                            color: '#64748b', 
                            font: { family: 'Inter' },
                            callback: function(value) { 
                                // PERBAIKAN JUGA DI SUMBU Y: Batasi desimalnya
                                return 'Rp ' + value.toLocaleString('id-ID', { 
                                    maximumFractionDigits: 2 
                                }); 
                            }
                        }
                    },
                    x: {
                        grid: { display: false, drawBorder: false },
                        ticks: { color: '#64748b', font: { family: 'Inter', weight: '500' } }
                    }
                },
                animation: {
                    duration: 2000,
                    easing: 'easeOutQuart'
                }
            }
        });
    </script>
</body>
</html>