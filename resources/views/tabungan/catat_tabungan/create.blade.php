@extends('layouts.index') {{-- Pakai layout utama. --}}

@section('title', 'Tambah Tabungan') {{-- Judul halaman pada title tag. --}}

@section('content') {{-- Mulai section konten utama. --}}

<h5 class="fw-bold mb-4">Tambah Tabungan</h5> {{-- Judul kecil halaman. --}}

<div class="card-artakula p-4 col-md-8"> {{-- Card wrapper form. --}}

    {{-- ================= FORM GET (PILIH KATEGORI) ================= --}}
    <form method="GET" action="{{ route('tabungan.create') }}"> {{-- Form pilih kategori (GET). --}}
        <input type="hidden" name="bulan" value="{{ request('bulan') }}"> {{-- Simpan bulan filter. --}}
        <input type="hidden" name="tahun" value="{{ request('tahun') }}"> {{-- Simpan tahun filter. --}}

        <label class="form-label fw-bold">Kategori Tabungan</label> {{-- Label kategori tabungan. --}}
        <select name="kategori_tabungan_id"
                class="form-select mb-3"
                onchange="this.form.submit()"
                required> {{-- Dropdown kategori, submit otomatis saat berubah. --}}
            <option value="">Pilih Kategori Tabungan</option> {{-- Placeholder kategori. --}}

            @foreach ($kategoriTabungan as $k) {{-- Loop daftar kategori tabungan. --}}
                <option value="{{ $k->id }}"
                    {{ request('kategori_tabungan_id') == $k->id ? 'selected' : '' }}> {{-- Set selected jika cocok. --}}
                    {{ $k->nama_kategori }} {{-- Nama kategori. --}}
                </option> {{-- Tutup opsi kategori. --}}
            @endforeach {{-- Tutup loop kategori. --}}
        </select> {{-- Tutup dropdown kategori. --}}
    </form> {{-- Tutup form GET. --}}

    {{-- ================= FORM POST (SIMPAN DATA) ================= --}}
    <form method="POST" action="{{ route('tabungan.store') }}"> {{-- Form simpan tabungan (POST). --}}
        @csrf {{-- Token CSRF. --}}

        <input type="hidden" name="bulan" value="{{ request('bulan') }}"> {{-- Simpan bulan filter. --}}
        <input type="hidden" name="tahun" value="{{ request('tahun') }}"> {{-- Simpan tahun filter. --}}
        <input type="hidden"
               name="kategori_tabungan_id"
               value="{{ request('kategori_tabungan_id') }}"> {{-- Simpan kategori terpilih. --}}

        {{-- Sumber dompet --}}
        <label class="form-label fw-bold">Sumber Dompet</label> {{-- Label sumber dompet. --}}
        <select name="sumber_dompet_id" class="form-select mb-3" required> {{-- Dropdown sumber dompet. --}}
            <option value="">Pilih Sumber Dompet</option> {{-- Placeholder sumber dompet. --}}
            @foreach ($dompet as $d) {{-- Loop daftar dompet. --}}
                <option value="{{ $d->id }}">{{ $d->nama_dompet }}</option> {{-- Opsi dompet. --}}
            @endforeach {{-- Tutup loop dompet. --}}
        </select> {{-- Tutup dropdown sumber dompet. --}}

        {{-- Dompet tujuan (otomatis) --}}
        <div class="mb-3"> {{-- Grup input dompet tujuan. --}}
            <label class="form-label fw-bold">Dompet Tujuan</label> {{-- Label dompet tujuan. --}}
            <input type="text"
                   class="form-control"
                   value="{{ $dompetTujuan?->nama_dompet ?? '-' }}"
                   readonly> {{-- Tampilkan dompet tujuan (read-only). --}}
        </div> {{-- Tutup grup dompet tujuan. --}}

        <input type="hidden"
               name="dompet_id"
               value="{{ $dompetTujuan?->id }}"> {{-- Simpan ID dompet tujuan. --}}

        {{-- Nominal --}}
        <label class="form-label fw-bold">Nominal (Rp)</label> {{-- Label nominal. --}}
        <input type="number"
               name="nominal"
               class="form-control mb-3"
               required> {{-- Input nominal. --}}

        {{-- Tanggal --}}
        <label class="form-label fw-bold">Tanggal</label> {{-- Label tanggal. --}}
        <input type="date"
               name="tanggal"
               class="form-control mb-3"
               required> {{-- Input tanggal. --}}

        {{-- Keterangan --}}
        <label class="form-label fw-bold">Keterangan</label> {{-- Label keterangan. --}}
        <input type="text"
               name="keterangan"
               class="form-control mb-3"
               placeholder="Keterangan (opsional)"> {{-- Input keterangan. --}}

        {{-- Aksi --}}
        <a href="{{ route('tabungan.index', [
                'bulan' => request('bulan'),
                'tahun' => request('tahun')
            ]) }}"
           class="btn btn-secondary"> {{-- Tombol batal. --}}
            Batal
        </a>

        <button class="btn btn-success">
            Simpan
        </button> {{-- Tombol simpan. --}}

    </form> {{-- Tutup form POST. --}}
</div> {{-- Tutup card wrapper. --}}

@endsection {{-- Akhiri section konten. --}}
