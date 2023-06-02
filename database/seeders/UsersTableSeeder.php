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
                'active' => false,
                'name' => 'Administrator',
                'username' => 'administrator',
                'phone_no' => '09163377896',
                'email' => 'admin@sww23-go.live',
                'points' => 0.00,
                'email_verified_at' => NULL,
                'password' => bcrypt('123456'),
            ),
        ));
        
        
    }
}