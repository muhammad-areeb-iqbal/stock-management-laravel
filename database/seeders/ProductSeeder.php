<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('products')->insert([
            ['name' => "Beef Burger", 'created_at' => config("common.current_date_time"), 'updated_at' => config("common.current_date_time")],
            ['name' => "Chicken Burger", 'created_at' => config("common.current_date_time"), 'updated_at' => config("common.current_date_time")]
        ]);
    }
}
