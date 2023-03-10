<?php

namespace App\Services;

use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Models\ProductDetail;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Models\Ingredient;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use App\Jobs\SendGridEmailJob;
use App\Exceptions\CustomException;

class OrderService
{
    protected $key_string;

    public function createOrder(StoreOrderRequest $request): bool
    {
        return $this->putOrder($request);
    }

    /**
     * Once Order taken and stored in the database, further process to complete
     * a order using product ingredients
     */
    public function runOrderProcess()
    {
        $orders = $this->getOrdersDetail(); //get order details via key

        /**
         * if any product failed due to shortage of ingredients process will roll back
         * but the order still in the db as a pending state to calculate and
         * reporting the loss due to shortage
        */
        DB::beginTransaction();

        foreach($orders as $order)
        {
            try{
                $products = $this->getProductDetail( $order->product_id );

                $order_status = $this->putIngredientsProduct($products, $order->id, $order->quantity);

                if($order_status !== TRUE)
                    DB::rollback();

            }catch(Exception $e){
                DB::rollback();
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }

        $this->updateOrderStatus();
        DB::commit();

        //return updated order
        return $orders->fresh();
    }

    public function getOrdersDetail(): Collection
    {
        return Order::where(["key" => $this->key_string])->get();
    }

    public function putOrder(StoreOrderRequest $request): bool
    {
        $this->key_string = Str::random(config("common.random_string_len"));
        $data = [];
        /**
         * assign a order key for multiple products with in the same order
         * So that order can track easily.
         */
        foreach ($request->safe()->products as $key => $value)
        {
            $data[$key] = $value;
            $data[$key]["key"] = $this->key_string;
            $data[$key]["created_at"] = $data[$key]["updated_at"] = getCurrentDateTime();
        }

        return Order::insert($data);
    }

    public function getProductDetail(int $product_id): Collection
    {
        return ProductDetail::with('ingredients')->where([
            'product_id' => $product_id,
        ])->get();
    }

    public function putIngredientsProduct(Collection $products,int $order_id, int $quantity = 1)
    {

            foreach($products as $product){
                $updated_quantity =  $product->ingredients->stock_in_gram - ( $product->quantity_use * $quantity );

                if( $updated_quantity < 0 ){
                    DB::rollback();
                    throw ValidationException::withMessages(['field_name' => $product->ingredients->name.config("common.out_of_stock_message")], 500);
                }

                $is_email = false;
                $is_low = false;

                if( $updated_quantity < ( ( $product->ingredients->stock_capacity_in_gram * config("common.stock_alert_value") ) / 100 ) ){
                    $is_email = true;
                    $is_low = true;

                    if($product->ingredients->is_email == false){
                        /**
                         * Email sent in the background using db queue
                         *  if the limit exceed below 50%
                         * Sendgrid library using for the emails
                         */
                        $data = [
                            'message' => "\"{$product->ingredients->name}\" ".config("common.email_message_content"),
                            'subject' => "\"{$product->ingredients->name}\" ".config("common.email_subject"),
                        ];
                        SendGridEmailJob::dispatch($data);
                    }
                }

                $update_ingredient = [ "is_email" => $is_email, "is_low" => $is_low, "stock_in_gram" => $updated_quantity ];
                $this->updateIngredientStatus($product->ingredient_id, $update_ingredient);
            }
            return TRUE;
    }

    public function updateIngredientStatus(int $product_id, array $update_ingredient)
    {
        Ingredient::where(['id' => $product_id])->update($update_ingredient);
    }

    public function updateOrderStatus()
    {
        ORDER::where(['key' => $this->key_string])->update(['order_status' => 'completed']);
    }

}

