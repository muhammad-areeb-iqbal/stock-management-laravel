<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('product_details')->insert([
            [ 'product_id' => 1, 'ingredient_id' => 1, 'quantity_use' => 150, 'created_at' => config("common.current_date_time"), 'updated_at' => config("common.current_date_time") ],
            [ 'product_id' => 1, 'ingredient_id' => 2, 'quantity_use' => 30, 'created_at' => config("common.current_date_time"), 'updated_at' => config("common.current_date_time") ],
            [ 'product_id' => 1, 'ingredient_id' => 3, 'quantity_use' => 20, 'created_at' => config("common.current_date_time"), 'updated_at' => config("common.current_date_time") ],
            [ 'product_id' => 2, 'ingredient_id' => 4, 'quantity_use' => 150, 'created_at' => config("common.current_date_time"), 'updated_at' => config("common.current_date_time") ],
            [ 'product_id' => 2, 'ingredient_id' => 2, 'quantity_use' => 30, 'created_at' => config("common.current_date_time"), 'updated_at' => config("common.current_date_time") ],
            [ 'product_id' => 2, 'ingredient_id' => 5, 'quantity_use' => 20, 'created_at' => config("common.current_date_time"), 'updated_at' => config("common.current_date_time") ],
        ]);
    }
}
