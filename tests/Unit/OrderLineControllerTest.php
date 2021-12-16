<?php

namespace Tests\Unit;

use App\Http\Controllers\OrderLineController;
use Database\Seeders\TestSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\AssertOrders;
use Tests\TestCase;
use Tests\ValidatesJsonResponses;

class OrderLineControllerTest extends TestCase
{
    use RefreshDatabase;
    use ValidatesJsonResponses;
    use AssertOrders;

    public static function validate_orderline(mixed $order)
    {
        static::assertObjectHasAttribute('id', $order);
        static::assertObjectHasAttribute('barcode', $order);
        static::assertObjectHasAttribute('quantity', $order);
        static::assertIsInt($order->id);
        static::assertIsString($order->barcode);
        static::assertIsInt($order->quantity);
    }

    /**
     * Test OrderLineController::index
     *
     * @return void
     */
    public function test_index()
    {
        $this->seed(TestSeeder::class);
        $request = Request::create('/api/orders', 'GET');
        $controller = new OrderLineController;
        $response = $controller->index($request, TestSeeder::ORDER_NUMBER);
        $data = $this->validate_response_success($response);
        foreach ($data as $order_line) {
            $this->validate_orderline($order_line);
        }
    }

    /**
     * Test the pagination of OrderLineController::index
     *
     * @return void
     */
    public function test_index_pagination()
    {
        $this->seed(TestSeeder::class);
        $controller = new OrderLineController;
        $orders = $controller->index(Request::create('/api/orders', 'GET'), TestSeeder::ORDER_NUMBER)->getData()->data;

        $request = Request::create('/api/orders/'.TestSeeder::ORDER_NUMBER."/order_lines", 'GET', [ 'limit' => 1 ]);
        $controller = new OrderLineController;
        $response = $controller->index($request, TestSeeder::ORDER_NUMBER);
        $data = $this->validate_response_success($response);
        $this->assertEquals(count($data), 1);
        $this->assertEquals($data[0], $orders[0]);
        $this->validate_orderline($data[0]);

        $request = Request::create('/api/orders/'.TestSeeder::ORDER_NUMBER."/order_lines", 'GET', [ 'start' => 1, 'limit' => 1 ]);
        $controller = new OrderLineController;
        $response = $controller->index($request, TestSeeder::ORDER_NUMBER);
        $data = $this->validate_response_success($response);
        $this->assertEquals(count($data), 1);
        $this->assertEquals($data[0], $orders[1]);
        $this->validate_orderline($data[0]);

        $request = Request::create('/api/orders/'.TestSeeder::ORDER_NUMBER."/order_lines", 'GET', [ 'limit' => 2 ]);
        $controller = new OrderLineController;
        $response = $controller->index($request, TestSeeder::ORDER_NUMBER);
        $data = $this->validate_response_success($response);
        $this->assertEquals(count($data), 2);
        $this->assertEquals($data[0], $orders[0]);
        $this->assertEquals($data[1], $orders[1]);
        $this->validate_orderline($data[0]);

        $request = Request::create('/api/orders/'.TestSeeder::ORDER_NUMBER."/order_lines", 'GET', [ 'start' => 2, 'limit' => 2 ]);
        $controller = new OrderLineController;
        $response = $controller->index($request, TestSeeder::ORDER_NUMBER);
        $data = $this->validate_response_success($response);
        $this->assertEquals(count($data), 2);
        $this->assertEquals($data[0], $orders[2]);
        $this->assertEquals($data[1], $orders[3]);
        $this->validate_orderline($data[0]);
    }

    /**
     * Test OrderLineController::show
     *
     * @return void
     */
    public function test_show()
    {
        $this->seed(TestSeeder::class);

        $RANDOM_ORDER_NUMBER = TestSeeder::random_order_number();

        $request = Request::create('/api/orders/'.$RANDOM_ORDER_NUMBER."/order_lines/".TestSeeder::ORDER_LINE_BARCODE_1, 'GET');
        $controller = new OrderLineController;
        $response = $controller->show($request, $RANDOM_ORDER_NUMBER, TestSeeder::ORDER_LINE_BARCODE_1);
        $order = $this->validate_response_failure($response);

        $request = Request::create('/api/orders/'.TestSeeder::ORDER_NUMBER."/order_lines/".TestSeeder::ORDER_LINE_BARCODE_1, 'GET');
        $controller = new OrderLineController;
        $response = $controller->show($request, TestSeeder::ORDER_NUMBER, TestSeeder::ORDER_LINE_BARCODE_1);
        $order = $this->validate_response_success($response);
        $this->validate_orderline($order);
    }

