<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('roles')->delete();
        
        \DB::table('roles')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Admin',
                'guard_name' => 'web',
                'created_at' => '2023-04-24 08:09:44',
                'updated_at' => '2023-04-24 08:09:44',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Player',
                'guard_name' => 'web',
                'created_at' => '2023-04-24 08:09:44',
                'updated_at' => '2023-04-24 08:09:44',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Operator',
                'guard_name' => 'web',
                'created_at' => '2023-04-24 08:09:44',
                'updated_at' => '2023-04-24 08:09:44',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'Super-admin',
                'guard_name' => 'web',
                'created_at' => '2023-04-24 08:41:59',
                'updated_at' => '2023-04-24 08:41:59',
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'Auditor',
                'guard_name' => 'web',
                'created_at' => '2023-04-24 08:09:44',
                'updated_at' => '2023-04-24 08:09:44',
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'Cash-out Operator',
                'guard_name' => 'web',
                'created_at' => '2023-04-24 08:09:44',
                'updated_at' => '2023-04-24 08:09:44',
            ),
            3 => 
            array (
                'id' => 7,
                'name' => 'Cash-in Operator',
                'guard_name' => 'web',
                'created_at' => '2023-04-24 08:41:59',
                'updated_at' => '2023-04-24 08:41:59',
            ),
        ));
        
        
    }
}