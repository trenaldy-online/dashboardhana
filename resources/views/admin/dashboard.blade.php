<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - AHCC Screening</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 text-slate-800 p-4 md:p-8 min-h-screen">

    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <h1 class="text-3xl font-bold text-teal-700 flex items-center gap-2">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                Database Pasien AHCC
            </h1>

            <div class="flex items-center gap-4">
                <span class="bg-white px-4 py-2 rounded-lg shadow-sm text-sm font-medium text-slate-600 border border-slate-200">
                    Total Data: {{ $screenings->total() }}
                </span>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-rose-50 hover:bg-rose-100 text-rose-600 px-4 py-2 rounded-lg shadow-sm text-sm font-bold transition-colors border border-rose-200 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        Keluar
                    </button>
                </form>
            </div>
        </div>

        <form method="GET" action="{{ url('/admin/dashboard') }}" class="mb-6 bg-white p-5 rounded-xl shadow-sm border border-slate-200 flex flex-col lg:flex-row gap-4 items-end">
               
               <div class="flex-1 w-full">
                   <label class="block text-sm font-medium text-slate-700 mb-1">Cari Pasien</label>
                   <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama, WA, atau email..." 
                       class="w-full p-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none text-sm">
               </div>

               <div class="w-full lg:w-40">
                   <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal</label>
                   <input type="date" name="date" value="{{ request('date') }}" 
                       class="w-full p-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none text-sm bg-white text-slate-700">
               </div>

               <div class="w-full lg:w-40">
                   <label class="block text-sm font-medium text-slate-700 mb-1">Tingkat Risiko</label>
                   <select name="risk_level" class="w-full p-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none text-sm bg-white">
                       <option value="">Semua Risiko</option>
                       <option value="Tinggi" {{ request('risk_level') == 'Tinggi' ? 'selected' : '' }}>Tinggi</option>
                       <option value="Sedang" {{ request('risk_level') == 'Sedang' ? 'selected' : '' }}>Sedang</option>
                       <option value="Rendah" {{ request('risk_level') == 'Rendah' ? 'selected' : '' }}>Rendah</option>
                   </select>
               </div>

               <div class="w-full lg:w-48">
                   <label class="block text-sm font-medium text-slate-700 mb-1">Jenis Kanker</label>
                   <select name="cancer_type" class="w-full p-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none text-sm bg-white">
                       <option value="">Semua Kanker</option>
                       @foreach($cancerTypes as $type)
                           <option value="{{ $type }}" {{ request('cancer_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                       @endforeach
                   </select>
               </div>

               <div class="w-full lg:w-auto flex gap-2">
                   <button type="submit" class="flex-1 lg:flex-none bg-teal-600 hover:bg-teal-700 text-white px-6 py-2.5 rounded-lg font-medium transition-colors text-sm shadow-sm">
                       Terapkan
                   </button>
                   @if(request()->anyFilled(['search', 'risk_level', 'cancer_type', 'date']))
                       <a href="{{ url('/admin/dashboard') }}" class="flex-1 lg:flex-none bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2.5 rounded-lg font-medium transition-colors text-sm text-center border border-slate-200">
                           Reset
                       </a>
                   @endif
               </div>
           </form>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse whitespace-nowrap">
                    <thead>
                        <tr class="bg-slate-50 text-slate-600 text-sm border-b border-slate-200">
                            <th class="p-4 font-semibold w-16 text-center">No</th>
                            <th class="p-4 font-semibold">Tanggal</th>
                            <th class="p-4 font-semibold">Nama Pasien</th>
                            <th class="p-4 font-semibold">WhatsApp / Email</th>
                            <th class="p-4 font-semibold">Jenis Kanker</th>
                            <th class="p-4 font-semibold">Risiko</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($screenings as $item)
                            <tr class="hover:bg-teal-50/50 border-b border-slate-100 transition-colors">
                                <td class="p-4 text-sm text-slate-500 text-center">
                                    {{ ($screenings->currentPage() - 1) * $screenings->perPage() + $loop->iteration }}
                                </td>
                                <td class="p-4 text-sm text-slate-500">
                                    <div class="font-medium text-slate-700">{{ $item->created_at->format('d M Y') }}</div>
                                    <div class="text-xs">{{ $item->created_at->format('H:i') }} WIB</div>
                                </td>
                                <td class="p-4 font-semibold text-slate-800">{{ $item->name }}</td>
                                <td class="p-4 text-sm text-slate-600">
                                    <div class="font-medium">{{ $item->whatsapp }}</div>
                                    <div class="text-xs text-slate-400">{{ $item->email }}</div>
                                </td>
                                <td class="p-4 text-sm text-slate-700">{{ $item->cancer_type }}</td>
                                <td class="p-4">
                                    @if($item->risk_level == 'Tinggi')
                                        <span class="px-3 py-1 bg-rose-100 text-rose-700 rounded-full text-xs font-bold uppercase tracking-wider">Tinggi</span>
                                    @elseif($item->risk_level == 'Sedang')
                                        <span class="px-3 py-1 bg-amber-100 text-amber-700 rounded-full text-xs font-bold uppercase tracking-wider">Sedang</span>
                                    @else
                                        <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-xs font-bold uppercase tracking-wider">Rendah</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-12 text-center">
                                    <div class="text-slate-400 mb-2">
                                        <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                    </div>
                                    <p class="text-slate-500 font-medium">Belum ada data pasien yang ditemukan.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($screenings->hasPages())
                <div class="p-4 border-t border-slate-200 bg-slate-50">
                    {{ $screenings->links() }}
                </div>
            @endif
        </div>

    </div>

</body>
</html>