<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NoticeResource\Pages;
use App\Models\Notice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class NoticeResource extends Resource
{
    protected static ?string $model = Notice::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Gerenciamento de Concursos';

    protected static ?string $modelLabel = 'Edital';

    protected static ?string $pluralModelLabel = 'Editais';

    protected static ?string $navigationLabel = 'Editais';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('contest_id')
                    ->relationship('contest', 'title')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('Concurso'),
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->label('Título do Edital'),
                Forms\Components\Textarea::make('description')
                    ->maxLength(65535)
                    ->columnSpanFull()
                    ->label('Descrição'),
                Forms\Components\DatePicker::make('publication_date')
                    ->label('Data de Publicação'),
                Forms\Components\FileUpload::make('file_path')
                    ->label('Arquivo do Edital (PDF)')
                    ->directory('notices')
                    ->acceptedFileTypes(['application/pdf'])
                    ->maxSize(10240)
                    ->visibility('public')
                    ->downloadable()
                    ->openable()
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('is_active')
                    ->required()
                    ->default(true)
                    ->label('Ativo'),
            ]);
    }

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        if (isset($data['file_path'])) {
            $data['file_name'] = basename($data['file_path']);
        }
        return $data;
    }

    public static function mutateFormDataBeforeSave(array $data): array
    {
        if (isset($data['file_path'])) {
            $data['file_name'] = basename($data['file_path']);
        }
        return $data;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->label('Título'),
                Tables\Columns\TextColumn::make('contest.title')
                    ->searchable()
                    ->sortable()
                    ->label('Concurso'),
                Tables\Columns\TextColumn::make('publication_date')
                    ->date()
                    ->sortable()
                    ->label('Publicação'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Ativo'),
                Tables\Columns\TextColumn::make('file_name')
                    ->label('Arquivo')
                    ->icon('heroicon-o-document-arrow-down')
                    ->url(fn (Notice $record): ?string => $record->file_path ? Storage::url($record->file_path) : null)
                    ->openUrlInNewTab()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Criado em'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('contest_id')
                    ->relationship('contest', 'title')
                    ->searchable()
                    ->preload()
                    ->label('Concurso'),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Ativo')
                    ->boolean(),
            ])
            ->actions([
                Tables\Actions\Action::make('extract')
                    ->label('Extrair Disciplinas')
                    ->icon('heroicon-o-document-arrow-up')
                    ->url(fn (Notice $record) => \App\Filament\Pages\ExtractSubjects::getUrl(['record' => $record])),
                Tables\Actions\Action::make('structure')
                    ->label('Estrutura')
                    ->icon('heroicon-o-queue-list')
                    ->url(fn (Notice $record) => \App\Filament\Pages\NoticeStructure::getUrl(['record' => $record])),
                Tables\Actions\EditAction::make()
                    ->label('Editar'),
                Tables\Actions\DeleteAction::make()
                    ->label('Excluir'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Excluir selecionados'),
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
            'index' => Pages\ListNotices::route('/'),
            'create' => Pages\CreateNotice::route('/create'),
            'edit' => Pages\EditNotice::route('/{record}/edit'),
        ];
    }
}