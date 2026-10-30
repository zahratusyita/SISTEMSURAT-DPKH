<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    /**
     * Tampilkan halaman profil akun.
     */
    public function index()
    {
        $users = User::where('id', '!=', auth()->id())->orderBy('name')->get();

        return view('profile.index', compact('users'));
    }

    /**
     * Update data profil (nama & email) user yang sedang login.
     */
    public function update(ProfileRequest $request)
    {
        $user = auth()->user();

        $validated = $request->validated();

        $user->update($validated);

        return back()->with('success', 'Informasi profil berhasil diperbarui.');
    }

    /**
     * Update password user yang sedang login.
     */
    public function updatePassword(ProfileRequest $request)
    {
        $validated = $request->validated();

        auth()->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Password berhasil diperbarui. Silakan login ulang jika diperlukan.');
    }

    /**
     * Tambahkan admin baru.
     */
    public function storeAdmin(ProfileRequest $request)
    {
        $validated = $request->validated();

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', "Admin '{$validated['name']}' berhasil ditambahkan.");
    }

    /**
     * Hapus akun admin lain (tidak bisa hapus diri sendiri).
     */
    public function destroyAdmin(User $user)
    {
        $this->authorize('delete', $user);
        $name = $user->name;
        DB::transaction(function () use ($user) {
            // Lock in a stable order to serialize simultaneous administrator removals.
            $active = User::orderBy('id')->lockForUpdate()->get();
            if (! $active->contains('id', auth()->id())) {
                abort(403);
            }
            if ($active->count() <= 1) {
                throw ValidationException::withMessages(['admin' => 'Administrator aktif terakhir tidak dapat dihapus.']);
            }
            $target = $active->firstWhere('id', $user->id);
            if (! $target) {
                abort(404);
            }
            $target->remember_token = null;
            $target->save();
            $target->delete();
            DB::table('sessions')->where('user_id', $target->id)->delete();
            DB::table('password_reset_tokens')->where('email', $target->email)->delete();
        }, 3);

        return back()->with('success', "Akun admin '{$name}' berhasil dihapus. Identitas pencatat pada surat tetap tersimpan.");
    }
}
