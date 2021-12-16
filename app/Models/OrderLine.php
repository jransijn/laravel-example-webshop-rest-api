<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderLine extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id',
        'barcode',
        'quantity',
    ];

    public static function find_by_order_id_and_barcode($order_id, $barcode)
    {
        return OrderLine::where([ 'order_id' => $order_id, 'barcode' => $barcode ])->first();
    }

    public static function exists(string $order_number, string $barcode): bool
    {
        $order_id = Order::find_id_from_order_number($order_number);
        if (is_null($order_id))
            return false;
        $order = OrderLine::find_by_order_id_and_barcode($order_id, $barcode);
        return !is_null($order);
    }
}
