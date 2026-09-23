<?php

namespace App\Services;

use App\Models\Notice;
use App\Models\Subject;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser;

class PdfNoticeParserService
{
    protected Parser $parser;

    public function __construct()
    {
        $this->parser = new Parser();
    }

    /**
     * Extrai disciplinas e conteúdos do PDF de um edital.
     *
     * @return array<int, array{name: string, contents: array, category: string}>
     */
    public function extractSubjects(Notice $notice): array
    {
        Log::info('Iniciando extração do PDF', [
            'notice_id' => $notice->id,
            'notice_title' => $notice->title,
            'file_path' => $notice->file_path,
            'file_exists' => $notice->file_path ? Storage::disk('public')->exists($notice->file_path) : false,
        ]);

        if (!$notice->file_path || !Storage::disk('public')->exists($notice->file_path)) {
            throw new \RuntimeException('Arquivo PDF do edital não encontrado.');
        }

        $pdfContent = Storage::disk('public')->get($notice->file_path);
        Log::info('Conteúdo do PDF lido', [
            'size_bytes' => strlen($pdfContent),
        ]);

        $pdf = $this->parser->parseContent($pdfContent);
        $text = $pdf->getText();

        Log::info('Texto extraído do PDF', [
            'text_length' => strlen($text),
            'text_preview' => mb_substr($text, 0, 500),
        ]);

        return $this->parseSubjectsFromText($text);
    }

    /**
     * Parseia o texto extraído do PDF para encontrar disciplinas e conteúdos.
     */
    protected function parseSubjectsFromText(string $text): array
    {
        $subjects = [];

        // Localiza a seção "DOS CONTEÚDOS PROGRAMÁTICOS"
        $startMarker = 'DOS CONTEÚDOS PROGRAMÁTICOS';
        $startPos = mb_stripos($text, $startMarker);

        Log::info('Buscando marcador DOS CONTEÚDOS PROGRAMÁTICOS', [
            'found' => $startPos !== false,
            'position' => $startPos,
        ]);

        if ($startPos === false) {
            throw new \RuntimeException('Seção "DOS CONTEÚDOS PROGRAMÁTICOS" não encontrada no PDF.');
        }

        // Pega o texto a partir do marcador
        $relevantText = mb_substr($text, $startPos);

        // Divide em CONHECIMENTOS GERAIS e CONHECIMENTOS ESPECÍFICOS
        $sections = [
            'geral' => $this->extractSectionContent($relevantText, 'CONHECIMENTOS GERAIS', 'CONHECIMENTOS ESPECÍFICOS'),
            'especifico' => $this->extractSectionContent($relevantText, 'CONHECIMENTOS ESPECÍFICOS', null),
        ];

        Log::info('Seções encontradas', [
            'geral_length' => strlen($sections['geral']),
            'especifico_length' => strlen($sections['especifico']),
        ]);

        foreach ($sections as $category => $sectionText) {
            if (empty($sectionText)) {
                continue;
            }

            $lines = explode("\n", $sectionText);
            $currentSubject = null;
            $currentContents = [];

            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line)) {
                    continue;
                }

                // Verifica se é um título de disciplina
                if (preg_match('/^(\d+\.?\s*)?([A-ZÀ-Ú][A-ZÀ-Ú\s\-]+)$/u', $line) || preg_match('/^\d+[\.$|]\s+.+/u', $line)) {
                    // Salva a disciplina anterior
                    if ($currentSubject !== null) {
                        $subjects[] = [
                            'name' => $currentSubject,
                            'contents' => array_filter(array_map('trim', $currentContents)),
                            'category' => $category,
                        ];
                    }

                    $currentSubject = preg_replace('/^\d+[\.$|]\s*/u', '', $line);
                    $currentSubject = trim($currentSubject);
                    $currentContents = [];
                } elseif ($currentSubject !== null) {
                    $currentContents[] = $line;
                }
            }

            // Salva a última disciplina
            if ($currentSubject !== null) {
                $subjects[] = [
                    'name' => $currentSubject,
                    'contents' => array_filter(array_map('trim', $currentContents)),
                    'category' => $category,
                ];
            }
        }

        Log::info('Disciplinas extraídas', [
            'count' => count($subjects),
            'names' => array_column($subjects, 'name'),
        ]);

        return $subjects;
    }

    /**
     * Extrai o texto entre dois marcadores.
     */
    protected function extractSectionContent(string $text, string $startMarker, ?string $endMarker): string
    {
        $startPos = mb_stripos($text, $startMarker);
        if ($startPos === false) {
            return '';
        }

        $startPos += mb_strlen($startMarker);

        if ($endMarker === null) {
            return mb_substr($text, $startPos);
        }

        $endPos = mb_stripos($text, $endMarker, $startPos);
        if ($endPos === false) {
            return mb_substr($text, $startPos);
        }

        return mb_substr($text, $startPos, $endPos - $startPos);
    }

    /**
     * Extrai disciplinas e salva no banco de dados.
     *
     * @return array<int, Subject>
     */
    public function extractAndSave(Notice $notice): array
    {
        $extracted = $this->extractSubjects($notice);
        $created = [];

        foreach ($extracted as $data) {
            $subject = Subject::firstOrCreate(
                ['name' => $data['name']],
                [
                    'description' => implode("\n", $data['contents']),
                    'contents' => $data['contents'],
                    'category' => $data['category'],
                    'is_active' => true,
                ]
            );

            // Vincula a disciplina ao edital com valores padrão
            if (!$subject->notices()->where('notice_id', $notice->id)->exists()) {
                $subject->notices()->attach($notice->id, [
                    'weight' => 1.00,
                    'question_count' => 5,
                    'elimination_criteria' => null,
                    'minimum_score' => null,
                    'historical_incidence' => null,
                ]);
            }

            $created[] = $subject;
        }

        return $created;
    }
}