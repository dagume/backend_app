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
        'is_visible'   => true,
        ]);

        Role::create([
        'name'          => 'Residente',
        'slug'          => 'residente',
        'is_visible'   => true,
        ]);

        Role::create([
        'name'          => 'Contador',
        'slug'          => 'contador',
        'is_visible'   => true,
        ]);

        Role::create([
        'name'          => 'Cliente',
        'slug'          => 'cliente',
        'is_visible'   => true,
        ]);

        Role::create([
        'name'          => 'Socio',
        'slug'          => 'socio',
        'is_visible'   => true,
        ]);
        Role::create([
        'name'          => 'Banco',
        'slug'          => 'banco',
        'special'       => 'no-access',
        'is_visible'   => true,
        ]);
        Role::create([
        'name'          => 'Prestamista',
        'slug'          => 'prestamista',
        'special'       => 'no-access',
        'is_visible'   => true,
        ]);
        Role::create([
        'name'          => 'Proveedor',
        'slug'          => 'proveedor',
        'special'       => 'no-access',
        'is_visible'   => true,
        ]);
        Role::create([
        'name'          => 'Proyecto',
        'slug'          => 'Proyecto',
        'special'       => 'no-access',
        'is_visible'   => false,
        ]);
    }
}
