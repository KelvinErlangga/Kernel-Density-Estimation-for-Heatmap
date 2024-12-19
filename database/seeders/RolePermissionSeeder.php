<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            'manage users',
            'manage jobs',
            'view jobs',
            'apply jobs',
        ];

        // Buat permission jika belum ada
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Admin memiliki semua permission
        $admin_role = Role::firstOrCreate(['name' => 'admin']);
        $admin_role->syncPermissions($permissions);

        // Pelamar hanya bisa melihat dan melamar pekerjaan
        $pelamar = Role::firstOrCreate(['name' => 'pelamar']);
        $pelamar->syncPermissions(['view jobs', 'apply jobs']);

        // Perusahaan bisa mengelola pekerjaan
        $perusahaan = Role::firstOrCreate(['name' => 'perusahaan']);
        $perusahaan->syncPermissions(['manage jobs']);

        // buat akun admin
        $admin = User::create([
            'name' => 'Super Admin',
            'email' => 'super@admin.com',
            'password' => bcrypt('123123123')
        ]);
        $admin->assignRole($admin_role);
    }
}
