@extends('layouts.index') {{-- Pakai layout utama. --}}

@section('title','Tambah Pemasukan') {{-- Judul halaman pada title tag. --}}

@section('content') {{-- Mulai section konten utama. --}}

<h5 class="fw-bold mb-4">Tambah Pemasukan</h5> {{-- Judul kecil pada konten. --}}

<div class="card-artakula p-4 col-md-8"> {{-- Card wrapper form. --}}
<form method="POST" action="{{ route('pemasukan.store') }}"> {{-- Form simpan pemasukan. --}}
@csrf {{-- Token CSRF untuk keamanan form. --}}
<input type="hidden" name="bulan" value="{{ request('bulan') }}"> {{-- Simpan bulan filter saat kembali. --}}
            <input type="hidden" name="tahun" value="{{ request('tahun') }}"> {{-- Simpan tahun filter saat kembali. --}}
<select name="dompet_id" class="form-select mb-3" required> {{-- Dropdown pilihan dompet. --}}
    <option value="">Pilih Dompet</option> {{-- Placeholder pilihan dompet. --}}
    @foreach($dompets as $d) {{-- Loop daftar dompet. --}}
        <option value="{{ $d->id }}">{{ $d->nama_dompet }}</option> {{-- Opsi dompet. --}}
    @endforeach {{-- Selesai loop dompet. --}}
</select> {{-- Tutup dropdown dompet. --}}

<input type="text" name="keterangan" class="form-control mb-3" placeholder="Keterangan" required> {{-- Input keterangan. --}}
<input type="number" name="jumlah" class="form-control mb-3" placeholder="Jumlah" required> {{-- Input jumlah pemasukan. --}}
<input type="date" name="tanggal" class="form-control mb-3" required> {{-- Input tanggal pemasukan. --}}

{{-- Link kembali ke halaman index dengan filter bulan/tahun. --}}
<a href="{{ route('pemasukan.index', [
                'bulan' => request('bulan'),
                'tahun' => request('tahun')
            ])  }}" class="btn btn-secondary">Batal</a>
<button class="btn btn-success">Simpan</button> {{-- Tombol submit simpan. --}}


</form> {{-- Tutup form. --}}
</div> {{-- Tutup card wrapper. --}}
@endsection {{-- Akhiri section konten. --}}
