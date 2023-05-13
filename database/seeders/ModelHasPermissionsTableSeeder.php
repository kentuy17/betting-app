<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ModelHasPermissionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('model_has_permissions')->delete();
        
        \DB::table('model_has_permissions')->insert(array (
            0 => 
            array (
                'permission_id' => 1,
                'model_type' => 'App\\Models\\User',
                'model_id' => 2,
            ),
            1 => 
            array (
                'permission_id' => 2,
                'model_type' => 'App\\Models\\User',
                'model_id' => 2,
            ),
            2 => 
            array (
                'permission_id' => 3,
                'model_type' => 'App\\Models\\User',
                'model_id' => 2,
            ),
            3 => 
            array (
                'permission_id' => 4,
                'model_type' => 'App\\Models\\User',
                'model_id' => 2,
            ),
            4 => 
            array (
                'permission_id' => 5,
                'model_type' => 'App\\Models\\User',
                'model_id' => 2,
            ),
            5 => 
            array (
                'permission_id' => 6,
                'model_type' => 'App\\Models\\User',
                'model_id' => 2,
            ),
            6 => 
            array (
                'permission_id' => 7,
                'model_type' => 'App\\Models\\User',
                'model_id' => 2,
            ),
            7 => 
            array (
                'permission_id' => 8,
                'model_type' => 'App\\Models\\User',
                'model_id' => 2,
            ),
        ));
        
        
    }
}