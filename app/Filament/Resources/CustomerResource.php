<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use App\Models\Process;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Placeholder;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Clientes Registrados';
    protected static ?string $pluralModelLabel = 'Clientes Registrados';
    protected static ?string $modelLabel = 'Clientes Registrados';


    public static function form(Form $form): Form
{
    return $form
        ->schema([

            Grid::make()
                ->columns([
                    'default' => 1,
                    'xl' => 2,
                ])
                ->schema([

                    Section::make('Información del Cliente')
                        ->icon('heroicon-o-user')
                        ->columnSpan(1)
                        ->schema([

                            TextInput::make('CodigoCustomer')
                                ->label('Código')
                                ->disabled(),

                            TextInput::make('Nombre')
                                ->label('Nombre')
                                ->disabled(),

                            TextInput::make('Telefono')
                                ->label('Teléfono')
                                ->disabled(),

                            TextInput::make('Sexo')
                                ->label('Sexo')
                                ->disabled(),

                            TextInput::make('Tipo')
                                ->label('Tipo')
                                ->disabled(),

                        ]),

                    Section::make('Gestión del Proceso')
                        ->icon('heroicon-o-clipboard-document-list')
                        ->columnSpan(1)
                        ->schema([

                            Select::make('EstadoGeneral')
                                ->label('Estado General')
                                ->options([
                                    'Pendiente'   => 'Pendiente',
                                    'En Proceso' => 'En Proceso',
                                    'Decorado'   => 'Decorado',
                                    'Entregado'  => 'Entregado',
                                ])
                                ->required(),

                            Select::make('EstadoContable')
                                ->label('Estado Contable')
                                ->options([
                                    'Pago' => 'Pago',
                                    'Debe' => 'Debe',
                                ])
                                ->required(),

                            Textarea::make('Observacion')
                                ->label('Observación')
                                ->rows(10)
                                ->dehydrated(false)
                                ->placeholder('Escriba una observación del proceso...'),

                        ]),

                ]),

            Section::make('Historial del Proceso')
                ->icon('heroicon-o-clock')
                ->columnSpanFull()
                ->schema(function ($record) {

                    $process = $record->processes()->first();

                    if (! $process) {
                        return [
                            Placeholder::make('sin_historial')
                                ->hiddenLabel()
                                ->content('No existe historial para este cliente.'),
                        ];
                    }

                    $histories = $process->histories()
                        ->latest()
                        ->get();

                    if ($histories->isEmpty()) {
                        return [
                            Placeholder::make('sin_historial')
                                ->hiddenLabel()
                                ->content('No existe historial para este cliente.'),
                        ];
                    }

                    $schema = [];

                    foreach ($histories as $history) {

                        $schema[] = Section::make(
                            $history->created_at->format('d/m/Y H:i')
                        )
                            ->columns(2)
                            ->collapsed()
                            ->schema([

                                Placeholder::make('estado_'.$history->idHistory)
                                    ->label('Estado')
                                    ->content($history->Estado),

                                Placeholder::make('fecha_'.$history->idHistory)
                                    ->label('Actualizado')
                                    ->content(
                                        $history->created_at->format('d/m/Y H:i')
                                    ),

                                Placeholder::make('observacion_'.$history->idHistory)
                                    ->label('Observación')
                                    ->content($history->Observacion)
                                    ->columnSpanFull(),

                            ]);

                    }

                    return $schema;

                }),

        ]);
}

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('CodigoCustomer')
                    ->label('Código')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('Nombre')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('Telefono')
                    ->label('Teléfono')
                    ->searchable(),

                TextColumn::make('Tipo')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'Adulto' => 'primary',
                        'Niño' => 'success',
                        default => 'gray',
                    }),

                TextColumn::make('EstadoGeneral')
                    ->label('Estado General')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'Pendiente' => 'gray',
                        'En Proceso' => 'warning',
                        'Decorado' => 'info',
                        'Entregado' => 'success',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('EstadoContable')
                    ->label('Estado Contable')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'Pago' => 'success',
                        'Debe' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Registrado')
                    ->date('d/m/Y')
                    ->sortable(),

            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
            ])
            ->actions([
             Tables\Actions\ViewAction::make()
                    ->visible(fn ($record) => Auth::user()->hasRole('Admin') || $record->id === Auth::id()),

                Tables\Actions\EditAction::make()
                    ->visible(fn ($record) => Auth::user()->hasRole('Admin') || $record->id === Auth::id()),

                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => Auth::user()->hasRole('Admin')),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => Auth::user()->hasRole('Admin')),
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
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
