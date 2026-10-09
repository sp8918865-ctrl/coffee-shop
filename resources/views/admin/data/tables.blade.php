@extends('admin.data.layout')

@section('title', 'Kelola Meja')
@section('heading', 'Daftar Meja')

@section('content')
<div class="space-y-5">
    <div class="grid gap-3 sm:grid-cols-2">
        <div class="rounded-xl border border-[#1e293b] bg-[#121824] p-4"><span class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">TOTAL MEJA</span><strong class="mt-2 block text-2xl font-extrabold text-white">{{ $tables->count() }}</strong><span class="text-[10px] text-slate-500">Terdaftar di kedai</span></div>
        <div class="rounded-xl border border-emerald-500/20 bg-[#121824] p-4"><span class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">MEJA TERSEDIA</span><strong id="available-table-count" class="mt-2 block text-2xl font-extrabold text-emerald-400">{{ $tables->where('status', 'available')->count() }}</strong><span class="text-[10px] text-slate-500">Bisa dipilih pelanggan saat memesan</span></div>
    </div>
    <p id="table-refresh-error" role="alert" hidden class="rounded-lg border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-xs text-rose-200">Status meja gagal diperbarui otomatis. Periksa koneksi atau muat ulang halaman.</p>

    <details class="rounded-xl border border-[#1e293b] bg-[#121824] p-5">
        <summary class="flex cursor-pointer list-none items-center justify-between text-xs font-bold text-amber-400"><span class="flex items-center gap-2"><i data-lucide="plus" class="h-4 w-4"></i>Tambah meja</span><i data-lucide="chevron-down" class="h-4 w-4"></i></summary>
        <form method="POST" action="{{ route('admin.tables.store') }}" class="mt-4">
            @csrf
            <div class="grid gap-3 sm:grid-cols-3">
                <label class="text-xs text-slate-300">Nomor / nama meja<input name="table_number" required maxlength="30" value="{{ old('table_number') }}" placeholder="Contoh: 01 atau Teras 1" class="mt-1 w-full rounded-lg border border-[#26334d] bg-[#0b0f17] px-3 py-2 text-white"></label>
                <label class="text-xs text-slate-300">Kapasitas orang<input name="capacity" required type="number" min="1" max="100" value="{{ old('capacity', 2) }}" class="mt-1 w-full rounded-lg border border-[#26334d] bg-[#0b0f17] px-3 py-2 text-white"></label>
                <label class="text-xs text-slate-300">Status<select name="status" required class="mt-1 w-full rounded-lg border border-[#26334d] bg-[#0b0f17] px-3 py-2 text-white"><option value="available">Tersedia</option><option value="occupied">Terpakai</option><option value="reserved">Direservasi</option></select></label>
            </div>
            <button class="mt-4 rounded-lg bg-amber-500 px-4 py-2 text-xs font-bold text-slate-950">Simpan meja</button>
        </form>
    </details>

    <section class="grid gap-3 lg:grid-cols-2">
        @forelse ($tables as $table)
            <article data-table-id="{{ $table->table_id }}" class="rounded-xl border border-[#1e293b] bg-[#121824] p-4">
                <div class="flex items-center justify-between gap-3">
                    <div><h2 class="text-sm font-bold text-white">Meja {{ $table->table_number }}</h2><p class="mt-1 text-[10px] text-slate-400">Kapasitas {{ $table->capacity }} orang · {{ $table->orders_count }} pesanan tercatat</p></div>
                    <span data-table-status class="rounded-full border px-2.5 py-1 text-[10px] font-semibold {{ $table->status === 'available' ? 'border-emerald-500/20 bg-emerald-500/10 text-emerald-400' : ($table->status === 'occupied' ? 'border-rose-500/20 bg-rose-500/10 text-rose-300' : 'border-amber-500/20 bg-amber-500/10 text-amber-300') }}">{{ ['available' => 'Tersedia', 'occupied' => 'Terpakai', 'reserved' => 'Direservasi'][$table->status] ?? 'Tidak tersedia' }}</span>
                </div>
                <details class="mt-4 border-t border-[#1e293b] pt-3">
                    <summary class="cursor-pointer text-xs font-semibold text-amber-400">Ubah meja</summary>
                    <form method="POST" action="{{ route('admin.tables.update', $table) }}" class="mt-3 grid gap-3 sm:grid-cols-3">
                        @csrf @method('PUT')
                        <label class="text-[10px] text-slate-400">Nomor<input name="table_number" required maxlength="30" value="{{ $table->table_number }}" class="mt-1 w-full rounded-lg border border-[#26334d] bg-[#0b0f17] px-2 py-2 text-xs text-white"></label>
                        <label class="text-[10px] text-slate-400">Kapasitas<input name="capacity" required type="number" min="1" max="100" value="{{ $table->capacity }}" class="mt-1 w-full rounded-lg border border-[#26334d] bg-[#0b0f17] px-2 py-2 text-xs text-white"></label>
                        <label class="text-[10px] text-slate-400">Status<select name="status" required class="mt-1 w-full rounded-lg border border-[#26334d] bg-[#0b0f17] px-2 py-2 text-xs text-white"><option value="available" @selected($table->status === 'available')>Tersedia</option><option value="occupied" @selected($table->status === 'occupied')>Terpakai</option><option value="reserved" @selected($table->status === 'reserved')>Direservasi</option></select></label>
                        <button class="rounded-lg bg-amber-500 px-3 py-2 text-xs font-bold text-slate-950 sm:col-span-3">Simpan perubahan</button>
                    </form>
                </details>
                <form method="POST" action="{{ route('admin.tables.destroy', $table) }}" onsubmit="return confirm('Hapus meja ini dari daftar?')" class="mt-3">@csrf @method('DELETE')<button class="text-xs text-rose-400">Hapus meja</button></form>
            </article>
        @empty
            <p class="rounded-xl border border-dashed border-[#26334d] p-6 text-center text-xs text-slate-400 lg:col-span-2">Belum ada meja. Tambahkan meja agar pelanggan dapat memilihnya saat memesan di tempat.</p>
        @endforelse
    </section>
