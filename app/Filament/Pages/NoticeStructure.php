<?php

namespace App\Filament\Pages;

use App\Models\Notice;
use Filament\Pages\Page;

class NoticeStructure extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Gerenciamento de Concursos';

    protected static ?string $navigationLabel = 'Estrutura do Edital';

    protected static ?string $slug = 'notice-structure/{record}';

    protected static bool $shouldRegisterNavigation = false;

    protected static string $view = 'filament.pages.notice-structure';

    public Notice $record;

    public function mount(Notice $record): void
    {
        $this->record = $record;
    }

    public function getTitle(): string
    {
        return "Estrutura do Edital: {$this->record->title}";
    }

    protected function getViewData(): array
    {
        return [
            'notice' => $this->record,
            'subjects' => $this->record->subjects()->withPivot([
                'weight', 'question_count', 'elimination_criteria', 'minimum_score', 'historical_incidence'
            ])->get(),
            'questions' => $this->record->questions()->with('subject')->get(),
        ];
    }
}