<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Rekam Medis - {{ $report->user_data['name'] ?? 'Pasien' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 font-sans text-slate-800 pb-12">

    <nav class="bg-teal-700 shadow-md p-4 sticky top-0 z-50">
        <div class="max-w-4xl mx-auto flex items-center justify-between text-white">
            <div class="flex items-center gap-3">
                <a href="/dashboard" class="bg-white/20 hover:bg-white/30 p-2 rounded-lg transition-colors" title="Kembali ke Dashboard">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <h1 class="text-xl font-bold">Detail Screening Pasien</h1>
            </div>
            <div class="text-sm">ID: <span class="font-mono bg-teal-800 px-2 py-1 rounded">{{ substr($report->id, 0, 8) }}</span></div>
        </div>
    </nav>

    <main class="max-w-4xl mx-auto p-4 md:p-6 mt-4 space-y-6">
        
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 flex flex-wrap gap-6 items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="bg-teal-100 text-teal-600 p-4 rounded-full">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-slate-900">{{ $report->user_data['name'] ?? 'Anonim' }}</h2>
                    <p class="text-slate-500">{{ $report->user_data['age'] ?? '-' }} Tahun • {{ $report->user_data['gender'] ?? '-' }} • WhatsApp: {{ $report->user_data['whatsapp'] ?? '-' }}</p>
                    <p class="text-slate-500 text-sm mt-1">Email: {{ $report->user_data['email'] ?? '-' }}</p>
                </div>
            </div>
            
            @php
                $risk = $report->report_data['risk_level'] ?? 'Tidak diketahui';
                $colorClass = 'bg-slate-100 text-slate-600 border-slate-200';
                if($risk == 'Tinggi') $colorClass = 'bg-rose-50 text-rose-600 border-rose-200';
                if($risk == 'Sedang') $colorClass = 'bg-amber-50 text-amber-500 border-amber-200';
                if($risk == 'Rendah') $colorClass = 'bg-emerald-50 text-emerald-500 border-emerald-200';
            @endphp
            <div class="text-center px-6 py-4 rounded-xl border {{ $colorClass }}">
                <p class="text-xs font-bold uppercase tracking-wider mb-1 opacity-80">Risiko Klinis</p>
                <div class="text-3xl font-black leading-none">{{ $risk }}</div>
                <div class="text-sm font-semibold mt-1">Skor: {{ $report->report_data['risk_score'] ?? 0 }}/100</div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <div class="space-y-6">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                    <h3 class="text-lg font-bold text-slate-800 mb-4 border-b pb-2 flex items-center gap-2">
                        <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Ringkasan Medis
                    </h3>
                    
                    <div class="space-y-4">
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase mb-1">Keluhan Utama</p>
                            <p class="text-sm text-slate-700 bg-slate-50 p-3 rounded-lg">{{ $report->user_data['chiefComplaint'] ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase mb-1">Analisis AI</p>
                            <p class="text-sm text-slate-700 bg-slate-50 p-3 rounded-lg text-justify">{{ $report->report_data['anamnesis_reasoning'] ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase mb-1">Suspek Kondisi</p>
                            <ul class="list-disc list-inside text-sm text-rose-600 font-semibold bg-rose-50 p-3 rounded-lg">
                                @foreach($report->report_data['suspected_conditions'] ?? [] as $suspek)
                                    <li>{{ $suspek }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 flex flex-col h-[600px]">
                <div class="p-4 border-b border-slate-200 bg-slate-50 rounded-t-2xl">
                    <h3 class="font-bold text-slate-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                        Transkrip Anamnesis (Raw Data)
                    </h3>
                </div>
                
                <div class="flex-1 overflow-y-auto p-4 space-y-4 bg-slate-100">
                    @php
                        $chats = $report->chat_history ?? [];
                    @endphp
                    
                    @if(empty($chats))
                        <div class="text-center text-slate-400 text-sm mt-10">Tidak ada riwayat chat yang tersimpan.</div>
                    @else
                        @foreach($chats as $chat)
                            @if(isset($chat['role']) && $chat['role'] === 'user')
                                <div class="flex justify-end">
                                    <div class="max-w-[85%] bg-teal-600 text-white p-3 rounded-2xl rounded-tr-none shadow-sm text-sm whitespace-pre-wrap">
                                        @if(!empty($chat['text']))
                                            <div>{{ $chat['text'] }}</div>
                                        @endif
                                        @if(!empty($chat['images']))
                                            <div class="flex flex-wrap gap-2 mt-2">
                                                @foreach($chat['images'] as $img)
                                                    <a href="{{ $img }}" target="_blank" title="Klik untuk memperbesar">
                                                        <img src="{{ $img }}" class="h-24 w-24 object-cover rounded-lg border border-teal-500/50 hover:opacity-90 transition-opacity">
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <div class="flex justify-start">
                                    <div class="max-w-[85%] bg-white border border-slate-200 text-slate-800 p-3 rounded-2xl rounded-tl-none shadow-sm text-sm whitespace-pre-wrap">
                                        {{ $chat['text'] ?? '' }}
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    @endif
                </div>
            </div>

        </div>
    </main>
</body>
</html>