    /**
     * Test OrderLineController::store
     */
    public function test_store()
    {
        $this->seed(TestSeeder::class);

        $RANDOM_ORDERLINE_BARCODE = TestSeeder::random_orderline_barcode();

        $this->assertThatOrderExists(TestSeeder::ORDER_NUMBER);
        $this->assertThatOrderLineDoesNotExists(TestSeeder::ORDER_NUMBER, $RANDOM_ORDERLINE_BARCODE);

        $request = Request::create('/api/orders/'.TestSeeder::ORDER_NUMBER."/order_lines", 'POST', [ 'barcode' => $RANDOM_ORDERLINE_BARCODE, 'quantity' => 4 ]);
        $controller = new OrderLineController;
        $response = $controller->store($request, TestSeeder::ORDER_NUMBER);
        $this->validate_response_success($response, false, 201);

        $this->assertThatOrderExists(TestSeeder::ORDER_NUMBER);
        $this->assertThatOrderLineExists(TestSeeder::ORDER_NUMBER, $RANDOM_ORDERLINE_BARCODE);

        $request = Request::create('/api/orders/'.TestSeeder::ORDER_NUMBER."/order_lines", 'POST', [ 'barcode' => $RANDOM_ORDERLINE_BARCODE, 'quantity' => 4 ]);
        $controller = new OrderLineController;
        $response = $controller->store($request, TestSeeder::ORDER_NUMBER);
        $this->validate_response_failure($response, 409);
    }

    /**
     * Test OrderLineController::store
     */
    public function test_update()
    {
        $this->seed(TestSeeder::class);

        $RANDOM_ORDER_NUMBER = TestSeeder::random_order_number();
        $RANDOM_ORDERLINE_BARCODE = TestSeeder::random_orderline_barcode();

        $this->assertThatOrderDoesNotExists($RANDOM_ORDER_NUMBER);
        $this->assertThatOrderLineDoesNotExists($RANDOM_ORDER_NUMBER, $RANDOM_ORDERLINE_BARCODE);

        $request = Request::create('/api/orders/'.$RANDOM_ORDER_NUMBER."/order_lines/".$RANDOM_ORDERLINE_BARCODE, 'PUT', [ 'barcode' => $RANDOM_ORDERLINE_BARCODE, 'quantity' => 4 ]);
        $controller = new OrderLineController;
        $response = $controller->update($request, $RANDOM_ORDER_NUMBER, TestSeeder::ORDER_LINE_BARCODE_1);
        $this->validate_response_failure($response);

        $this->assertThatOrderExists(TestSeeder::ORDER_NUMBER);
        $this->assertThatOrderLineExists(TestSeeder::ORDER_NUMBER, TestSeeder::ORDER_LINE_BARCODE_1);

        $request = Request::create('/api/orders/'.TestSeeder::ORDER_NUMBER."/order_lines/".$RANDOM_ORDERLINE_BARCODE, 'PUT', [ 'barcode' => $RANDOM_ORDERLINE_BARCODE, 'quantity' => 4 ]);
        $controller = new OrderLineController;
        $response = $controller->update($request, TestSeeder::ORDER_NUMBER, $RANDOM_ORDERLINE_BARCODE);
        $this->validate_response_failure($response);

        $request = Request::create('/api/orders/'.TestSeeder::ORDER_NUMBER."/order_lines/".TestSeeder::ORDER_LINE_BARCODE_1, 'PUT', [ 'barcode' => TestSeeder::ORDER_LINE_BARCODE_1, 'quantity' => 4 ]);
        $controller = new OrderLineController;
        $response = $controller->update($request, TestSeeder::ORDER_NUMBER, TestSeeder::ORDER_LINE_BARCODE_1);
        $this->validate_response_success($response, false);
    }

    /**
     * Test OrderLineController::destroy
     */
    public function test_destroy()
    {
        $this->seed(TestSeeder::class);

        $this->assertThatOrderLineExists(TestSeeder::ORDER_NUMBER, TestSeeder::ORDER_LINE_BARCODE_1);

        $request = Request::create('/api/orders/'.TestSeeder::ORDER_NUMBER."/order_lines/".TestSeeder::ORDER_LINE_BARCODE_1, 'DELETE');
        $controller = new OrderLineController;
        $response = $controller->destroy($request, TestSeeder::ORDER_NUMBER, TestSeeder::ORDER_LINE_BARCODE_1);
        $this->validate_response_success($response, false);

        $this->assertThatOrderLineDoesNotExists(TestSeeder::ORDER_NUMBER, TestSeeder::ORDER_LINE_BARCODE_1);
    }
}
