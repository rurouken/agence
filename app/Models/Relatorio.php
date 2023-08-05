<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Relatorio extends Model
{
    protected $fillable = [
        'date_emissao',
        'no_usuario',
        'custo_fixo',
        'lucro',
        'comissao',
        'receita_liquida',
    ];

    const CURRENCY_SYMBOL = '$R';

    protected function formatAmount(float $amount)
    {
        return self::CURRENCY_SYMBOL . ' ' . number_format($amount, 2, ',', '.');
    }

    protected function comissao(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => $this->formatAmount($attributes['comissao'])
        );
    }

    protected function lucro(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => $this->formatAmount($attributes['lucro'])
        );
    }

    protected function custoFixo(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => $this->formatAmount($attributes['custo_fixo'])
        );
    }

    protected function receitaLiquida(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => $this->formatAmount($attributes['receita_liquida'])
        );
    }
}
