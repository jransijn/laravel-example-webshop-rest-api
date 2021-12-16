<?php

namespace Tests;

use App\Models\Order;
use App\Models\OrderLine;

trait AssertOrders
{
    public static function assertThatOrderExists(string $order_number)
    {
        static::assertTrue(Order::exists($order_number), 'Expected order to exist');
    }
    public static function assertThatOrderDoesNotExists(string $order_number)
    {
        static::assertFalse(Order::exists($order_number), 'Expected order not to exist');
    }

    public static function assertThatOrderLineExists(string $order_number, string $barcode)
    {
        static::assertTrue(OrderLine::exists($order_number, $barcode), 'Expected order line to exist');
    }
    public static function assertThatOrderLineDoesNotExists(string $order_number, string $barcode)
    {
        static::assertFalse(OrderLine::exists($order_number, $barcode), 'Expected order line not to exist');
    }
}
