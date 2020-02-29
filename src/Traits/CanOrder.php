<?php

namespace Varbox\Traits;

use Illuminate\Http\Request;

trait CanOrder
{
    /**
     * Order entity rows based on the order and model specified from request (ajax).
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function order(Request $request)
    {
        if (!$request->ajax()) {
            return response()->json([
                'error' => 'Bad request'
            ], 400);
        }

        $request->validate([
            'model' => 'required',
            'items' => 'required|array'
        ]);

        app($request->input('model'))->setNewOrder(
            array_values($request->input('items'))
        );

        return response()->json([
            'status' => true,
        ]);
    }
}
