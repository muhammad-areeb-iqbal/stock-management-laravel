<?php

namespace Tests\Feature;

use App\Models\Ingredient;
use Tests\TestCase;
use App\Models\ProductDetail;

class OrderTest extends TestCase
{
    /**
     * Running migrations before test run in the separate database
     */
    protected function run_migrations()
    {
        $this->artisan('migrate:fresh');
        $this->artisan('db:seed');
    }

    public function test_run_migrations()
    {
        $this->run_migrations();
        $this->assertTrue(true);
    }

    /**
     * A Complete order test, including order storage and making sure
     * Ingredient data updated correctly w.r.t order
     */
    public function test_store_order_ingredient()
    {
        Ingredient::where(['id' => 2])->update(['stock_in_gram' => 5000]);

        $products = [
            "products" => [
                0 => [
                    'product_id' => 1,
                    'quantity' => 2
                ],
                1 => [
                    'product_id' => 2,
                    'quantity' => 1
                ]
            ]
        ];

        $product_detail = ProductDetail::whereIn('product_id', [1,2])->get();
        $arr = [];
        foreach($product_detail as $val)
        {
            $product = $products['products'][$val['product_id']-1]['quantity'];
            if(isset($arr[$val['ingredient_id']]))
                $arr[$val['ingredient_id']] += $val['quantity_use']*$product;
            else
                $arr[$val['ingredient_id']] = $val['quantity_use']*$product;

        }

        $ing = Ingredient::whereIn('id', array_keys($arr))->get()->keyBy('id');

        $response = $this->post('api/v1/order', $products);

        $ing_upd = $ing->fresh();

        $same = true;
        foreach($ing_upd as $key => $val)
        {
            if( ($ing_upd[$key]['stock_in_gram'] + $arr[$key]) != $ing[$key]['stock_in_gram'] )
            {
                $same = false;
                break;
            }
        }

        $same == true ? $this->assertTrue(true) : $this->assertTrue(false);

        $response->assertJson([
            'data' =>[0 => [
                'key' => true
            ], 1 => [ 'key' => true ]]
        ])->assertJsonPath('data.0.order_status', 'completed')
        ->assertJsonPath('data.1.order_status', 'completed')
        ->assertStatus(200);
    }

    /**
     * Email testing if any ingredient remain below 50% of any product
     */
    public function test_email()
    {
        Ingredient::where(['id' => 1])->update(['stock_in_gram' => 10000]);
        $products = [
            "products" => [
                0 => [
                    'product_id' => 1,
                    'quantity' => 2
                ],
                1 => [
                    'product_id' => 2,
                    'quantity' => 1
                ]
            ]
        ];

        $response = $this->post('api/v1/order', $products);
        $ing_data = Ingredient::where('id',1)->get();

        $ing_data[0]->is_email == true ? $this->assertTrue(true): $this->assertTrue(false);

        $response->assertJson([
            'data' =>[0 => [
                'key' => true
            ], 1 => [ 'key' => true ]]
        ])->assertJsonPath('data.0.order_status', 'completed')
        ->assertJsonPath('data.1.order_status', 'completed')
        ->assertStatus(200);
    }
}
