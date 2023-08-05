<?php

use App\Http\Controllers\BarChartController;
use App\Http\Controllers\ConsultoresController;
use App\Http\Controllers\PizzaChartController;
use App\Http\Controllers\RelatorioController;
use Illuminate\Support\Facades\Route;

Route::get('consultores', [ConsultoresController::class, 'index']);

Route::get('relatorio', RelatorioController::class);

Route::get('pizzaChart', PizzaChartController::class);

Route::get('barChart', BarChartController::class);
