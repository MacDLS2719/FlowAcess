<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerEntregaResource\Pages;
use App\Filament\Resources\CustomerEntregaResource\RelationManagers;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use App\Models\Process;

class CustomerEntregaResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Entregado';
    protected static ?string $pluralModelLabel = 'Entregados';
    protected static ?string $modelLabel = 'Entregado';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('processes', function ($q) {
                $q->where('EstadoActual', 'Entregado');
            });
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Grid::make(2)
                    ->schema([

                        Section::make('Información del Cliente')
                            ->schema([
                                TextInput::make('Nombre')->disabled(),
                                TextInput::make('Telefono')->disabled(),
                                TextInput::make('Sexo')->disabled(),
                                TextInput::make('Tipo')->disabled(),
                                TextInput::make('EstadoCustomer')->disabled(),
                                TextInput::make('CodigoCustomer')->disabled(),
                            ])
                            ->columnSpan(1),

                        Section::make('Gestión del Proceso')
                            ->schema([

                                Select::make('EstadoProceso')
                                    ->label('Estado')
                                    ->options([
                                        'En Proceso' => 'En Proceso',
                                        'Decorado' => 'Decorado',
                                        'Entregado' => 'Entregado',
                                    ])
                                    ->required()
                                    ->afterStateHydrated(function ($set, $record) {
                                        $proceso = $record->processes->last();
                                        if ($proceso) {
                                            $set('EstadoProceso', $proceso->Estado);
                                        }
                                    }),

                                DatePicker::make('Fecha')
                                    ->required()
                                    ->afterStateHydrated(function ($set, $record) {
                                        $proceso = $record->processes->last();
                                        if ($proceso) {
                                            $set('Fecha', $proceso->Fecha);
                                        }
                                    }),

                                Textarea::make('Observacion')
                                    ->afterStateHydrated(function ($set, $record) {
                                        $proceso = $record->processes->last();
                                        if ($proceso) {
                                            $set('Observacion', $proceso->Observacion);
                                        }
                                    }),

                                Textarea::make('Historial')
                                    ->label('Historial')
                                    ->disabled()
                                    ->visible(fn ($record) => $record->processes->count() > 0)
                                    ->afterStateHydrated(function ($set, $record) {
                                        $proceso = $record->processes->last();
                                        if ($proceso) {
                                            $set('Historial', $proceso->Historial);
                                        }
                                    }),

                            ])
                            ->columnSpan(1),

                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('Nombre')
                ->searchable(),

                TextColumn::make('Telefono'),

                TextColumn::make('Tipo')
                    ->badge()
                    ->color(fn ($state) => $state === 'Adulto' ? 'success' : 'warning'),

                TextColumn::make('CodigoCustomer')
                    ->label('Código')
                    ->copyable(),

                TextColumn::make('EstadoProceso')
                    ->label('Estado')
                    ->getStateUsing(fn ($record) =>
                        $record->processes->first()?->Estado
                    )
                    ->badge()
                    ->color('success'),

                TextColumn::make('FechaProceso')
                    ->label('Fecha')
                    ->getStateUsing(fn ($record) =>
                        $record->processes->first()?->Fecha
                    ),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomerEntregas::route('/'),
            'create' => Pages\CreateCustomerEntrega::route('/create'),
            'edit' => Pages\EditCustomerEntrega::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
