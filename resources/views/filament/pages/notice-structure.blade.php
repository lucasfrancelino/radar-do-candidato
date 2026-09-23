<x-filament-panels::page>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">
                    {{ $this->getTitle() }}
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Concurso: {{ $notice->contest?->title ?? 'Não informado' }}
                    &middot;
                    Publicação: {{ $notice->publication_date?->format('d/m/Y') ?? 'Não informada' }}
                </p>
            </div>
            <div class="flex gap-2">
                <x-filament::button
                    tag="a"
                    href="{{ \App\Filament\Resources\NoticeResource::getUrl('edit', ['record' => $notice]) }}"
                    color="gray"
                    icon="heroicon-o-pencil"
                >
                    Editar Edital
                </x-filament::button>
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Disciplinas</dt>
            <dd class="mt-1 text-2xl font-semibold">{{ $subjects->count() }}</dd>
        </div>
        <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Questões</dt>
            <dd class="mt-1 text-2xl font-semibold">{{ $questions->count() }}</dd>
        </div>
        <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
            <dd class="mt-1">
                @if ($notice->is_active)
                    <x-filament::badge color="success">Ativo</x-filament::badge>
                @else
                    <x-filament::badge color="danger">Inativo</x-filament::badge>
                @endif
            </dd>
        </div>
    </div>

    <div class="mt-6">
        <h2 class="text-lg font-semibold mb-4">Disciplinas e Pesos</h2>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <th class="px-4 py-2 text-left font-medium text-gray-500 dark:text-gray-400">Disciplina</th>
                        <th class="px-4 py-2 text-center font-medium text-gray-500 dark:text-gray-400">Peso</th>
                        <th class="px-4 py-2 text-center font-medium text-gray-500 dark:text-gray-400">Qtd. Questões</th>
                        <th class="px-4 py-2 text-center font-medium text-gray-500 dark:text-gray-400">Nota Mínima</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-500 dark:text-gray-400">Critérios Eliminatórios</th>
                        <th class="px-4 py-2 text-center font-medium text-gray-500 dark:text-gray-400">Incidência Histórica</th>
                        <th class="px-4 py-2 text-center font-medium text-gray-500 dark:text-gray-400">Questões Cadastradas</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($subjects as $subject)
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <td class="px-4 py-3 font-medium">{{ $subject->name }}</td>
                            <td class="px-4 py-3 text-center">{{ $subject->pivot->weight ? number_format($subject->pivot->weight, 2) : '-' }}</td>
                            <td class="px-4 py-3 text-center">{{ $subject->pivot->question_count ?? '-' }}</td>
                            <td class="px-4 py-3 text-center">{{ $subject->pivot->minimum_score ? number_format($subject->pivot->minimum_score, 2) : '-' }}</td>
                            <td class="px-4 py-3 max-w-xs truncate" title="{{ $subject->pivot->elimination_criteria }}">{{ $subject->pivot->elimination_criteria ?: '-' }}</td>
                            <td class="px-4 py-3 text-center">{{ $subject->pivot->historical_incidence ? number_format($subject->pivot->historical_incidence, 2) . '%' : '-' }}</td>
                            <td class="px-4 py-3 text-center">
                                @php
                                    $count = $questions->where('subject_id', $subject->id)->count();
                                @endphp
                                <x-filament::badge :color="$count >= 5 ? 'success' : ($count > 0 ? 'warning' : 'danger')">
                                    {{ $count }} / 5
                                </x-filament::badge>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                Nenhuma disciplina vinculada a este edital.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($questions->isNotEmpty())
        <div class="mt-8">
            <h2 class="text-lg font-semibold mb-4">Questões por Disciplina</h2>

            @foreach ($subjects as $subject)
                @php
                    $subjectQuestions = $questions->where('subject_id', $subject->id);
                @endphp

                @if ($subjectQuestions->isNotEmpty())
                    <div class="mb-6">
                        <h3 class="text-md font-medium mb-2 text-gray-700 dark:text-gray-300">
                            {{ $subject->name }}
                            <span class="text-sm text-gray-500">({{ $subjectQuestions->count() }} questões)</span>
                        </h3>

                        <div class="space-y-2">
                            @foreach ($subjectQuestions as $question)
                                <div class="rounded-lg border border-gray-200 bg-white p-3 dark:border-gray-700 dark:bg-gray-800">
                                    <p class="text-sm">{{ Str::limit($question->statement, 200) }}</p>
                                    <div class="mt-1 flex gap-2 text-xs text-gray-500">
                                        <span>Dificuldade: {{ ucfirst($question->difficulty) }}</span>
                                        <span>Resposta: {{ $question->correct_answer }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    @endif
</x-filament-panels::page>