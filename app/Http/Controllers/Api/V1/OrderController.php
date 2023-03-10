<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Services\OrderService;

class OrderController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreOrderRequest $request, OrderService $order_service)
    {
        $result = [];
        //create order in the database first
        $order_status = $order_service->createOrder($request);

        //once order set, now process order toward completion by making product
        if( $order_status )
            $result = $order_service->runOrderProcess();

        return [
            'status' => 1,
            'data' =>  $result
        ];
    }

}
