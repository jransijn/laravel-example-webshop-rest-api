<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderLine;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TestSeeder extends Seeder
{

    const ORDER_NUMBER = "ac30d3f7-2550-3773-93a6-cc22453ce3cc";
    const ORDER_LINE_BARCODE_1 = "71952e04-18ce-4f9a-9f8b-151853fbce59";
    const ORDER_LINE_BARCODE_2 = "f61fcbac-d26c-43fe-8eb5-1a1edff7b8cd";

    public static function random_order_number(): string
    {
        return Str::uuid()->toString();
    }
    public static function random_orderline_barcode(): string
    {
        return Str::uuid()->toString();
    }

    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        OrderLine::factory(10)->create();

        $order = new Order();
        $order->number = Self::ORDER_NUMBER;
        $order->total_amount = 33.5;
        $order->status = 'paid';
        $order->save();

        $order_line = new OrderLine();
        $order_line->order_id = $order->id;
        $order_line->barcode = Self::ORDER_LINE_BARCODE_1;
        $order_line->quantity = 44;
        $order_line->save();

        $order_line = new OrderLine();
        $order_line->order_id = $order->id;
        $order_line->barcode = Self::ORDER_LINE_BARCODE_2;
        $order_line->quantity = 44;
        $order_line->save();

        for ($i=0; $i < 10; $i++) {
            $order_line = new OrderLine();
            $order_line->order_id = $order->id;
            $order_line->barcode = $this->random_orderline_barcode();
            $order_line->quantity = 44;
            $order_line->save();
        }
    }

}
