<?php

use Illuminate\Database\Seeder;
use Caffeinated\Shinobi\Models\Role;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

       Role::create([
        'name'          => 'Admnistrador',
        'slug'          => 'admnistrador',
        'special'   => 'all-access',
        ]);

        Role::create([
        'name'          => 'Ingeniero Residente',
        'slug'          => 'ingenieroResidente',
        ]);

        Role::create([
        'name'          => 'Contador',
        'slug'          => 'contador',
        ]);

        Role::create([
        'name'          => 'Cliente',
        'slug'          => 'cliente',
        ]);
        Role::create([
        'name'          => 'Socio',
        'slug'          => 'socio',
        ]);
        Role::create([
        'name'          => 'Banco',
        'slug'          => 'banco',
        'special'       => 'no-access',
        ]);
        Role::create([
        'name'          => 'Prestamista',
        'slug'          => 'prestamista',
        'special'       => 'no-access',
        ]);
        Role::create([
        'name'          => 'Proveedor',
        'slug'          => 'proveedor',
        'special'       => 'no-access',
        ]);
    }
}
