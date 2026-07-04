<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Illuminate\Validation\Rules\Password;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;


class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Section::make('Información del usuario')
                    ->columns(2)
                    ->schema([

                        TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label('Correo electrónico')
                            ->email()
                            ->required(),

                        Select::make('role')
                            ->label('Rol')
                            ->options(Role::pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->native(false)
                            ->dehydrated(false)
                            ->afterStateHydrated(function ($component, $record) {
                                if ($record) {
                                    $component->state($record->roles->first()?->id);
                                }
                            }),

                        Select::make('Estado')
                            ->label('Estado')
                            ->options([
                                'activo' => 'Activo',
                                'inactivo' => 'Inactivo',
                            ])
                            ->required(),

                        TextInput::make('password')
                                    ->password()
                                    ->label('Contraseña')
                                    ->placeholder(fn ($record) =>
                                        $record
                                            ? '•••••••• (déjalo vacío si no deseas cambiarla)'
                                            : 'Ingresa una contraseña'
                                    ),

                    ])

            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (Auth::user()->hasRole('Admin')) {
            return $query;
        }

        return $query->where('id', Auth::id());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->disableClick(),

                TextColumn::make('email')
                    ->label('Correo')
                    ->searchable()
                    ->disableClick(),

                TextColumn::make('roles.name')
                    ->label('Rol')
                    ->badge()
                    ->disableClick(),

                IconColumn::make('Estado')
                    ->label('Estado')
                    ->boolean()
                    ->disableClick(),

                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y'),

            ])
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    //RULS 

    public static function canCreate(): bool
    {
        return auth()->user()->hasRole('Admin');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->hasRole('Admin');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->hasRole('Admin');
    }
}
