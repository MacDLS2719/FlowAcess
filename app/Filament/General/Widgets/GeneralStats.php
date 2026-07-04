<?php

namespace App\Filament\General\Widgets;

use App\Models\Customer;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class GeneralStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [

            Stat::make(
                'Registrados sin proceso',
                Customer::where('EstadoGeneral', 'Pendiente')->count()
            )
                ->description('Clientes registrados')
                ->color('gray')
                ->icon('heroicon-o-clock'),

            Stat::make(
                'En Proceso',
                Customer::where('EstadoGeneral', 'En Proceso')->count()
            )
                ->description('Procesos activos')
                ->color('warning')
                ->icon('heroicon-o-cog-6-tooth'),

            Stat::make(
                'Decorados',
                Customer::where('EstadoGeneral', 'Decorado')->count()
            )
                ->description('Pendientes de entrega')
                ->color('info')
                ->icon('heroicon-o-sparkles'),

            Stat::make(
                'Entregados',
                Customer::where('EstadoGeneral', 'Entregado')->count()
            )
                ->description('Procesos finalizados')
                ->color('success')
                ->icon('heroicon-o-check-circle'),

        ];
    }
}