<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CentrifugoProxyController extends Controller
{
    public function connect(Request $request): JsonResponse
    {
        return response()->json([
           'result' => [
               'user' => (string) $request->user()->id,
           ]
        ]);
    }
}
