<?php

namespace Tests;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait ValidatesJsonResponses
{
    public static function validate_response(JsonResponse $response, bool $data=true, ?int $code=200): mixed
    {
        if (!is_null($code)) {
            static::assertEquals($code, $response->getStatusCode());
        }
        static::assertInstanceOf('Illuminate\Http\JsonResponse', $response);
        $json = $response->getData();
        static::assertObjectHasAttribute('status', $json);
        if ($data) {
            static::assertObjectHasAttribute('data', $json);
        }
        static::assertContains($json->status, [ 'success', 'failure' ]);
        return $json;
    }
    public static function validate_response_success(Response $response, bool $data=true, ?int $code=200): mixed
    {
        $json = static::validate_response($response, $data, $code);
        static::assertEquals('success', $json->status);
        if ($data) {
            return $json->data;
        }
        return NULL;
    }
    public static function validate_response_failure(Response $response, ?int $code=404)
    {
        $json = static::validate_response($response, false, $code);
        static::assertEquals('failure', $json->status);
    }
}
