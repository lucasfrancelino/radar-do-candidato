<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContestResource\Pages;
use App\Filament\Resources\ContestResource\RelationManagers;
use App\Models\Contest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ContestResource extends Resource
{
    protected static ?string $model = Contest::class;

    protected static ?string $navigationIcon = 'heroicon-o-trophy';

    protected static ?string $navigationGroup = 'Gerenciamento de Concursos';

    protected static ?string $modelLabel = 'Concurso';

    protected static ?string $pluralModelLabel = 'Concursos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('institution_id')
                    ->relationship('institution', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('Instituição'),
                Forms\Components\Select::make('board_id')
                    ->relationship('board', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('Banca'),
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->label('Título do Concurso'),
                Forms\Components\Textarea::make('description')
                    ->maxLength(65535)
                    ->columnSpanFull()
                    ->label('Descrição'),
                Forms\Components\DatePicker::make('start_date')
                    ->required()
                    ->label('Data de Início'),
                Forms\Components\DatePicker::make('end_date')
                    ->required()
                    ->label('Data de Término'),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pendente',
                        'open' => 'Aberto',
                        'closed' => 'Fechado',
                        'canceled' => 'Cancelado',
                    ])
                    ->required()
                    ->default('pending')
                    ->label('Status'),
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
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->label('Título'),
                Tables\Columns\TextColumn::make('institution.name')
                    ->searchable()
                    ->sortable()
                    ->label('Instituição'),
                Tables\Columns\TextColumn::make('board.name')
                    ->searchable()
                    ->sortable()
                    ->label('Banca'),
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable()
                    ->label('Início'),
                Tables\Columns\TextColumn::make('end_date')
                    ->date()
                    ->sortable()
                    ->label('Término'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'open' => 'success',
                        'closed' => 'info',
                        'canceled' => 'danger',
                    })
                    ->label('Status'),
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
                Tables\Filters\SelectFilter::make('institution_id')
                    ->relationship('institution', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Instituição'),
                Tables\Filters\SelectFilter::make('board_id')
                    ->relationship('board', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Banca'),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pendente',
                        'open' => 'Aberto',
                        'closed' => 'Fechado',
                        'canceled' => 'Cancelado',
                    ])
                    ->label('Status'),
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
            'index' => Pages\ListContests::route('/'),
            'create' => Pages\CreateContest::route('/create'),
            'edit' => Pages\EditContest::route('/{record}/edit'),
        ];
    }
}