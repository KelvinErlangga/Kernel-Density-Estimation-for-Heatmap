<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\StoreUpdateUserRequest;

class UserAdminController extends Controller
{
    /**
     * Display a listing of the resource (Read All).
     */
    public function index()
    {
        // Ambil semua user termasuk yang soft deleted
        $users = User::withTrashed()->orderBy('created_at', 'desc')->get();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource (Create Form).
     */
    public function create()
    {
        $roles = [
            'pelamar'     => 'Pelamar',
            'perusahaan'  => 'Perusahaan',
            'admin'       => 'Super Admin',
        ];

        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage (Create).
     */
    public function store(StoreUpdateUserRequest $request)
    {
        DB::transaction(function () use ($request) {
            $validated = $request->validated();

            // Buat user utama
            $user = User::create([
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            // Assign role
            $user->assignRole($validated['role']);

            // Jika role pelamar
            if ($validated['role'] === 'pelamar') {
                $user->personalPelamar()->create([
                    'user_id'              => $user->id,
                    'name_pelamar'         => $validated['name'],
                    'email_pelamar'        => $validated['email'],
                    'phone_pelamar'        => $validated['phone_pelamar'] ?? null,
                    'city_pelamar'         => $validated['city_pelamar'] ?? null,
                    'gender'               => $validated['gender'] ?? null,
                    'date_of_birth_pelamar' => $validated['date_of_birth_pelamar'] ?? null,
                ]);
            }

            // Jika role perusahaan
            if ($validated['role'] === 'perusahaan') {
                $user->personalCompany()->create([
                    'user_id'           => $user->id,
                    'name_company'      => $validated['name'],
                    'email_company'     => $validated['email'],
                    'phone_company'     => $validated['phone_company'] ?? null,
                    'city_company'      => $validated['city_company'] ?? null,
                    'type_of_company'   => $validated['type_of_company'] ?? null,
                    'name_user_company' => $validated['name_user_company'] ?? null,
                ]);
            }
        });

        return redirect()->route('admin.users.index')->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource (Edit Form).
     */
    public function edit(User $user)
    {
        $roles = [
            'pelamar'     => 'Pelamar',
            'perusahaan'  => 'Perusahaan',
            'admin'       => 'Super Admin',
        ];

        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage (Update).
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',

            // tambahan
            'phone'               => 'nullable|string|max:20',
            'gender'              => 'nullable|in:laki-laki,perempuan',
            'date_of_birth_pelamar' => 'nullable|date',
            'city'                => 'nullable|string|max:255',
            'type_of_company'     => 'nullable|string|max:255',
            'name_user_company'   => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($validated, $user) {
            $data = [
                'name'  => $validated['name'],
                'email' => $validated['email'],
            ];

            if (!empty($validated['password'])) {
                $data['password'] = Hash::make($validated['password']);
            }

            $user->update($data);

            // update detail pelamar
            if ($user->hasRole('pelamar')) {
                $user->personalPelamar()->updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'name_pelamar'         => $validated['name'],
                        'email_pelamar'        => $validated['email'],
                        'phone_pelamar'        => $validated['phone'] ?? null,
                        'city_pelamar'         => $validated['city'] ?? null,
                        'gender'               => $validated['gender'] ?? null,
                        'date_of_birth_pelamar' => $validated['date_of_birth_pelamar'] ?? null,
                    ]
                );
            }

            // update detail perusahaan
            if ($user->hasRole('perusahaan')) {
                $user->personalCompany()->updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'name_company'     => $validated['name'],
                        'email_company'    => $validated['email'],
                        'phone_company'    => $validated['phone'] ?? null,
                        'city_company'     => $validated['city'] ?? null,
                        'type_of_company'  => $validated['type_of_company'] ?? null,
                        'name_user_company' => $validated['name_user_company'] ?? null,
                    ]
                );
            }
        });

        return redirect()->route('admin.users.index')->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage (Delete).
     */
    // public function destroy(User $user)
    // {
    //     DB::transaction(function () use ($user) {
    //         $user->delete();
    //     });

    //     return redirect()->route('admin.users.index')->with('success', 'User berhasil dihapus.');
    // }

    public function destroy(User $user)
    {
        DB::transaction(function () use ($user) {
            $user->delete(); // ini cuma mengisi deleted_at
        });

        return redirect()->route('admin.users.index')->with('success', 'User berhasil dinonaktifkan.');
    }

    public function restore($id)
    {
        // Ambil user termasuk yang soft deleted
        $user = User::withTrashed()->findOrFail($id);

        // Restore user
        $user->restore();

        return redirect()->route('admin.users.index')->with('success', 'User berhasil diaktifkan kembali.');
    }
}
