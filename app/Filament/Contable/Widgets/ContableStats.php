<?php

namespace App\Filament\Contable\Widgets;

use App\Models\Customer;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ContableStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [

            Stat::make(
                'Entregados',
                Customer::where('EstadoGeneral', 'Entregado')->count()
            )
                ->description('Procesos finalizados')
                ->color('success')
                ->icon('heroicon-o-check-circle'),

            Stat::make(
                'Pagados',
                Customer::where('EstadoContable', 'Pago')->count()
            )
                ->description('Clientes al día')
                ->color('success')
                ->icon('heroicon-o-banknotes'),

            Stat::make(
                'Pendientes de Pago',
                Customer::where('EstadoContable', 'Debe')->count()
            )
                ->description('Clientes con saldo pendiente')
                ->color('danger')
                ->icon('heroicon-o-exclamation-circle'),

        ];
    }
}