<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Traits\SelectOptionTrait;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, SelectOptionTrait;

    function responseValidateFailed($validator)
    {
        return response()->json([
            'success' => false,
            'message' => $validator->getMessageBag()->first()
        ], 422);
    }

    function responseValidateSuccess($redirect)
    {
        return response()->json([
            'success' => true,
            'message' => 'ok',
            'redirect' => $redirect
        ]);
    }

    function responseComplete()
    {
        return response()->json([
            'success' => true,
            'message' => 'Complete',
        ]);
    }

    function responseFailed($message = 'Complete')
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ]);
    }

    function responseWithCode($status, $message, $data, $status_code)
    {
        return response()->json([
            'success' => $status,
            'message' => $message,
            'data' => $data
        ], $status_code);
    }

    function trimComma(&$request, $attributes = [])
    {
        foreach ($attributes as $value) {
            $request->merge([$value => transform_float($request->{$value})]);
        }
    }
}