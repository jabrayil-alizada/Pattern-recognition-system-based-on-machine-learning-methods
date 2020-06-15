<?php

namespace App\Util\Helper;

class Response {

    public static function prepareUtf8JsonResponse($data)
    {
        return response()->json(
            $data,
            200,
            ['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'],
            JSON_UNESCAPED_UNICODE
        );
    }

}
