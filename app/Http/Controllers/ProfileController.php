<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    // halaman profil
    public function index()
    {
        $user = Auth::user();
        return view('profile.index', compact('user'));
    }

    // update data profil
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required',
            // 'username' => 'required|unique:tb_users,username,' . $user->id,
            'email' => 'required|email|unique:tb_users,email,' . $user->id,
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // upload foto
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $namaFile = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('foto_profil'), $namaFile);
            $user->foto = $namaFile;
        }

        $user->name = $request->name;
        // $user->username = $request->username;
        $user->email = $request->email;
        $user->save();

        return back()->with('success', 'Profil berhasil diperbarui');
    }

    // ganti password
    public function updatePassword(Request $request)
    {
        $request->validate([
            'password_lama' => 'required',
            'password_baru' => 'required|min:6|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->password_lama, $user->password)) {
            return back()->with('error', 'Password lama salah');
        }

        $user->password = Hash::make($request->password_baru);
        $user->save();

        return back()->with('success', 'Password berhasil diganti');
    }
}
