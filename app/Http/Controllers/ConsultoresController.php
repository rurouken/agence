<?php

namespace App\Http\Controllers;

use App\Actions\GetConsultores;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ConsultoresController extends Controller
{
    public function index(Request $request, GetConsultores $action): Collection
    {
        try {
            $collection = $action->exec($request->all());
        } catch (Exception $ex) {
            Log::error($ex->getMessage(), [
                'exception' => $ex,
            ]);

            return response()->json([
                'data' => [
                    'status' => 'error',
                    'message' => 'Something has happened. Please, try again later.'
                ]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $collection;
    }
}
