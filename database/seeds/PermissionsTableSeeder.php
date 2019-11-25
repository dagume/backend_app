<?php

use Illuminate\Database\Seeder;
use Caffeinated\Shinobi\Models\Permission;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //contacts
        Permission::create([
        'name'          => 'Listar contacts',
        'slug'          => 'contact.list',
        'description'   => 'Lista y navega todos los contacts del sistema',
        ]);

        Permission::create([
        'name'          => 'Ver detalle de contact',
        'slug'          => 'contact.show',
        'description'   => 'ver en detalle cada contact del sistema',
        ]);

        Permission::create([
            'name'          => 'Creacion de contact',
            'slug'          => 'contact.create',
            'description'   => 'crear un contact en el sistema',
            ]);

        Permission::create([
        'name'          => 'Edicion de contact',
        'slug'          => 'contact.edit',
        'description'   => 'editar cualquier dato de un contact del sistema',

        ]);
        Permission::create([
        'name'          => 'Eliminar contact',
        'slug'          => 'contact.destroy',
        'description'   => 'Eliminar cualquier contact del sistema',

        ]);

        //projects
        Permission::create([
            'name'          => 'Listar projects',
            'slug'          => 'project.list',
            'description'   => 'Lista y navega tods los projects del sistema',
            ]);

            Permission::create([
            'name'          => 'Ver detalle de project',
            'slug'          => 'project.show',
            'description'   => 'ver en detalle cada project del sistema',
            ]);

            Permission::create([
                'name'          => 'Creacion de project',
                'slug'          => 'project.create',
                'description'   => 'crear un project en el sistema',
                ]);

            Permission::create([
            'name'          => 'Edicion de project',
            'slug'          => 'project.edit',
            'description'   => 'editar cualquier dato de un project del sistema',

            ]);
            Permission::create([
            'name'          => 'Eliminar project',
            'slug'          => 'project.destroy',
            'description'   => 'Eliminar cualquier project del sistema',

            ]);

        //roles
        Permission::create([
        'name'          => 'Listar roles',
        'slug'          => 'role.list',
        'description'   => 'Lista y navega todos los roles del sistema',
        ]);

        Permission::create([
        'name'          => 'Ver detalle del role',
        'slug'          => 'role.show',
        'description'   => 'ver en detalle cada role del sistema',
        ]);

        Permission::create([
            'name'          => 'Creacion de role',
            'slug'          => 'role.create',
            'description'   => 'crear un role en el sistema',
            ]);

        Permission::create([
        'name'          => 'Edicion de role',
        'slug'          => 'role.edit',
        'description'   => 'editar cualquier dato de un role del sistema',

        ]);
        Permission::create([
        'name'          => 'Eliminar role',
        'slug'          => 'role.destroy',
        'description'   => 'Eliminar cualquier role del sistema',

        ]);

        //products
        Permission::create([
        'name'          => 'Listar products',
        'slug'          => 'product.list',
        'description'   => 'Lista y navega todos los products del sistema',
        ]);

        Permission::create([
        'name'          => 'Ver detalle del product',
        'slug'          => 'product.show',
        'description'   => 'ver en detalle cada product del sistema',
        ]);

        Permission::create([
            'name'          => 'Creacion de product',
            'slug'          => 'product.create',
            'description'   => 'crear un product en el sistema',
            ]);

        Permission::create([
        'name'          => 'Edicion de product',
        'slug'          => 'product.edit',
        'description'   => 'editar cualquier dato de un product del sistema',

        ]);
        Permission::create([
        'name'          => 'Eliminar product',
        'slug'          => 'product.destroy',
        'description'   => 'Eliminar cualquier product del sistema',

        ]);

        //categories
        Permission::create([
        'name'          => 'Listar categories',
        'slug'          => 'category.list',
        'description'   => 'Lista y navega todos los categories del sistema',
        ]);

        Permission::create([
        'name'          => 'Ver detalle del category',
        'slug'          => 'category.show',
        'description'   => 'ver en detalle cada category del sistema',
        ]);

        Permission::create([
            'name'          => 'Creacion de category',
            'slug'          => 'category.create',
            'description'   => 'crear una category en el sistema',
            ]);

        Permission::create([
        'name'          => 'Edicion de category',
        'slug'          => 'category.edit',
        'description'   => 'editar cualquier dato de un category del sistema',

        ]);
        Permission::create([
        'name'          => 'Eliminar category',
        'slug'          => 'category.destroy',
        'description'   => 'Eliminar cualquier category del sistema',

        ]);

        //permission
        Permission::create([
        'name'          => 'Listar permissions',
        'slug'          => 'permission.list',
        'description'   => 'Lista y navega todos los permissions del sistema',
        ]);

        Permission::create([
        'name'          => 'Ver detalle del permission',
        'slug'          => 'permission.show',
        'description'   => 'ver en detalle cada permission del sistema',
        ]);

    }
}

