<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'start' => ['integer'],
            'limit' => ['integer'],
        ]);
        $start = $validatedData['start'] ?? 0;
        $limit = $validatedData['limit'] ?? 15;
        $orders = Order::get()->skip($start)->take($limit);
        if (is_null($orders))
            return response()->json([ 'status' => 'failure', 'data' => array(), 'reason' => 'RESOURCE_NOT_FOUND' ], 404);
        return response()->json([ 'status' => 'success', 'data' => $orders->values() ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'number' => ['required', 'string'],
            'total_amount' => ['required', 'numeric'],
            'status' => ['required', 'in:pending,paid,shipped'],
        ]);
        $order_id = Order::find_id_from_order_number($validatedData['number']);
        if (!is_null($order_id))
            return response()->json([ 'status' => 'failure', 'reason' => 'RESOURCE_ALREADY_EXISTS' ], 409);
        $order = new Order;
        $order->number = $validatedData['number'];
        $order->total_amount = $validatedData['total_amount'];
        $order->status = $validatedData['status'];
        $order->save();
        return response()->json([ 'status' => 'success' ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $order_number
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, string $order_number): JsonResponse
    {
        $order = Order::find_from_order_number($order_number);
        if (is_null($order))
            return response()->json([ 'status' => 'failure', 'data' => null, 'reason' => 'RESOURCE_NOT_FOUND' ], 404);
        return response()->json([ 'status' => 'success', 'data' => $order ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $order_number
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, string $order_number): JsonResponse
    {
        $validatedData = $request->validate([
            'number' => ['string'],
            'total_amount' => ['required', 'numeric'],
            'status' => ['required', 'in:pending,paid,shipped'],
        ]);
        $order = Order::find_from_order_number($order_number);
        if (is_null($order))
            return response()->json([ 'status' => 'failure', 'reason' => 'RESOURCE_NOT_FOUND' ], 404);
        $order->number = $validatedData['number'];
        $order->total_amount = $validatedData['total_amount'];
        $order->status = $validatedData['status'];
        $order->save();
        return response()->json([ 'status' => 'success' ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $order_number
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, string $order_number): JsonResponse
    {
        $order_id = Order::find_id_from_order_number($order_number);
        Order::destroy($order_id);
        return response()->json([ 'status' => 'success' ]);
    }
}
