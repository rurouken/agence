<?php

namespace App\Actions;

use Illuminate\Support\Facades\DB;

class GetConsultores implements Action
{
    public function exec($params)
    {
        return DB::table('cao_usuario as cu')
            ->select([
                'cu.co_usuario',
                'cu.no_usuario',
                'ps.co_sistema',
                'ps.in_ativo',
                'ps.co_tipo_usuario',
            ])
            ->join('permissao_sistema as ps', 'ps.co_usuario', '=', 'cu.co_usuario')
            ->where('ps.co_sistema', '=', 1)
            ->where('ps.in_ativo', '=', 'S')
            ->whereIn('ps.co_tipo_usuario', [0, 1, 2])
            ->orderBy('cu.no_usuario')
            ->get();
    }
}
