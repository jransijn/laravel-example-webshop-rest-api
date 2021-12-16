<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'number',
        'total_amount',
        'status',
    ];

    public static function find_from_order_number($order_number): mixed
    {
        return Order::where('number', $order_number)->first();
    }
    public static function find_id_from_order_number($order_number): ?int
    {
        $order = Self::select('id')->where('number', $order_number)->first();
        if (is_null($order))
            return NULL;
        return $order->id;
    }

    public static function exists(string $order_number): bool
    {
        $order_id = Order::find_id_from_order_number($order_number);
        return !is_null($order_id);
    }

}
