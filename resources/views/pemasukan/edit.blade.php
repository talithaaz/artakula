@extends('layouts.index') {{-- Pakai layout utama. --}}

@section('title','Edit Pemasukan') {{-- Judul halaman pada title tag. --}}

@section('content') {{-- Mulai section konten utama. --}}

<h5 class="fw-bold mb-4">Edit Pemasukan</h5> {{-- Judul kecil pada konten. --}}

<div class="card-artakula p-4 col-md-8"> {{-- Card wrapper form. --}}
<form method="POST" action="{{ route('pemasukan.update', $pemasukan->id) }}"> {{-- Form update pemasukan. --}}
@csrf {{-- Token CSRF untuk keamanan form. --}}
@method('PUT') {{-- Spoof method PUT untuk update. --}}
<input type="hidden" name="bulan" value="{{ $bulan }}"> {{-- Simpan bulan filter saat kembali. --}}
    <input type="hidden" name="tahun" value="{{ $tahun }}"> {{-- Simpan tahun filter saat kembali. --}}
<select name="dompet_id" class="form-select mb-3" required> {{-- Dropdown pilihan dompet. --}}
    <option value="">Pilih Dompet</option> {{-- Placeholder pilihan dompet. --}}
    @foreach($dompets as $d) {{-- Loop daftar dompet. --}}
        <option value="{{ $d->id }}"  {{-- Value id dompet. --}}
            {{ $pemasukan->dompet_id == $d->id ? 'selected' : '' }}> {{-- Set selected jika cocok. --}}
            {{ $d->nama_dompet }} {{-- Tampilkan nama dompet. --}}
        </option> {{-- Tutup opsi dompet. --}}
    @endforeach {{-- Selesai loop dompet. --}}
</select> {{-- Tutup dropdown dompet. --}}

<input type="text" name="keterangan" class="form-control mb-3"  {{-- Input keterangan. --}}
    placeholder="Keterangan" value="{{ $pemasukan->keterangan }}" required> {{-- Nilai awal keterangan. --}}

<input type="number" name="jumlah" class="form-control mb-3"  {{-- Input jumlah pemasukan. --}}
    placeholder="Jumlah" value="{{ $pemasukan->jumlah }}" required> {{-- Nilai awal jumlah. --}}

<input type="date" name="tanggal" class="form-control mb-3"  {{-- Input tanggal pemasukan. --}}
    value="{{ $pemasukan->tanggal }}" required> {{-- Nilai awal tanggal. --}}

    {{-- Link kembali ke halaman index dengan filter bulan/tahun. --}}
    <a href="{{ route('pemasukan.index', [
                'bulan' => request('bulan'),
                'tahun' => request('tahun')
            ]) }}" class="btn btn-secondary">Batal</a>
<button class="btn btn-success">Simpan</button> {{-- Tombol submit simpan. --}}


</form> {{-- Tutup form. --}}
</div> {{-- Tutup card wrapper. --}}
@endsection {{-- Akhiri section konten. --}}
