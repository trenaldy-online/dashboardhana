<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marketing Insights AI - AHCC</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; }
        .material-symbols-outlined { font-variation-settings: 'FILL' 1, 'wght' 600, 'GRAD' 0, 'opsz' 24; }
        
        /* Styling khusus untuk memoles HTML mentah dari AI */
        .ai-content h3 { font-size: 1.125rem; font-weight: 800; color: #0f766e; margin-top: 1.5rem; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem; }
        .ai-content h3::before { content: 'lightbulb'; font-family: 'Material Symbols Outlined'; font-size: 1.2rem; color: #f59e0b; }
        .ai-content p { color: #334155; line-height: 1.7; margin-bottom: 1rem; font-size: 0.95rem; }
        .ai-content ul { list-style-type: none; padding-left: 0.5rem; margin-bottom: 1.5rem; gap: 0.5rem; display: flex; flex-direction: column; }
        .ai-content li { color: #334155; line-height: 1.6; font-size: 0.95rem; position: relative; padding-left: 1.5rem; margin-bottom: 0.5rem; }
        .ai-content li::before { content: 'check_circle'; font-family: 'Material Symbols Outlined'; position: absolute; left: 0; top: 0.1rem; font-size: 1.1rem; color: #10b981; }
        .ai-content strong { color: #0f766e; font-weight: 700; }
        
        /* Auto scroll ke bawah untuk chat */
        .chat-container { scroll-behavior: smooth; }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar { height: 8px; width: 8px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.4); border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(255, 255, 255, 0.6); }
    </style>
</head>
<body class="text-slate-800 antialiased min-h-screen relative overflow-x-hidden selection:bg-teal-500 selection:text-white pb-6">

    <div class="fixed inset-0 z-[-1] bg-[#e6f4f1] overflow-hidden">
        <div class="absolute -top-[10%] -left-[10%] w-[60%] h-[60%] rounded-full bg-indigo-300/40 mix-blend-multiply filter blur-[120px]"></div>
        <div class="absolute top-[20%] -right-[10%] w-[50%] h-[50%] rounded-full bg-emerald-300/40 mix-blend-multiply filter blur-[120px]"></div>
        <div class="absolute -bottom-[20%] left-[20%] w-[60%] h-[60%] rounded-full bg-sky-300/40 mix-blend-multiply filter blur-[120px]"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex flex-col gap-6 h-[calc(100vh-3rem)]">

        <nav class="bg-white/40 backdrop-blur-xl border border-white/50 rounded-full px-4 py-2 flex justify-between items-center shadow-[0_8px_32px_0_rgba(31,38,135,0.05)] shrink-0">
            <div class="flex items-center gap-3 pl-2">
                <div class="bg-[#388e3c] text-white p-1.5 rounded-full flex items-center justify-center">
                    <span class="material-symbols-outlined text-lg">health_metrics</span>
                </div>
                <h1 class="text-xl font-extrabold tracking-tight text-slate-800">Admin Panel <span class="text-teal-700">AHCC</span></h1>
            </div>
            
            <div class="hidden md:flex items-center gap-8 font-semibold text-sm">
                <a href="/dashboard" class="text-slate-500 hover:text-teal-800 pb-1 px-2 transition-colors">Data Pasien</a>
                <a href="/analytics" class="text-slate-500 hover:text-teal-800 pb-1 px-2 transition-colors">Analitik AI</a>
                <a href="/marketing" class="text-teal-800 border-b-2 border-teal-600 pb-1 px-2 font-bold">Marketing AI</a>
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
                    <button class="bg-rose-100/80 hover:bg-rose-200 text-rose-600 px-4 py-2 rounded-full text-sm font-bold transition-colors backdrop-blur-sm border border-rose-200/50">logout</button>
                </form>
            </div>
        </nav>

        @if(empty($history))
            <div class="flex flex-col items-center justify-center flex-1">
                <div class="bg-white/40 backdrop-blur-2xl border border-white/60 rounded-[2rem] p-10 shadow-[0_8px_32px_0_rgba(31,38,135,0.05)] max-w-2xl w-full text-center">
                    <span class="material-symbols-outlined text-[80px] text-fuchsia-600 mb-4 animate-bounce">campaign</span>
                    <h2 class="text-3xl font-black text-slate-800 mb-2">Konsultan Marketing AI</h2>
                    <p class="text-slate-600 font-medium mb-8">Pilih rentang waktu untuk menyuruh AI membaca keluhan pasien dan merumuskan strategi kampanye rumah sakit.</p>
                    
                    <form action="/marketing/generate" method="POST" class="flex flex-col gap-6 bg-white/50 p-6 rounded-[2rem] border border-white/60">
                        @csrf
                        <div class="flex flex-col sm:flex-row gap-4 items-center justify-center">
                            <div class="w-full text-left">
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-2 ml-2">Dari Tanggal (Opsional)</label>
                                <input type="date" name="start_date" class="w-full bg-white/80 border border-white/60 rounded-2xl px-4 py-3 text-slate-700 outline-none focus:ring-2 focus:ring-fuchsia-500 transition-all shadow-sm">
                            </div>
                            <div class="w-full text-left">
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-2 ml-2">Sampai Tanggal (Opsional)</label>
                                <input type="date" name="end_date" class="w-full bg-white/80 border border-white/60 rounded-2xl px-4 py-3 text-slate-700 outline-none focus:ring-2 focus:ring-fuchsia-500 transition-all shadow-sm">
                            </div>
                        </div>
                        <button type="submit" class="w-full bg-gradient-to-r from-fuchsia-600 to-indigo-600 hover:from-fuchsia-700 hover:to-indigo-700 text-white rounded-2xl px-6 py-4 text-base font-bold flex items-center justify-center gap-2 shadow-lg shadow-fuchsia-900/20 transition-all transform hover:scale-[1.02]">
                            <span class="material-symbols-outlined text-xl">auto_awesome</span> Analisis Database Sekarang
                        </button>
                    </form>
                </div>
            </div>

        @else
            <div class="flex justify-between items-end px-2 shrink-0 mt-2">
                <div>
                    <h2 class="text-2xl font-black text-slate-800 flex items-center gap-2">
                        <span class="material-symbols-outlined text-4xl text-fuchsia-600">campaign</span>
                        Ruang Diskusi Marketing
                    </h2>
                    <div class="flex items-center gap-2 mt-2">
                        <span class="bg-teal-100 text-teal-800 text-xs font-bold px-3 py-1 rounded-lg border border-teal-200">
                            Data Pasien: {{ $activeDates['start'] ? \Carbon\Carbon::parse($activeDates['start'])->format('d M Y') : 'Awal' }} s/d {{ $activeDates['end'] ? \Carbon\Carbon::parse($activeDates['end'])->format('d M Y') : 'Terakhir' }}
                        </span>
                    </div>
                </div>
                
                <form action="/marketing/reset" method="POST">
                    @csrf
                    <button type="submit" class="bg-white/60 hover:bg-rose-50 text-rose-600 border border-rose-200/50 rounded-full px-5 py-2.5 text-sm font-bold flex items-center gap-2 shadow-sm transition-all">
                        <span class="material-symbols-outlined text-sm">restart_alt</span> Buat Sesi Baru
                    </button>
                </form>
            </div>

            <div id="chatBox" class="chat-container flex-1 bg-white/40 backdrop-blur-2xl border border-white/60 rounded-[2rem] p-4 sm:p-6 shadow-[0_8px_32px_0_rgba(31,38,135,0.05)] overflow-y-auto flex flex-col gap-6">
                
                @foreach($history as $index => $chat)
                    @if($index == 0) @continue @endif

                    @if($chat['role'] == 'model')
                        <div class="flex justify-start">
                            <div class="max-w-[95%] sm:max-w-[85%]">
                                <div class="flex items-center gap-2 mb-1 ml-2">
                                    <div class="w-7 h-7 rounded-full bg-gradient-to-r from-fuchsia-500 to-indigo-500 flex items-center justify-center text-white shadow-sm"><span class="material-symbols-outlined text-[14px]">auto_awesome</span></div>
                                    <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">CMO Virtual</span>
                                </div>
                                <div class="ai-content bg-white/80 backdrop-blur-sm p-5 sm:p-7 rounded-[2rem] rounded-tl-none border border-white shadow-sm">
                                    {!! str_replace('(Tolong jawab menggunakan tag HTML dasar seperti <p>, <ul>, <li>, <strong>, atau <br> agar rapi saat ditampilkan).', '', $chat['parts'][0]['text']) !!}
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="flex justify-end mt-4">
                            <div class="max-w-[85%]">
                                <div class="flex justify-end items-center gap-2 mb-1 mr-2">
                                    <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Anda</span>
                                </div>
                                <div class="bg-slate-800 text-slate-100 p-4 sm:p-5 rounded-[2rem] rounded-tr-none shadow-md text-sm sm:text-base leading-relaxed">
                                    {{ str_replace("\n\n(Tolong jawab menggunakan tag HTML dasar seperti <p>, <ul>, <li>, <strong>, atau <br> agar rapi saat ditampilkan).", "", $chat['parts'][0]['text']) }}
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach

            </div>

            <div class="shrink-0 mt-2">
                <form action="/marketing/chat" method="POST" class="relative">
                    @csrf
                    <input type="text" name="message" required autocomplete="off" placeholder="Ketik perintah lanjutan untuk AI..." 
                        class="w-full bg-white/80 backdrop-blur-xl border-2 border-white/60 rounded-full pl-6 pr-16 py-4 text-slate-700 outline-none focus:bg-white focus:border-fuchsia-400 focus:ring-4 focus:ring-fuchsia-500/20 transition-all shadow-lg shadow-indigo-900/5 text-sm sm:text-base font-medium">
                    <button type="submit" class="absolute right-2 top-2 bottom-2 bg-gradient-to-r from-fuchsia-600 to-indigo-600 hover:from-fuchsia-700 hover:to-indigo-700 text-white w-12 rounded-full flex items-center justify-center shadow-md transition-transform hover:scale-105">
                        <span class="material-symbols-outlined">send</span>
                    </button>
                </form>
            </div>
            
        @endif

    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var chatBox = document.getElementById("chatBox");
            if(chatBox) { chatBox.scrollTop = chatBox.scrollHeight; }
        });
    </script>
</body>
</html>