</div>
@endsection

@push('scripts')
<script>
    const tableRefreshError = document.getElementById('table-refresh-error');
    let tableRefreshInProgress = false;

    async function refreshTableStatuses() {
        if (tableRefreshInProgress) return;
        tableRefreshInProgress = true;

        try {
            const response = await fetch(@json(route('admin.tables.status')), {
                headers: { 'Accept': 'application/json' },
                cache: 'no-store'
            });
            if (!response.ok) throw new Error('Status meja tidak dapat dimuat.');

            const data = await response.json();
            let availableCount = 0;

            data.tables.forEach(table => {
                const card = document.querySelector(`[data-table-id="${table.id}"]`);
                if (!card) return;

                const available = table.status === 'available';
                if (available) availableCount++;

                const badge = card.querySelector('[data-table-status]');
                const labels = { available: 'Tersedia', occupied: 'Terpakai', reserved: 'Direservasi' };
                const colors = {
                    available: 'border-emerald-500/20 bg-emerald-500/10 text-emerald-400',
                    occupied: 'border-rose-500/20 bg-rose-500/10 text-rose-300',
                    reserved: 'border-amber-500/20 bg-amber-500/10 text-amber-300'
                };
                badge.textContent = labels[table.status] || 'Tidak tersedia';
                badge.className = `rounded-full border px-2.5 py-1 text-[10px] font-semibold ${colors[table.status] || colors.reserved}`;

                const statusSelect = card.querySelector('select[name="status"]');
                if (statusSelect && document.activeElement !== statusSelect) {
                    statusSelect.value = table.status;
                }
            });

            document.getElementById('available-table-count').textContent = availableCount;
            tableRefreshError.hidden = true;
        } catch (error) {
            tableRefreshError.hidden = false;
        } finally {
            tableRefreshInProgress = false;
        }
    }

    refreshTableStatuses();
    window.setInterval(refreshTableStatuses, 5000);
</script>
@endpush
