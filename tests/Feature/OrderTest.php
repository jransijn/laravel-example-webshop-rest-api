<?php

namespace Tests\Feature;

use Database\Seeders\TestSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use Spectator\Spectator;
use Tests\AssertOrders;

class OrderTest extends TestCase
{
    use RefreshDatabase;
    use AssertOrders;

    public function setUp(): void {
        parent::setUp();
        Spectator::using('openapi.yml');
    }

    public function test_list_orders()
    {
        $this->seed(TestSeeder::class);

        $this->getJson('/api/orders')
            ->assertValidRequest()
            ->assertValidResponse(200);
    }

    public function test_get_order()
    {
        $this->seed(TestSeeder::class);

        $RANDOM_ORDER_NUMBER = TestSeeder::random_order_number();

        $this->getJson('/api/orders/'.TestSeeder::ORDER_NUMBER)
            ->assertValidRequest()
            ->assertValidResponse(200);

        $this->getJson('/api/orders/'.$RANDOM_ORDER_NUMBER)
            ->assertValidRequest()
            ->assertValidResponse(404);
    }

    public function test_post_order()
    {
        $this->seed(TestSeeder::class);

        $RANDOM_ORDER_NUMBER = TestSeeder::random_order_number();

        $this->postJson('/api/orders', array( 'number' => $RANDOM_ORDER_NUMBER, 'total_amount' => 4.3, 'status' => 'pending' ))
            ->assertValidRequest()
            ->assertValidResponse(201);

            $this->postJson('/api/orders', array( 'number' => $RANDOM_ORDER_NUMBER, 'total_amount' => 4.3, 'status' => 'pending' ))
            ->assertValidRequest()
            ->assertValidResponse(409);

        $this->postJson('/api/orders', array( 'number' => TestSeeder::ORDER_NUMBER, 'total_amount' => 4.3, 'status' => 'pending' ))
            ->assertValidRequest()
            ->assertValidResponse(409);
    }

    public function test_put_order()
    {
        $this->seed(TestSeeder::class);

        $RANDOM_ORDER_NUMBER = TestSeeder::random_order_number();

        $this->putJson('/api/orders/'.TestSeeder::ORDER_NUMBER, array( 'number' => TestSeeder::ORDER_NUMBER, 'total_amount' => 4.3, 'status' => 'pending' ))
            ->assertValidRequest()
            ->assertValidResponse(200);

        $this->putJson('/api/orders/'.$RANDOM_ORDER_NUMBER, array( 'number' => $RANDOM_ORDER_NUMBER, 'total_amount' => 4.3, 'status' => 'pending' ))
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
