<x-filament-panels::page>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold tracking-tight">{{ $this->getTitle() }}</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Arquivo: {{ $record->file_name ?? 'Nenhum arquivo enviado' }}
            </p>
        </div>
        <div class="flex gap-2">
            <x-filament::button
                wire:click="doExtract"
                icon="heroicon-o-document-arrow-up"
                color="primary"
            >
                Extrair Disciplinas
            </x-filament::button>

            @if ($extracted && count($extractedSubjects) > 0)
                <x-filament::button
                    wire:click="save"
                    icon="heroicon-o-check"
                    color="success"
                >
                    Salvar Disciplinas
                </x-filament::button>
            @endif
        </div>
    </div>

    @if (!$extracted)
        <div class="rounded-xl bg-white p-8 text-center shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <x-filament::icon
                name="heroicon-o-document-arrow-up"
                class="mx-auto h-12 w-12 text-gray-400"
            />
            <h3 class="mt-4 text-lg font-semibold">Clique em "Extrair Disciplinas"</h3>
            <p class="mt-2 text-sm text-gray-500">
                O sistema lerá o PDF do edital e identificará automaticamente as disciplinas
                da seção "DOS CONTEÚDOS PROGRAMÁTICOS".
            </p>
        </div>
    @elseif (count($extractedSubjects) === 0)
        <div class="rounded-xl bg-white p-8 text-center shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <x-filament::icon
                name="heroicon-o-exclamation-triangle"
                class="mx-auto h-12 w-12 text-warning-400"
            />
            <h3 class="mt-4 text-lg font-semibold">Nenhuma disciplina encontrada</h3>
            <p class="mt-2 text-sm text-gray-500">
                Verifique se o PDF contém a seção "DOS CONTEÚDOS PROGRAMÁTICOS".
            </p>
        </div>
    @else
        <div class="space-y-4">
            @php
                $geral = array_filter($extractedSubjects, fn($s) => $s['category'] === 'geral');
                $especifico = array_filter($extractedSubjects, fn($s) => $s['category'] === 'especifico');
            @endphp

            @if (count($geral) > 0)
                <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                    <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-700">
                        <h2 class="text-lg font-semibold">CONHECIMENTOS GERAIS</h2>
                        <p class="text-sm text-gray-500">{{ count($geral) }} disciplinas encontradas</p>
                    </div>
                    <div class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach ($geral as $subject)
                            <div class="px-6 py-4">
                                <h3 class="font-medium">{{ $subject['name'] }}</h3>
                                @if (count($subject['contents']) > 0)
                                    <ul class="mt-2 list-inside list-disc space-y-1 text-sm text-gray-600 dark:text-gray-400">
                                        @foreach ($subject['contents'] as $content)
                                            <li>{{ $content }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if (count($especifico) > 0)
                <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                    <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-700">
                        <h2 class="text-lg font-semibold">CONHECIMENTOS ESPECÍFICOS</h2>
                        <p class="text-sm text-gray-500">{{ count($especifico) }} disciplinas encontradas</p>
                    </div>
                    <div class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach ($especifico as $subject)
                            <div class="px-6 py-4">
                                <h3 class="font-medium">{{ $subject['name'] }}</h3>
                                @if (count($subject['contents']) > 0)
                                    <ul class="mt-2 list-inside list-disc space-y-1 text-sm text-gray-600 dark:text-gray-400">
                                        @foreach ($subject['contents'] as $content)
                                            <li>{{ $content }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @endif
</x-filament-panels::page>