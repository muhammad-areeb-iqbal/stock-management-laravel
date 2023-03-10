<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IngredientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('ingredients')->insert([
            [ 'name' => "Beef", 'stock_in_gram' => 20000, 'stock_capacity_in_gram' => 20000, 'created_at' => config("common.current_date_time"), 'updated_at' => config("common.current_date_time") ],
            [ 'name' => "Cheese", 'stock_in_gram' => 5000, 'stock_capacity_in_gram' => 5000, 'created_at' => config("common.current_date_time"), 'updated_at' => config("common.current_date_time") ],
            [ 'name' => "Onion", 'stock_in_gram' => 1000, 'stock_capacity_in_gram' => 1000, 'created_at' => config("common.current_date_time"), 'updated_at' => config("common.current_date_time") ],
            [ 'name' => "Chicken", 'stock_in_gram' => 20000, 'stock_capacity_in_gram' => 20000, 'created_at' => config("common.current_date_time"), 'updated_at' => config("common.current_date_time") ],
            [ 'name' => "Tomato", 'stock_in_gram' => 1000, 'stock_capacity_in_gram' => 1000, 'created_at' => config("common.current_date_time"), 'updated_at' => config("common.current_date_time") ],
        ]);
    }
}
