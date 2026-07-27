<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-paper leading-tight">
                {{ __('Tarefas do dia') }}
            </h2>

            <a
                href="{{ route('profile.perfil') }}"
                class="text-xs font-semibold uppercase tracking-widest text-paper/60 hover:text-ember transition">
                Voltar ao calendário
            </a>
        </div>
    </x-slot>

    {{-- 🎯 NOVO: agenda diária clara e cronológica aberta pelos dias destacados
         no calendário do perfil. Ela sempre consulta apenas tarefas do usuário. --}}
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        <section class="overflow-hidden rounded-xl bg-ink shadow-lg">
            <div class="h-1.5 bg-ember"></div>
            <div class="p-6 sm:p-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
                    <div class="flex items-start gap-4">
                        <span class="w-12 h-12 rounded-xl bg-ember/15 text-ember flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3.75 9.75h16.5M5.25 5.25h13.5A1.5 1.5 0 0120.25 6.75v12a1.5 1.5 0 01-1.5 1.5H5.25a1.5 1.5 0 01-1.5-1.5v-12a1.5 1.5 0 011.5-1.5z" />
                            </svg>
                        </span>

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-ember">
                                Programação
                            </p>
                            {{-- 🎯 ALTERADO: somente a primeira letra da data é
                                 colocada em maiúscula, preservando as preposições
                                 em minúsculo conforme a escrita natural em português. --}}
                            <h1 class="mt-2 text-2xl sm:text-3xl font-bold text-paper">
                                {{ ucfirst($selectedDate->translatedFormat('l, d \d\e F \d\e Y')) }}
                            </h1>
                            <p class="mt-2 text-sm text-paper/50">
                                {{ $dayStats['total'] }}
                                {{ $dayStats['total'] === 1 ? 'tarefa programada' : 'tarefas programadas' }}
                            </p>
                        </div>
                    </div>

                    {{-- 🎯 NOVO: navegação simples permite consultar o dia anterior
                         ou seguinte sem precisar retornar primeiro ao perfil. --}}
                    <div class="inline-flex self-start sm:self-auto rounded-lg border border-paper/10 bg-paper/5 p-1">
                        <a
                            href="{{ route('tasks.by-date', ['date' => $previousDate->format('Y-m-d')]) }}"
                            aria-label="Ver dia anterior"
                            class="inline-flex items-center gap-2 rounded-md px-3 py-2 text-xs font-semibold uppercase tracking-wider text-paper/60 hover:bg-ember hover:text-paper transition">
                            <span aria-hidden="true">←</span>
                            Anterior
                        </a>
                        <a
                            href="{{ route('tasks.by-date', ['date' => $nextDate->format('Y-m-d')]) }}"
                            aria-label="Ver próximo dia"
                            class="inline-flex items-center gap-2 rounded-md px-3 py-2 text-xs font-semibold uppercase tracking-wider text-paper/60 hover:bg-ember hover:text-paper transition">
                            Próximo
                            <span aria-hidden="true">→</span>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        {{-- 🎯 NOVO: resumo do dia usa as mesmas cores de status já conhecidas
             pelo usuário nas páginas de tarefas. --}}
        <section class="grid grid-cols-2 lg:grid-cols-4 gap-3">
            <div class="rounded-lg bg-paper border border-ink/10 p-4">
                <p class="text-2xl font-bold text-ink">{{ $dayStats['total'] }}</p>
                <p class="mt-1 text-[10px] font-semibold uppercase tracking-widest text-ink/45">Total</p>
            </div>
            <div class="rounded-lg bg-slate-50 border border-slate-200 p-4">
                <p class="text-2xl font-bold text-slate-700">{{ $dayStats['pending'] }}</p>
                <p class="mt-1 text-[10px] font-semibold uppercase tracking-widest text-slate-500">Pendentes</p>
            </div>
            <div class="rounded-lg bg-orange-50 border border-orange-200 p-4">
                <p class="text-2xl font-bold text-orange-700">{{ $dayStats['doing'] }}</p>
                <p class="mt-1 text-[10px] font-semibold uppercase tracking-widest text-orange-600">Fazendo</p>
            </div>
            <div class="rounded-lg bg-green-50 border border-green-200 p-4">
                <p class="text-2xl font-bold text-green-700">{{ $dayStats['completed'] }}</p>
                <p class="mt-1 text-[10px] font-semibold uppercase tracking-widest text-green-600">Concluídas</p>
            </div>
        </section>

        <section class="space-y-3">
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-sm font-semibold uppercase tracking-widest text-ink/55">
                    Ordem por horário
                </h2>
                <a href="{{ route('tasks.index') }}" class="text-xs font-semibold text-ember-dark hover:underline">
                    Ver todas as tarefas
                </a>
            </div>

            @forelse($tasks as $task)
                @php
                    $statusClasses = match ($task->status) {
                        'Fazendo' => 'border-orange-400 bg-orange-50 text-orange-700',
                        'Concluída' => 'border-green-500 bg-green-50 text-green-700',
                        default => 'border-slate-300 bg-slate-50 text-slate-700',
                    };
                @endphp

                {{-- 🎯 ALTERADO: o cartão diário deixou de ser um link para a
                     edição; somente o nome abre a visualização lateral de leitura. --}}
                <article
                    class="group block bg-paper border border-ink/10 rounded-xl shadow-sm hover:border-ember/60 hover:shadow-md transition">
                    <div class="grid sm:grid-cols-[110px_1fr_auto] sm:items-center gap-4 p-5">
                        <div class="sm:border-e sm:border-ink/10 sm:pe-5">
                            <p class="text-2xl font-bold text-ink">{{ $task->due_datetime?->format('H:i') }}</p>
                            <p class="mt-1 text-[10px] font-semibold uppercase tracking-widest text-ink/40">
                                Horário
                            </p>
                        </div>

                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <x-task-preview-trigger
                                    :task="$task"
                                    class="text-left font-bold text-ink group-hover:text-ember-dark hover:underline underline-offset-4 transition">
                                    {{ $task->title }}
                                </x-task-preview-trigger>
                                <span class="inline-flex rounded-full border px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider {{ $statusClasses }}">
                                    {{ $task->status }}
                                </span>
                                <x-task-priority :priority="$task->priority" class="text-xs" />
                            </div>

                            @if($task->description)
                                <p class="mt-2 text-sm text-ink/55">
                                    {{ \Illuminate\Support\Str::limit($task->description, 140) }}
                                </p>
                            @endif
                        </div>

                        {{-- 🎯 ALTERADO: a indicação esclarece que a consulta
                             rápida acontece pelo nome, sem sugerir edição. --}}
                        <span class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-widest text-ink/35">
                            Clique no nome
                        </span>
                    </div>
                </article>
            @empty
                <div class="rounded-xl border-2 border-dashed border-ink/15 bg-paper px-6 py-12 text-center">
                    <div class="w-12 h-12 mx-auto rounded-full bg-ink/5 text-ink/35 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3.75 9.75h16.5M5.25 5.25h13.5A1.5 1.5 0 0120.25 6.75v12a1.5 1.5 0 01-1.5 1.5H5.25a1.5 1.5 0 01-1.5-1.5v-12a1.5 1.5 0 011.5-1.5z" />
                        </svg>
                    </div>
                    <h3 class="mt-4 font-bold text-ink">Nenhuma tarefa neste dia</h3>
                    <p class="mt-1 text-sm text-ink/50">Use as setas acima ou volte ao calendário para escolher outra data.</p>
                </div>
            @endforelse
        </section>
    </div>
</x-app-layout>
