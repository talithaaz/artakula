@extends('layouts.index')

@section('title','Profil | Artakula')
@section('page_title','Manajemen Profil')

@section('content')

<div class="card-artakula p-4">


<div class="row g-4">

    {{-- FOTO PROFIL --}}
    <div class="col-md-4 text-center border-end">

        @if($user->foto)
            <img src="{{ asset('foto_profil/'.$user->foto) }}" width="150" class="rounded-circle mb-3 shadow-sm">
        @else
            <img src="https://ui-avatars.com/api/?name={{ $user->name }}" width="150" class="rounded-circle mb-3 shadow-sm">
        @endif

        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <input type="file" name="foto" class="form-control form-control-sm mb-3">

            <small class="text-muted">
                Format: JPG/PNG (Max 2MB)
            </small>

    </div>

    {{-- DATA PROFIL --}}
    <div class="col-md-8">

        <h6 class="fw-bold mb-3">Informasi Akun</h6>

        <div class="mb-3">
            <label class="form-label">Nama</label>
            <input type="text" name="name" class="form-control" value="{{ $user->name }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" value="{{ $user->username }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="{{ $user->email }}">
        </div>

        <button class="btn btn-primary">
            Simpan Perubahan
        </button>

        </form>

        {{-- GANTI PASSWORD --}}
        <hr class="my-4">

        <h6 class="fw-bold mb-3">Keamanan Akun</h6>

        <form action="{{ route('profile.password') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label">Password Lama</label>
                <input type="password" name="password_lama" class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label">Password Baru</label>
                <input type="password" name="password_baru" class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label">Konfirmasi Password</label>
                <input type="password" name="password_baru_confirmation" class="form-control">
            </div>

            <button class="btn btn-outline-danger">
                Ganti Password
            </button>

        </form>

    </div>

</div>


</div>

@endsection
