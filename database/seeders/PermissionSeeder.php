<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        Permission::create(['name' => 'edit gis']);
        Permission::create(['name' => 'delete gis']);
        Permission::create(['name' => 'create gis']);
        Permission::create(['name' => 'view gis']);

        Permission::create(['name' => 'edit absen']);
        Permission::create(['name' => 'delete absen']);
        Permission::create(['name' => 'create absen']);
        Permission::create(['name' => 'view absen']);

        Permission::create(['name' => 'edit presensi']);
        Permission::create(['name' => 'delete presensi']);
        Permission::create(['name' => 'create presensi']);
        Permission::create(['name' => 'view presensi']);
        
        Permission::create(['name' => 'edit presensi-user']);
        Permission::create(['name' => 'delete presensi-user']);
        Permission::create(['name' => 'create presensi-user']);
        Permission::create(['name' => 'view presensi-user']);

        Permission::create(['name' => 'edit finger']);
        Permission::create(['name' => 'delete finger']);
        Permission::create(['name' => 'create finger']);
        Permission::create(['name' => 'view finger']);

        Permission::create(['name' => 'edit finger-scan']);
        Permission::create(['name' => 'delete finger-scan']);
        Permission::create(['name' => 'create finger-scan']);
        Permission::create(['name' => 'view finger-scan']);

        Permission::create(['name' => 'edit dinas']);
        Permission::create(['name' => 'delete dinas']);
        Permission::create(['name' => 'create dinas']);
        Permission::create(['name' => 'view dinas']);

        Permission::create(['name' => 'edit dinas-scan']);
        Permission::create(['name' => 'delete dinas-scan']);
        Permission::create(['name' => 'create dinas-scan']);
        Permission::create(['name' => 'view dinas-scan']);

        // create roles and assign existing permissions
        $role1 = Role::create(['name' => 'sdm']);
        $role1->givePermissionTo('edit gis');
        $role1->givePermissionTo('delete gis');
        $role1->givePermissionTo('create gis');
        $role1->givePermissionTo('view gis');

        $role1->givePermissionTo('edit absen');
        $role1->givePermissionTo('delete absen');
        $role1->givePermissionTo('create absen');
        $role1->givePermissionTo('view absen');

        $role1->givePermissionTo('edit presensi');
        $role1->givePermissionTo('delete presensi');
        $role1->givePermissionTo('create presensi');
        $role1->givePermissionTo('view presensi');

        $role1->givePermissionTo('edit presensi-user');
        $role1->givePermissionTo('delete presensi-user');
        $role1->givePermissionTo('create presensi-user');
        $role1->givePermissionTo('view presensi-user');

        $role2 = Role::create(['name' => 'karyawan']);
        // $role2->givePermissionTo('edit finger');
        // $role2->givePermissionTo('delete finger');
        $role2->givePermissionTo('create finger');
        $role2->givePermissionTo('view finger');

        // $role2->givePermissionTo('edit finger-scan');
        // $role2->givePermissionTo('delete finger-scan');
        $role2->givePermissionTo('create finger-scan');
        $role2->givePermissionTo('view finger-scan');

        // $role2->givePermissionTo('edit dinas');
        // $role2->givePermissionTo('delete dinas');
        $role2->givePermissionTo('create dinas');
        $role2->givePermissionTo('view dinas');

        // $role2->givePermissionTo('edit dinas-scan');
        // $role2->givePermissionTo('delete dinas-scan');
        $role2->givePermissionTo('create dinas-scan');
        $role2->givePermissionTo('view dinas-scan');

        // $role3 = Role::create(['name' => 'Super-Admin']);
        // gets all permissions via Gate::before rule; see AuthServiceProvider

        $pass = config('app.seeder_default');
        // create demo users
        $user = User::factory()->create([
            'npp' => 'sdm',
            'email' => 'sdm@pindadmedika.com',
            'no_hp' => '08562160040',
            'email_verified_at' => now(),
            'password' => Hash::make($pass), // password
            'remember_token' => Str::random(10),
        ]);
        $user->assignRole($role1);

        $user = User::factory()->create([
            'npp' => '12503',
            'email' => 'rizky.rizky@pindadmedika.com',
            'no_hp' => '08562160039',
            'email_verified_at' => now(),
            'password' => Hash::make($pass), // password
            'remember_token' => Str::random(10),
        ]);
        $user->assignRole($role2);

        $user = User::factory()->create([
            'npp' => '12504',
            'email' => 'axel@pindadmedika.com',
            'no_hp' => '08562160038',
            'email_verified_at' => now(),
            'password' => Hash::make($pass), // password
            'remember_token' => Str::random(10),
        ]);
        $user->assignRole($role2);
        $user = User::factory()->create([
            'npp' => '12505',
            'email' => 'adits@pindadmedika.com',
            'no_hp' => '08562160037',
            'email_verified_at' => now(),
            'password' => Hash::make($pass), // password
            'remember_token' => Str::random(10),
        ]);
        $user->assignRole($role2);

        // $user = User::factory()->create([
        //     'npp' => 'spadmin',
        //     'email' => 'spadmin@pindadmedika.com',
        // ]);
        // $user->assignRole($role3);
    }
}
