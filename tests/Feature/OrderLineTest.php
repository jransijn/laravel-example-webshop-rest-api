<?php

namespace Tests\Feature;

use Database\Seeders\TestSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use Spectator\Spectator;
use Tests\AssertOrders;

class OrderLineTest extends TestCase
{
    use RefreshDatabase;
    use AssertOrders;

    public function setUp(): void {
        parent::setUp();
        Spectator::using('openapi.yml');
    }

    public function test_list_order_lines()
    {
        $this->seed(TestSeeder::class);

        $this->getJson('/api/orders/'.TestSeeder::ORDER_NUMBER)
            ->assertValidRequest()
            ->assertValidResponse(200);
    }

    public function test_get_order()
    {
        $this->seed(TestSeeder::class);

        $RANDOM_ORDER_NUMBER = TestSeeder::random_order_number();
        $RANDOM_ORDERLINE_BARCODE = TestSeeder::random_orderline_barcode();

        $this->getJson('/api/orders/'.TestSeeder::ORDER_NUMBER.'/order_lines/'.TestSeeder::ORDER_LINE_BARCODE_1)
            ->assertValidRequest()
            ->assertValidResponse(200);

        $this->getJson('/api/orders/'.$RANDOM_ORDER_NUMBER.'/order_lines/'.TestSeeder::ORDER_LINE_BARCODE_1)
            ->assertValidRequest()
            ->assertValidResponse(404);

        $this->getJson('/api/orders/'.TestSeeder::ORDER_NUMBER.'/order_lines/'.$RANDOM_ORDERLINE_BARCODE)
            ->assertValidRequest()
            ->assertValidResponse(404);

            $this->getJson('/api/orders/'.$RANDOM_ORDER_NUMBER.'/order_lines/'.$RANDOM_ORDERLINE_BARCODE)
            ->assertValidRequest()
            ->assertValidResponse(404);
    }

    public function test_post_order()
    {
        $this->seed(TestSeeder::class);

        $RANDOM_ORDER_NUMBER = TestSeeder::random_order_number();
        $RANDOM_ORDERLINE_BARCODE = TestSeeder::random_orderline_barcode();

        $this->postJson('/api/orders/'.TestSeeder::ORDER_NUMBER.'/order_lines', array( 'barcode' => $RANDOM_ORDERLINE_BARCODE, 'quantity' => 4 ))
            ->assertValidRequest()
            ->assertValidResponse(201);

        $this->postJson('/api/orders/'.TestSeeder::ORDER_NUMBER.'/order_lines', array( 'barcode' => $RANDOM_ORDERLINE_BARCODE, 'quantity' => 4 ))
            ->assertValidRequest()
            ->assertValidResponse(409);

        $this->postJson('/api/orders/'.$RANDOM_ORDER_NUMBER.'/order_lines', array( 'barcode' => $RANDOM_ORDERLINE_BARCODE, 'quantity' => 4 ))
            ->assertValidRequest()
            ->assertValidResponse(404);

        $this->postJson('/api/orders/'.TestSeeder::ORDER_NUMBER.'/order_lines', array( 'barcode' => TestSeeder::ORDER_LINE_BARCODE_1, 'quantity' => 4 ))
            ->assertValidRequest()
            ->assertValidResponse(409);
    }

    public function test_put_order()
    {
        $this->seed(TestSeeder::class);

        $RANDOM_ORDER_NUMBER = TestSeeder::random_order_number();
        $RANDOM_ORDERLINE_BARCODE = TestSeeder::random_orderline_barcode();

        $this->putJson('/api/orders/'.TestSeeder::ORDER_NUMBER.'/order_lines/'.TestSeeder::ORDER_LINE_BARCODE_1, array( 'barcode' => TestSeeder::ORDER_LINE_BARCODE_1, 'quantity' => 4 ))
            ->assertValidRequest()
            ->assertValidResponse(200);

        $this->putJson('/api/orders/'.TestSeeder::ORDER_NUMBER.'/order_lines/'.$RANDOM_ORDERLINE_BARCODE, array( 'barcode' => $RANDOM_ORDERLINE_BARCODE, 'quantity' => 4 ))
            ->assertValidRequest()
            ->assertValidResponse(404);

        $this->putJson('/api/orders/'.$RANDOM_ORDER_NUMBER.'/order_lines/'.$RANDOM_ORDERLINE_BARCODE, array( 'barcode' => $RANDOM_ORDERLINE_BARCODE, 'quantity' => 4 ))
            ->assertValidRequest()
            ->assertValidResponse(404);
    }

    public function test_delete_order()
    {
        $this->seed(TestSeeder::class);

        $RANDOM_ORDER_NUMBER = TestSeeder::random_order_number();

        $this->delete('/api/orders/'.TestSeeder::ORDER_NUMBER)
            ->assertValidRequest()
            ->assertValidResponse(200);

        $this->delete('/api/orders/'.$RANDOM_ORDER_NUMBER)
            ->assertValidRequest()
            ->assertValidResponse(200);
    }
}
