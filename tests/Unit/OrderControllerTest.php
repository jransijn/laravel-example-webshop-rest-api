<?php

namespace Tests\Unit;

use App\Http\Controllers\OrderController;
use App\Models\Order;
use Database\Seeders\TestSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;
use Tests\ValidatesJsonResponses;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;
    use ValidatesJsonResponses;

    private static function order_exists(string $order_number): bool
    {
        $order = Order::find_id_from_order_number($order_number);
        return !is_null($order);
    }

    public static function assertThatOrderExists(string $order_number)
    {
        static::assertTrue(static::order_exists($order_number));
    }
    public static function assertThatOrderDoesNotExists(string $order_number)
    {
        static::assertFalse(static::order_exists($order_number));
    }

    public static function validate_order(mixed $order)
    {
        static::assertObjectHasAttribute('id', $order);
        static::assertObjectHasAttribute('number', $order);
        static::assertObjectHasAttribute('total_amount', $order);
        static::assertObjectHasAttribute('status', $order);
        static::assertIsInt($order->id);
        static::assertIsString($order->number);
        static::assertIsNumeric($order->total_amount);
        static::assertContains($order->status, [ 'pending', 'paid', 'shipped' ]);
    }

    /**
     * Test OrderController::index
     *
     * @return void
     */
    public function test_index()
    {
        $this->seed(TestSeeder::class);
        $request = Request::create('/api/orders', 'GET');
        $controller = new OrderController;
        $response = $controller->index($request);
        $data = $this->validate_response_success($response);
        foreach ($data as $order) {
            $this->validate_order($order);
        }
    }

    /**
     * Test the pagination of OrderController::index
     *
     * @return void
     */
    public function test_index_pagination()
    {
        $this->seed(TestSeeder::class);
        $controller = new OrderController;
        $orders = $controller->index(Request::create('/api/orders', 'GET'))->getData()->data;

        $request = Request::create('/api/orders', 'GET', [ 'limit' => 1 ]);
        $controller = new OrderController;
        $response = $controller->index($request);
        $data = $this->validate_response_success($response);
        $this->assertEquals(count($data), 1);
        $this->assertEquals($data[0], $orders[0]);
        $this->validate_order($data[0]);

        $request = Request::create('/api/orders', 'GET', [ 'start' => 1, 'limit' => 1 ]);
        $controller = new OrderController;
        $response = $controller->index($request);
        $data = $this->validate_response_success($response);
        $this->assertEquals(count($data), 1);
        $this->assertEquals($data[0], $orders[1]);
        $this->validate_order($data[0]);

        $request = Request::create('/api/orders', 'GET', [ 'limit' => 2 ]);
        $controller = new OrderController;
        $response = $controller->index($request);
        $data = $this->validate_response_success($response);
        $this->assertEquals(count($data), 2);
        $this->assertEquals($data[0], $orders[0]);
        $this->assertEquals($data[1], $orders[1]);
        $this->validate_order($data[0]);

        $request = Request::create('/api/orders', 'GET', [ 'start' => 2, 'limit' => 2 ]);
        $controller = new OrderController;
        $response = $controller->index($request);
        $data = $this->validate_response_success($response);
        $this->assertEquals(count($data), 2);
        $this->assertEquals($data[0], $orders[2]);
        $this->assertEquals($data[1], $orders[3]);
        $this->validate_order($data[0]);
    }

    /**
     * Test OrderController::show
     *
     * @return void
     */
    public function test_show()
    {
        $this->seed(TestSeeder::class);

        $RANDOM_ORDER_NUMBER = TestSeeder::random_order_number();

        $request = Request::create('/api/orders/'.$RANDOM_ORDER_NUMBER, 'GET');
        $controller = new OrderController;
        $response = $controller->show($request, $RANDOM_ORDER_NUMBER);
        $order = $this->validate_response_failure($response);

        $request = Request::create('/api/orders/'.TestSeeder::ORDER_NUMBER, 'GET');
        $controller = new OrderController;
        $response = $controller->show($request, TestSeeder::ORDER_NUMBER);
        $order = $this->validate_response_success($response);
        $this->validate_order($order);
    }

    /**
     * Test OrderController::store
     */
    public function test_store()
    {
        $this->seed(TestSeeder::class);

        $RANDOM_ORDER_NUMBER = TestSeeder::random_order_number();

        $this->assertThatOrderDoesNotExists($RANDOM_ORDER_NUMBER);

        $request = Request::create('/api/orders/'.$RANDOM_ORDER_NUMBER, 'POST', [ 'number' => $RANDOM_ORDER_NUMBER, 'total_amount' => 42.5, 'status' => 'pending' ]);
        $controller = new OrderController;
        $response = $controller->store($request, $RANDOM_ORDER_NUMBER);
        $this->validate_response_success($response, false, 201);

        $this->assertThatOrderExists($RANDOM_ORDER_NUMBER);

        $request = Request::create('/api/orders/'.$RANDOM_ORDER_NUMBER, 'POST', [ 'number' => $RANDOM_ORDER_NUMBER, 'total_amount' => 42.5, 'status' => 'pending' ]);
        $controller = new OrderController;
        $response = $controller->store($request, $RANDOM_ORDER_NUMBER);
        $this->validate_response_failure($response, 409);
    }

    /**
     * Test OrderController::update
     */
    public function test_update()
    {
        $this->seed(TestSeeder::class);

        $RANDOM_ORDER_NUMBER = TestSeeder::random_order_number();

        $this->assertThatOrderDoesNotExists($RANDOM_ORDER_NUMBER);

        $request = Request::create('/api/orders/'.$RANDOM_ORDER_NUMBER, 'PUT', [ 'number' => $RANDOM_ORDER_NUMBER, 'total_amount' => 42.5, 'status' => 'pending' ]);
        $controller = new OrderController;
        $response = $controller->update($request, $RANDOM_ORDER_NUMBER);
        $this->validate_response_failure($response);

        $request = Request::create('/api/orders/'.TestSeeder::ORDER_NUMBER, 'PUT', [ 'number' => TestSeeder::ORDER_NUMBER, 'total_amount' => 42.5, 'status' => 'pending' ]);
        $controller = new OrderController;
        $response = $controller->update($request, TestSeeder::ORDER_NUMBER);
        $this->validate_response_success($response, false);
    }

    /**
     * Test OrderController::destroy
     */
    public function test_destroy()
    {
        $this->seed(TestSeeder::class);

        $this->assertThatOrderExists(TestSeeder::ORDER_NUMBER);

        $request = Request::create('/api/orders/'.TestSeeder::ORDER_NUMBER, 'DELETE');
        $controller = new OrderController;
        $response = $controller->destroy($request, TestSeeder::ORDER_NUMBER);
        $this->validate_response_success($response, false);

        $this->assertThatOrderDoesNotExists(TestSeeder::ORDER_NUMBER);
    }
}
