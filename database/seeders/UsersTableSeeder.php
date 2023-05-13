<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('users')->delete();
        
        \DB::table('users')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Dodot Gwapo',
                'username' => 'dodot',
                'phone_no' => '09163377896',
                'email' => 'dodot@gmail.com',
                'points' => 1000.00,
                'email_verified_at' => NULL,
                'password' => bcrypt('123456'),
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Admin',
                'username' => 'admin',
                'phone_no' => '09163377896',
                'email' => 'admin@gmail.com',
                'points' => 0.0,
                'email_verified_at' => NULL,
                'password' => bcrypt('123456'),
                'remember_token' => NULL,
                'created_at' => '2023-04-24 08:09:44',
                'updated_at' => '2023-04-29 17:51:25',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Operator',
                'username' => 'operator',
                'phone_no' => '09163377896',
                'email' => 'operator@gmail.com',
                'points' => 0.0,
                'email_verified_at' => NULL,
                'password' => bcrypt('123456'),
            ),
        ));
        
        
    }
}