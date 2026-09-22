<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentResource\Pages;
use App\Filament\Resources\StudentResource\RelationManagers;
use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Usuários e Perfis';

    protected static ?string $modelLabel = 'Estudante';

    protected static ?string $pluralModelLabel = 'Estudantes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->searchable()
                    ->preload()
                    ->label('Usuário'),
                Forms\Components\TextInput::make('registration_number')
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->label('Número de Matrícula'),
                Forms\Components\DatePicker::make('birth_date')
                    ->label('Data de Nascimento'),
                Forms\Components\TextInput::make('phone_number')
                    ->tel()
                    ->maxLength(255)
                    ->label('Telefone'),
                Forms\Components\TextInput::make('address')
                    ->maxLength(255)
                    ->label('Endereço'),
                Forms\Components\TextInput::make('city')
                    ->maxLength(255)
                    ->label('Cidade'),
                Forms\Components\TextInput::make('state')
                    ->maxLength(255)
                    ->label('Estado'),
                Forms\Components\TextInput::make('zip_code')
                    ->maxLength(255)
                    ->label('CEP'),
                Forms\Components\Toggle::make('is_active')
                    ->required()
                    ->default(true)
                    ->label('Ativo'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable()
                    ->sortable()
                    ->label('Usuário'),
                Tables\Columns\TextColumn::make('registration_number')
                    ->searchable()
                    ->label('Matrícula'),
                Tables\Columns\TextColumn::make('birth_date')
                    ->date()
                    ->sortable()
                    ->label('Nascimento'),
                Tables\Columns\TextColumn::make('phone_number')
                    ->searchable()
                    ->label('Telefone'),
                Tables\Columns\TextColumn::make('city')
                    ->searchable()
                    ->label('Cidade'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Ativo'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Criado em'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Atualizado em'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Usuário'),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Ativo')
                    ->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
        ];
    }
}