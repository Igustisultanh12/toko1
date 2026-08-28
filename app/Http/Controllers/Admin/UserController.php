<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class UserController extends Controller
{
    /**
     * Tampilkan daftar seluruh akun (Admin & Kasir)
     */
    public function index(Request $request)
    {
        $users = User::orderBy('created_at', 'desc')->get();

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
        ]);
    }

    /**
     * Form tambah akun baru
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Simpan akun kasir / admin baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'role'     => ['required', 'in:admin,cashier'],
            'alias'    => ['nullable', 'string', 'max:100'],
            'password' => ['required', 'string', 'min:6'],
        ], [
            'name.required'     => 'Nama pengguna wajib diisi.',
            'email.required'    => 'Alamat email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'email.unique'      => 'Email ini sudah digunakan oleh akun lain.',
            'role.required'     => 'Peran akun (Role) wajib dipilih.',
            'password.required' => 'Password wajib diisi.',
            'password.min'      => 'Password minimal harus 6 karakter.',
        ]);

        User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'role'     => $validated['role'],
            'alias'    => $validated['alias'] ?? null,
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Akun ' . ($validated['role'] === 'cashier' ? 'Kasir' : 'Admin') . ' baru berhasil ditambahkan!');
    }

    /**
     * Form edit akun
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update data akun
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role'     => ['required', 'in:admin,cashier'],
            'alias'    => ['nullable', 'string', 'max:100'],
            'password' => ['nullable', 'string', 'min:6'],
        ], [
            'name.required'  => 'Nama pengguna wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email'    => 'Format email tidak valid.',
            'email.unique'   => 'Email ini sudah digunakan oleh akun lain.',
            'role.required'  => 'Peran akun (Role) wajib dipilih.',
            'password.min'   => 'Password minimal harus 6 karakter.',
        ]);

        $userData = [
            'name'  => $validated['name'],
            'email' => $validated['email'],
            'role'  => $validated['role'],
            'alias' => $validated['alias'] ?? null,
        ];

        // Jika password diisi, update password baru
        if (!empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }

        $user->update($userData);

        return redirect()->route('admin.users.index')->with('success', 'Data akun ' . $user->name . ' berhasil diperbarui!');
    }

    /**
     * Hapus akun pengguna
     */
    public function destroy(User $user)
    {
        // Cegah menghapus akun diri sendiri yang sedang login
        if (Auth::id() === $user->id) {
            return redirect()->route('admin.users.index')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif digunakan!');
        }

        $userName = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Akun ' . $userName . ' berhasil dihapus dari sistem!');
    }
}
