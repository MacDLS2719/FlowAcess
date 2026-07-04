<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AvailabilityResource\Pages;
use App\Filament\Resources\AvailabilityResource\RelationManagers;
use App\Models\Availability;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Carbon\Carbon;

class AvailabilityResource extends Resource
{
    protected static ?string $model = Availability::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Fechas Habilitadas';
    protected static ?string $pluralModelLabel = 'Fechas Habilitadas';
    protected static ?string $modelLabel = 'Fechas Habilitadas';
    protected static ?string $navigationGroup = 'Operaciones';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Configuración de Disponibilidad')
                    ->schema([

                        Forms\Components\DatePicker::make('FechaInicio')
                            ->label('Fecha Inicial')
                            ->afterStateHydrated(function ($component, $record) {
                                if ($record && $record->AvailableDate) {
                                    $component->state(
                                        Carbon::parse($record->AvailableDate)->format('Y-m-d')
                                    );
                                }
                            }),

                        Forms\Components\DatePicker::make('FechaFin')
                            ->label('Fecha Final')
                            ->required()
                            ->afterStateHydrated(function ($component, $record) {
                                if ($record && $record->AvailableDate) {
                                    $component->state(
                                        Carbon::parse($record->AvailableDate)->format('Y-m-d')
                                    );
                                }
                            }),

                        Forms\Components\TimePicker::make('HoraInicio')
                            ->label('Hora Inicial')
                            ->seconds(false)
                            ->required()
                            ->afterStateHydrated(function ($component, $record) {
                                if ($record) {
                                    $component->state($record->AvailableTime);
                                }
                            }),

                        Forms\Components\TimePicker::make('HoraFin')
                            ->label('Hora Final')
                            ->seconds(false)
                            ->required()
                            ->afterStateHydrated(function ($component, $record) {
                                if ($record) {
                                    $component->state($record->AvailableTime);
                                }
                            }),

                        Forms\Components\Select::make('Intervalo')
                            ->label('Intervalo')
                            ->options([
                                15 => '15 minutos',
                                30 => '30 minutos',
                                60 => '60 minutos',
                            ])
                            ->default(30)
                            ->afterStateHydrated(function ($component, $record) {
                                if ($record) {
                                    $component->state(30);
                                }
                            }),

                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('IdAvailability')
                    ->label('ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('AvailableDate')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('AvailableTime')
                    ->label('Hora')
                    ->time('H:i')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('Status')
                    ->label('Estado')
                    ->formatStateUsing(fn ($state) => $state == 0 ? 'Disponible' : 'Ocupado')
                    ->colors([
                        'success' => fn ($state) => $state == 0,
                        'danger' => fn ($state) => $state == 1,
                    ]),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),

            ])
            ->filters([

                Tables\Filters\SelectFilter::make('Status')
                    ->options([
                        0 => 'Disponible',
                        1 => 'Ocupado',
                    ]),

                Tables\Filters\Filter::make('fecha')
                    ->form([
                        Forms\Components\DatePicker::make('desde'),
                        Forms\Components\DatePicker::make('hasta'),
                    ])
                    ->query(function ($query, array $data) {

                        return $query
                            ->when(
                                $data['desde'],
                                fn ($q) => $q->whereDate('AvailableDate', '>=', $data['desde'])
                            )
                            ->when(
                                $data['hasta'],
                                fn ($q) => $q->whereDate('AvailableDate', '<=', $data['hasta'])
                            );
                    }),

            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([

                    Tables\Actions\DeleteBulkAction::make(),

                ]),
            ])
            ->defaultSort('AvailableDate');
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
            'index' => Pages\ListAvailabilities::route('/'),
            'create' => Pages\CreateAvailability::route('/create'),
            'edit' => Pages\EditAvailability::route('/{record}/edit'),
        ];
    }
}
