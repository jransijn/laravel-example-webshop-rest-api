<?php

namespace App\Http\Controllers;

use App\Models\OrderLine;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class OrderLineController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $order_lines = OrderLine::get();
        return response()->json([ 'status' => 'success', 'data' => $order_lines ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $order = new OrderLine;
        $order->order_id = $request->input('order_id');
        $order->barcode = $request->input('barcode');
        $order->quantity = $request->input('quantity');
        $order->save();
        return response()->json([ 'status' => 'success' ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $order_id
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($order_id, $id)
    {
        $order_line = OrderLine::find($id);
        return response()->json([ 'status' => 'success', 'data' => $order_line ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $order_id, $id)
    {
        $order = OrderLine::find($id);
        $order->order_id = $request->input('order_id');
        $order->barcode = $request->input('barcode');
        $order->quantity = $request->input('quantity');
        $order->save();
        return response()->json([ 'status' => 'success' ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        OrderLine::destroy($id);
        return response()->json([ 'status' => 'success' ]);
    }

}
