<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Order;
use App\Models\Ingredient;


class ProductTest extends TestCase
{

    /**
     * A basic validation test.
     */
    public function test_validation()
    {
        $products = [
            "products" => [
                0 => [
                    'product_id' => "1d",
                    'quantity' => 2
                ],
                1 => [
                    'product_id' => 2,
                    'quantity' => "1x"
                ]
            ]
        ];
        $response = $this->post('api/v1/order', $products);
        $response->assertJson([
            'data' =>['original' => [
                'error' => true
            ]]
        ])->assertStatus(422);

    }

    /**
     * ingredient shortage test during processing order
     */
    public function test_shortage_ingredients()
    {
        Ingredient::where(['id' => 2])->update(['stock_in_gram' => 20]);
        $total_order_records = Order::count();

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

        $this->assertDatabaseCount('orders', ( $total_order_records+2 ));
        $response->assertJson([
            'data' =>['original' => [
                'error' => true
            ]]
        ])->assertStatus(200);
    }

    /**
     * Any ingredient shortage during processing multiple product
     * of the same order e.g Order has 2 products
     * i) Beef Burger ii) Chicken Burger
     * Cheese fulfill for the Beef Burger but not enough for Chicken Burger,
     * This should validate the order can't completed w.r.t any product or
     * any quantity
     */
    public function test_shortage_ingredients_one_product()
    {
        Ingredient::where(['id' => 2])->update(['stock_in_gram' => 70]);
        $total_order_records = Order::count();

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
        $this->assertDatabaseCount('orders', ( $total_order_records+2 ));
        $response->assertJson([
            'data' =>['original' => [
                'error' => true
            ]]
        ])->assertStatus(200);
    }
}
