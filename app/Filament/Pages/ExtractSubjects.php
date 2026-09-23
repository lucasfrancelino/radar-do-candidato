<?php

namespace App\Filament\Pages;

use App\Models\Notice;
use App\Services\PdfNoticeParserService;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ExtractSubjects extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-arrow-up';

    protected static ?string $slug = 'extract-subjects/{record}';

    protected static bool $shouldRegisterNavigation = false;

    protected static string $view = 'filament.pages.extract-subjects';

    public Notice $record;

    public array $extractedSubjects = [];

    public bool $extracted = false;

    public function mount(Notice $record): void
    {
        $this->record = $record;
    }

    public function getTitle(): string
    {
        return "Extrair Disciplinas: {$this->record->title}";
    }

    public function doExtract(): void
    {
        try {
            $service = app(PdfNoticeParserService::class);
            $this->extractedSubjects = $service->extractSubjects($this->record);
            $this->extracted = true;

            if (empty($this->extractedSubjects)) {
                Notification::make()
                    ->warning()
                    ->title('Nenhuma disciplina encontrada')
                    ->body('Verifique se o PDF contém a seção "DOS CONTEÚDOS PROGRAMÁTICOS".')
                    ->send();
            } else {
                Notification::make()
                    ->success()
                    ->title(count($this->extractedSubjects) . ' disciplinas encontradas')
                    ->send();
            }
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title('Erro ao extrair disciplinas')
                ->body($e->getMessage())
                ->send();
        }
    }

    public function save(): void
    {
        try {
            $service = app(PdfNoticeParserService::class);
            $created = $service->extractAndSave($this->record);

            Notification::make()
                ->success()
                ->title(count($created) . ' disciplinas salvas com sucesso!')
                ->body('As disciplinas foram vinculadas ao edital.')
                ->send();

            $this->redirect(\App\Filament\Resources\NoticeResource::getUrl('edit', ['record' => $this->record]));
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title('Erro ao salvar disciplinas')
                ->body($e->getMessage())
                ->send();
        }
    }
}