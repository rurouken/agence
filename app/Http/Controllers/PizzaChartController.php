<?php

namespace App\Http\Controllers;

use App\Actions\GetRelatorio;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class PizzaChartController extends Controller
{
    public function __invoke(Request $request, GetRelatorio $action)
    {
        try {
            $relatorios = $action->exec($request->input('consultores'));
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

        $totalReceitas = $relatorios->sum('receita_liquida');

        return $relatorios->groupBy('no_usuario')->map(
            fn (Collection $grouped) => round(($grouped->sum('receita_liquida') / $totalReceitas) * 100, 2)
        );
    }
}
