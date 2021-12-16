<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderLine;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class OrderLineController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param string  $order_number
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, string $order_number): JsonResponse
    {
        $validatedData = $request->validate([
            'start' => ['integer'],
            'limit' => ['integer'],
        ]);
        $start = $validatedData['start'] ?? 0;
        $limit = $validatedData['limit'] ?? 15;
        $order_id = Order::find_id_from_order_number($order_number);
        if (is_null($order_id))
            return response()->json([ 'status' => 'failure', 'data' => array(), 'reason' => 'RESOURCE_NOT_FOUND' ], 404);
        $order_lines = OrderLine::where('order_id', $order_id)->skip($start)->take($limit)->get();
        if (is_null($order_lines))
            return response()->json([ 'status' => 'failure', 'data' => array(), 'reason' => 'RESOURCE_NOT_FOUND' ], 404);
        return response()->json([ 'status' => 'success', 'data' => $order_lines->values() ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param string  $order_number
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function store(Request $request, string $order_number): JsonResponse
    {
        $validatedData = $request->validate([
            'barcode' => ['string'],
            'quantity' => ['integer'],
        ]);
        $order_id = Order::find_id_from_order_number($order_number);
        if (is_null($order_id))
            return response()->json([ 'status' => 'failure', 'reason' => 'RESOURCE_NOT_FOUND' ], 404);
        $barcode = $request->input('barcode');
        $order_line = OrderLine::find_by_order_id_and_barcode($order_id, $barcode);
        if (!is_null($order_line))
            return response()->json([ 'status' => 'failure', 'reason' => 'RESOURCE_ALREADY_EXISTS' ], 409);
        $order_line = new OrderLine;
        $order_line->order_id = $order_id;
        $order_line->barcode = $barcode;
        $order_line->quantity = $validatedData['quantity'];
        $order_line->save();
        return response()->json([ 'status' => 'success' ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $order_number
     * @param  string  $barcode
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function show(Request $request, string $order_number, string $barcode): JsonResponse
    {
        $order_id = Order::find_id_from_order_number($order_number);
        if (is_null($order_id))
            return response()->json([ 'status' => 'failure', 'reason' => 'RESOURCE_NOT_FOUND' ], 404);
        $order_line = OrderLine::find_by_order_id_and_barcode($order_id, $barcode);
        if (is_null($order_line))
            return response()->json([ 'status' => 'failure', 'data' => NULL, 'reason' => 'RESOURCE_NOT_FOUND' ], 404);
        return response()->json([ 'status' => 'success', 'data' => $order_line ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param string  $order_number
     * @param string  $barcode
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function update(Request $request, string $order_number, string $barcode): JsonResponse
    {
        $validatedData = $request->validate([
            'barcode' => ['string'],
            'quantity' => ['integer'],
        ]);
        $order_id = Order::find_id_from_order_number($order_number);
        if (is_null($order_id))
            return response()->json([ 'status' => 'failure', 'reason' => 'RESOURCE_NOT_FOUND' ], 404);
        $order_line = OrderLine::find_by_order_id_and_barcode($order_id, $barcode);
        if (is_null($order_line))
            return response()->json([ 'status' => 'failure', 'reason' => 'RESOURCE_NOT_FOUND' ], 404);
        $order_line->order_id = $order_id;
        $order_line->barcode = $validatedData['barcode'];
        $order_line->quantity = $validatedData['quantity'];
        $order_line->save();
        return response()->json([ 'status' => 'success' ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $order_id
     * @param  string  $barcode
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function destroy(Request $request, string $order_number, string $barcode): JsonResponse
    {
        $order_id = Order::find_id_from_order_number($order_number);
        $order_line = OrderLine::find_by_order_id_and_barcode($order_id, $barcode);
        OrderLine::destroy($order_line->id);
        return response()->json([ 'status' => 'success' ]);
    }

}
