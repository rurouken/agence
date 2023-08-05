<?php

namespace App\Actions;

use Illuminate\Support\Facades\DB;
use App\Models\Relatorio;
use Illuminate\Support\Collection;

final class GetRelatorio implements Action
{
    public function exec($params)
    {
        return DB::table('cao_os as co')
            ->selectRaw(
                "cu.no_usuario, 
                CONCAT(MONTHNAME(cf.data_emissao), ' ', YEAR(cf.data_emissao)) AS date_emissao,
                MONTHNAME(cf.data_emissao) AS month_emissao,
                YEAR(cf.data_emissao) AS year_emissao,
                SUM(cf.valor) AS valor, 
                SUM(cf.valor) - (SUM(cf.valor) * (cf.total_imp_inc / 100)) AS receita_liquida, 
                cs.brut_salario AS custo_fixo, 
                (cf.comissao_cn / 100) * (cf.valor - (cf.valor * (cf.total_imp_inc / 100))) AS comissao, 
                (SUM(cf.valor) - (SUM(cf.valor) * (cf.total_imp_inc / 100))) - (cs.brut_salario + (cf.comissao_cn / 100) * (cf.valor - (cf.valor * (cf.total_imp_inc / 100)))) AS lucro"
            )
            ->whereIn('co.co_usuario', explode(',', $params))
            ->join('cao_fatura as cf', 'cf.co_os', '=', 'co.co_os')
            ->join('cao_salario as cs', 'cs.co_usuario', '=', 'co.co_usuario')
            ->join('permissao_sistema as ps', 'ps.co_usuario', '=', 'co.co_usuario')
            ->join('cao_usuario as cu', 'cu.co_usuario', '=', 'co.co_usuario')
            ->groupByRaw('cf.comissao_cn, cf.total_imp_inc, cf.valor, cu.no_usuario, date_emissao, custo_fixo, comissao, cs.brut_salario, cf.data_emissao')
            ->orderByRaw("STR_TO_DATE(CONCAT('0001 ', date_emissao, ' 01'), '%Y %M %d')")
            ->get()
            ->groupBy('no_usuario')
            ->map(
                fn (Collection $relatorio) => $relatorio->groupBy('date_emissao')
                    ->map(
                        fn (Collection $group) => [
                            'date_emissao' => $group->first()->date_emissao,
                            'month_emissao' => $group->first()->month_emissao,
                            'year_emissao' => $group->first()->year_emissao,
                            'no_usuario' => $group->first()->no_usuario,
                            'custo_fixo' => $group->first()->custo_fixo,
                            'lucro' => $group->sum('lucro'),
                            'comissao' => $group->sum('comissao'),
                            'receita_liquida' => $group->sum('receita_liquida'),
                        ]
                    )
                    ->values()
            )
            ->values()
            ->flatten(1);
    }
}
