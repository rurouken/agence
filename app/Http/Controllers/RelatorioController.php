<?php

namespace App\Http\Controllers;

use App\Actions\GetRelatorio;
use App\Models\Relatorio;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RelatorioController extends Controller
{
    public function __invoke(Request $request, GetRelatorio $action)
    {
        try {
            $collection = $action->exec($request->input('consultores'))
                ->map(fn ($rawRelatorio) => new Relatorio($rawRelatorio));
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
