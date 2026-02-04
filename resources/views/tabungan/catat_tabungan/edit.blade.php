@extends('layouts.index') {{-- Pakai layout utama. --}}

@section('title', 'Edit Tabungan | Artakula') {{-- Judul halaman pada title tag. --}}

@section('content') {{-- Mulai section konten utama. --}}

<h5 class="fw-bold mb-4">Edit Tabungan</h5> {{-- Judul kecil halaman. --}}

<div class="card-artakula p-4 col-md-6"> {{-- Card wrapper form. --}}

    <form method="POST" action="{{ route('tabungan.update', $tabungan->id) }}"> {{-- Form update tabungan. --}}
        @csrf {{-- Token CSRF. --}}
        @method('PUT') {{-- Spoof method PUT. --}}

        <input type="hidden" name="bulan" value="{{ request('bulan') }}"> {{-- Simpan bulan filter. --}}
        <input type="hidden" name="tahun" value="{{ request('tahun') }}"> {{-- Simpan tahun filter. --}}

        {{-- Kategori --}}
        <div class="mb-3"> {{-- Grup input kategori. --}}
            <label class="form-label fw-bold">Kategori Tabungan</label> {{-- Label kategori tabungan. --}}
            <select name="kategori_tabungan_id" class="form-select" required> {{-- Dropdown kategori. --}}
                @foreach ($kategori as $item) {{-- Loop daftar kategori. --}}
                    <option value="{{ $item->id }}"
                        @selected($item->id == $tabungan->kategori_tabungan_id)> {{-- Set selected jika cocok. --}}
                        {{ $item->nama_kategori }} {{-- Nama kategori. --}}
                    </option> {{-- Tutup opsi kategori. --}}
                @endforeach {{-- Tutup loop kategori. --}}
            </select> {{-- Tutup dropdown kategori. --}}
        </div> {{-- Tutup grup input kategori. --}}

        {{-- Sumber dompet --}}
        <div class="mb-3"> {{-- Grup input sumber dompet. --}}
            <label class="form-label fw-bold">Sumber Dompet</label> {{-- Label sumber dompet. --}}
            <select name="sumber_dompet_id" class="form-select" required> {{-- Dropdown sumber dompet. --}}
                @foreach ($dompet as $d) {{-- Loop daftar dompet. --}}
                    <option value="{{ $d->id }}"
                        @selected($d->id == $tabungan->sumber_dompet_id)> {{-- Set selected jika cocok. --}}
                        {{ $d->nama_dompet }} {{-- Nama dompet. --}}
                    </option> {{-- Tutup opsi dompet. --}}
                @endforeach {{-- Tutup loop dompet. --}}
            </select> {{-- Tutup dropdown sumber dompet. --}}
        </div> {{-- Tutup grup input sumber dompet. --}}

        {{-- Dompet tujuan --}}
        <div class="mb-3"> {{-- Grup input dompet tujuan. --}}
            <label class="form-label fw-bold">Dompet Tujuan</label> {{-- Label dompet tujuan. --}}
            <input type="text"
                   class="form-control"
                   value="{{ optional($tabungan->kategori->dompetTujuan)->nama_dompet ?? '-' }}"
                   readonly> {{-- Tampilkan dompet tujuan (read-only). --}}
        </div> {{-- Tutup grup input dompet tujuan. --}}

        {{-- Nominal --}}
        <div class="mb-3"> {{-- Grup input nominal. --}}
            <label class="form-label fw-bold">Nominal (Rp)</label> {{-- Label nominal. --}}
            <input type="number"
                   name="nominal"
                   value="{{ $tabungan->nominal }}"
                   class="form-control"
                   required> {{-- Input nominal. --}}
        </div> {{-- Tutup grup input nominal. --}}

        {{-- Tanggal --}}
        <div class="mb-3"> {{-- Grup input tanggal. --}}
            <label class="form-label fw-bold">Tanggal</label> {{-- Label tanggal. --}}
            <input type="date"
                   name="tanggal"
                   value="{{ $tabungan->tanggal }}"
                   class="form-control"
                   required> {{-- Input tanggal. --}}
        </div> {{-- Tutup grup input tanggal. --}}

        {{-- Keterangan --}}
        <div class="mb-3"> {{-- Grup input keterangan. --}}
            <label class="form-label fw-bold">Keterangan</label> {{-- Label keterangan. --}}
            <textarea name="keterangan"
                      class="form-control"
                      rows="3">{{ old('keterangan', $tabungan->keterangan) }}</textarea> {{-- Input keterangan. --}}
        </div> {{-- Tutup grup input keterangan. --}}

        {{-- Aksi --}}
        <div class="d-flex gap-2"> {{-- Wrapper tombol aksi. --}}
            <a href="{{ route('tabungan.index', [
                    'bulan' => request('bulan'),
                    'tahun' => request('tahun')
                ]) }}"
               class="btn btn-secondary"> {{-- Tombol batal. --}}
                Batal
            </a>

            <button type="submit" class="btn btn-primary">
                Update
            </button> {{-- Tombol update. --}}
        </div> {{-- Tutup wrapper tombol aksi. --}}

    </form> {{-- Tutup form. --}}
</div> {{-- Tutup card wrapper. --}}

@endsection {{-- Akhiri section konten. --}}
