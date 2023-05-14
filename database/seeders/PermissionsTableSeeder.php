<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('permissions')->delete();
        
        \DB::table('permissions')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'role-list',
                'guard_name' => 'web',
                'created_at' => '2023-04-24 08:11:16',
                'updated_at' => '2023-04-24 08:11:16',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'role-create',
                'guard_name' => 'web',
                'created_at' => '2023-04-24 08:11:16',
                'updated_at' => '2023-04-24 08:11:16',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'role-edit',
                'guard_name' => 'web',
                'created_at' => '2023-04-24 08:11:16',
                'updated_at' => '2023-04-24 08:11:16',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'role-delete',
                'guard_name' => 'web',
                'created_at' => '2023-04-24 08:11:16',
                'updated_at' => '2023-04-24 08:11:16',
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'user-list',
                'guard_name' => 'web',
                'created_at' => '2023-04-24 08:11:16',
                'updated_at' => '2023-04-24 08:11:16',
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'user-create',
                'guard_name' => 'web',
                'created_at' => '2023-04-24 08:11:16',
                'updated_at' => '2023-04-24 08:11:16',
            ),
            6 => 
            array (
                'id' => 7,
                'name' => 'user-edit',
                'guard_name' => 'web',
                'created_at' => '2023-04-24 08:11:16',
                'updated_at' => '2023-04-24 08:11:16',
            ),
            7 => 
            array (
                'id' => 8,
                'name' => 'user-delete',
                'guard_name' => 'web',
                'created_at' => '2023-04-24 08:11:16',
                'updated_at' => '2023-04-24 08:11:16',
            ),
        ));
        
        
    }
}