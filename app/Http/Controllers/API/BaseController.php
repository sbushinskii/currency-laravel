<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;

class BaseController extends Controller
{
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendResponse($data)
    {
    	$response = [
            'status' => 'success',
            'code' => 200,
            'data'    => $data,
        ];
        return response()->json($response, 200);
    }


    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError($error, $code = 403)
    {
    	$response = [
            'status' => 'error',
            'message' => $error,
        ];

        return response()->json($response, $code);
    }
}
