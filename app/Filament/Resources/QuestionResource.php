<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuestionResource\Pages;
use App\Models\Notice;
use App\Models\Question;
use App\Models\Subject;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class QuestionResource extends Resource
{
    protected static ?string $model = Question::class;

    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';

    protected static ?string $navigationGroup = 'Gerenciamento de Concursos';

    protected static ?string $modelLabel = 'Questão';

    protected static ?string $pluralModelLabel = 'Questões';

    protected static ?string $navigationLabel = 'Questões';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('notice_id')
                    ->relationship('notice', 'title')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->reactive()
                    ->afterStateUpdated(fn (callable $set) => $set('subject_id', null))
                    ->label('Edital'),
                Forms\Components\Select::make('subject_id')
                    ->relationship('subject', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->reactive()
                    ->rule(function (callable $get) {
                        return function (string $attribute, $value, \Closure $fail) use ($get) {
                            $noticeId = $get('notice_id');
                            if (!$noticeId || !$value) {
                                return;
                            }

                            $notice = Notice::find($noticeId);
                            $subject = Subject::find($value);

                            if (!$notice || !$subject) {
                                return;
                            }

                            if ($notice->hasReachedMaxQuestionsForSubject($subject)) {
                                $fail("A disciplina '{$subject->name}' já atingiu o limite de 5 questões neste edital.");
                            }
                        };
                    })
                    ->label('Disciplina'),
                Forms\Components\Textarea::make('statement')
                    ->required()
                    ->maxLength(65535)
                    ->columnSpanFull()
                    ->label('Enunciado'),
                Forms\Components\Repeater::make('options')
                    ->schema([
                        Forms\Components\TextInput::make('option')
                            ->required()
                            ->maxLength(255)
                            ->label('Opção'),
                    ])
                    ->minItems(2)
                    ->maxItems(6)
                    ->defaultItems(5)
                    ->addActionLabel('Adicionar opção')
                    ->label('Opções'),
                Forms\Components\Select::make('correct_answer')
                    ->options(function (callable $get) {
                        $options = $get('options') ?? [];
                        $choices = [];
                        foreach (array_values($options) as $i => $option) {
                            $letter = chr(65 + $i);
                            $choices[$letter] = $letter . ' - ' . ($option['option'] ?? '');
                        }
                        return $choices;
                    })
                    ->required()
                    ->label('Resposta Correta'),
                Forms\Components\Textarea::make('explanation')
                    ->maxLength(65535)
                    ->columnSpanFull()
                    ->label('Explicação'),
                Forms\Components\Select::make('difficulty')
                    ->options([
                        'easy' => 'Fácil',
                        'medium' => 'Médio',
                        'hard' => 'Difícil',
                    ])
                    ->required()
                    ->default('medium')
                    ->label('Dificuldade'),
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
                Tables\Columns\TextColumn::make('id')
                    ->sortable()
                    ->label('#'),
                Tables\Columns\TextColumn::make('subject.name')
                    ->searchable()
                    ->sortable()
                    ->label('Disciplina'),
                Tables\Columns\TextColumn::make('notice.title')
                    ->searchable()
                    ->sortable()
                    ->label('Edital')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('statement')
                    ->limit(60)
                    ->searchable()
                    ->label('Enunciado'),
                Tables\Columns\TextColumn::make('correct_answer')
                    ->badge()
                    ->color('success')
                    ->label('Resposta'),
                Tables\Columns\TextColumn::make('difficulty')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'easy' => 'success',
                        'hard' => 'danger',
                        default => 'warning',
                    })
                    ->label('Dificuldade'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Ativo'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Criado em'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('subject_id')
                    ->relationship('subject', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Disciplina'),
                Tables\Filters\SelectFilter::make('difficulty')
                    ->options([
                        'easy' => 'Fácil',
                        'medium' => 'Médio',
                        'hard' => 'Difícil',
                    ])
                    ->label('Dificuldade'),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Ativo')
                    ->boolean(),
            ])
            ->actions([
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
            'index' => Pages\ListQuestions::route('/'),
            'create' => Pages\CreateQuestion::route('/create'),
            'edit' => Pages\EditQuestion::route('/{record}/edit'),
        ];
    }
}