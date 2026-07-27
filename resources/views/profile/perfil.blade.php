<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-paper leading-tight">
                {{ __('Perfil') }}
            </h2>
        </div>
    </x-slot>

    {{-- 🎨 ALTERADO: foto, nome e e-mail foram reunidos em um hero de perfil
         responsivo. O bloco usa o preto do header, o laranja das ações e o
         branco do conteúdo para combinar com o restante da identidade Tarefy. --}}
    {{-- 🎨 ALTERADO: o padding inferior foi reduzido para aproximar o painel das
         informações do usuário sem deixar os dois cartões visualmente colados. --}}
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 pb-3">
        <section class="relative overflow-hidden bg-ink border border-ink-soft rounded-2xl shadow-xl">
            <div class="absolute inset-x-0 top-0 h-1.5 bg-ember"></div>
            <div class="pointer-events-none absolute -top-24 -end-24 w-64 h-64 rounded-full border border-ember/20"></div>
            <div class="pointer-events-none absolute -top-14 -end-14 w-40 h-40 rounded-full border border-paper/10"></div>

            <div class="relative px-6 py-10 sm:px-10 sm:py-12">
                <div class="flex flex-col sm:flex-row sm:items-center gap-7 sm:gap-9">

                    {{-- 🎯 ALTERADO: avatar maior com borda branca, aro laranja e
                         indicador decorativo, mantendo o fallback com a inicial. --}}
                    <div class="relative shrink-0 self-center sm:self-auto">
                        <div class="absolute -inset-2 rounded-full border border-paper/15"></div>
                        <div class="relative w-32 h-32 sm:w-36 sm:h-36 rounded-full bg-ember border-4 border-white ring-4 ring-ember/30 flex items-center justify-center overflow-hidden shadow-2xl">
                            @if($user->avatar_url)
                                <img
                                    src="{{ $user->avatar_url }}"
                                    alt="Foto de perfil de {{ $user->name }}"
                                    class="w-full h-full object-cover">
                            @else
                                <span class="text-4xl font-bold text-ink">
                                    {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                                </span>
                            @endif
                        </div>

                        <span class="absolute bottom-1 end-1 w-9 h-9 rounded-full bg-ember border-4 border-ink flex items-center justify-center shadow-lg">
                            <svg class="w-4 h-4 text-paper" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 12l4 4L19 6" />
                            </svg>
                        </span>
                    </div>

                    {{-- 🎯 ALTERADO: nome e e-mail ganharam hierarquia visual,
                         melhor contraste e ícone de identificação da conta. --}}
                    <div class="min-w-0 flex-1 text-center sm:text-left">
                        <div class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.2em] text-ember">
                            <span class="w-2 h-2 rounded-full bg-ember"></span>
                            Perfil pessoal
                        </div>

                        <h1 class="mt-3 text-3xl sm:text-4xl font-bold text-paper break-words">
                            {{ $user->name }}
                        </h1>

                        <div class="mt-4 inline-flex max-w-full items-center gap-2 rounded-full border border-paper/10 bg-paper/5 px-4 py-2 text-sm text-paper/65">
                            <svg class="w-4 h-4 shrink-0 text-ember" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5A2.25 2.25 0 0119.5 19.5h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0l-8.69 5.517a2 2 0 01-2.12 0L2.25 6.75" />
                            </svg>
                            <span class="truncate">{{ $user->email }}</span>
                        </div>
                    </div>

                    {{-- 🎯 ALTERADO: ação de edição integrada ao cabeçalho do
                         perfil, com ícone e comportamento responsivo. --}}
                    <a
                        href="{{ route('profile.edit') }}"
                        class="inline-flex self-center sm:self-auto items-center justify-center gap-2 px-5 py-3 bg-ember hover:bg-ember-dark text-paper font-semibold text-xs uppercase tracking-widest rounded-lg shadow-lg transition ease-in-out duration-150">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.862 4.487zM19.5 7.125L16.875 4.5M18 13.5V19.125A1.875 1.875 0 0116.125 21H4.875A1.875 1.875 0 013 19.125V7.875A1.875 1.875 0 014.875 6H10.5" />
                        </svg>
                        {{ __('Editar Perfil') }}
                    </a>
                </div>
            </div>

            {{-- 🎨 ALTERADO: a biografia permanece junto da identificação, mas
                 em uma área branca para equilibrar o contraste do cartão. --}}
            <div class="relative bg-paper px-6 py-6 sm:px-10">
                <div class="flex items-start gap-4">
                    <span class="w-10 h-10 rounded-full bg-ink text-ember flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 9.75a3.375 3.375 0 116.75 0 3.375 3.375 0 01-6.75 0zM3.75 19.5a8.25 8.25 0 0116.5 0" />
                        </svg>
                    </span>

                    <div>
                        <h2 class="text-xs font-semibold uppercase tracking-widest text-ink/45">
                            {{ __('Sobre') }}
                        </h2>

                        @if(isset($user->bio) && $user->bio)
                            <p class="mt-2 text-ink leading-relaxed">{{ $user->bio }}</p>
                        @else
                            <p class="mt-2 text-ink/50 italic leading-relaxed">
                                {{ __('Você ainda não escreveu nada sobre você. Conte um pouco sobre o que você faz.') }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    </div>

    {{--
        =========================================================================
        🎨 ALTERADO — duas mudanças pedidas:
        1. Container próprio bem mais largo (max-w-[1600px], só um pouco de
           respiro nas bordas com px-4) em vez de herdar o max-w-3xl do card de
           perfil — os 3 blocos agora ocupam quase a largura toda do site.
        2. TODO texto (letras e números) nas 3 colunas passou pra branco/paper.
           Pra isso funcionar sem ficar "branco no branco", as 3 colunas agora
           são todas de fundo escuro (bg-ink / bg-ink-soft) — antes o card do
           meio e o da direita eram bg-paper (branco), o que é incompatível com
           texto branco por cima.
        =========================================================================
    --}}
    {{-- 🎨 ALTERADO: o painel sobe com `pt-3`, diminuindo o antigo espaço vazio
         entre perfil, indicadores, tarefas concluídas e calendário. --}}
    <div class="max-w-[1600px] mx-auto px-4 sm:px-5 pt-3 pb-8">
        <h2 class="text-sm font-semibold uppercase tracking-widest text-ink/60 mb-4">
            {{ __('Painel') }}
        </h2>

        <div class="grid lg:grid-cols-3 gap-6 items-start">

            {{-- ===================== COLUNA ESQUERDA: DASHBOARD (modelo novo) ===================== --}}
            {{-- 🎨 ALTERADO: era 4 caixas empilhadas separadas; agora é 1 cartão
                 único, com linhas (ícone + número + rótulo) separadas por
                 divisórias finas — modelo diferente do dashboard principal,
                 como pedido, e todo em fundo escuro pra caber texto branco. --}}
            <div class="bg-ink rounded-lg p-6">
                <div class="space-y-5 divide-y divide-paper/10">

                    <div class="flex items-center gap-4">
                        <div class="w-11 h-11 rounded-full bg-ember/15 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-ember" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-paper">{{ $stats['total'] }}</p>
                            <p class="text-xs uppercase tracking-widest text-paper/60">{{ __('Tarefas criadas') }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 pt-5">
                        <div class="w-11 h-11 rounded-full {{ $stats['overdue'] > 0 ? 'bg-ember' : 'bg-paper/10' }} flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 {{ $stats['overdue'] > 0 ? 'text-ink' : 'text-paper/40' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-paper">{{ $stats['overdue'] }}</p>
                            <p class="text-xs uppercase tracking-widest text-paper/60">{{ __('Atrasadas') }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 pt-5">
                        <div class="w-11 h-11 rounded-full bg-ember/15 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-ember" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-paper">{{ $stats['todo'] }}</p>
                            <p class="text-xs uppercase tracking-widest text-paper/60">{{ __('Para fazer') }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 pt-5">
                        <div class="w-11 h-11 rounded-full bg-ember/15 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-ember" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-paper">{{ $stats['completed'] }}</p>
                            <p class="text-xs uppercase tracking-widest text-paper/60">{{ __('Concluídas') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===================== COLUNA CENTRO: TAREFAS CONCLUÍDAS ===================== --}}
            <div class="bg-ink rounded-lg p-6">
                <h3 class="text-sm font-semibold uppercase tracking-widest text-paper/60 mb-4">
                    {{ __('Tarefas concluídas') }}
                </h3>

                <div class="space-y-2 max-h-[480px] overflow-y-auto pr-1">
                    @forelse($completedTasks as $task)
                        <div class="bg-ink-soft border-l-4 border-ember rounded-md px-4 py-3">
                            {{-- 🎯 ALTERADO: tarefas concluídas abrem a consulta
                                 rápida sem levar diretamente para a edição. --}}
                            <x-task-preview-trigger
                                :task="$task"
                                class="text-left text-paper/80 line-through hover:text-ember transition">
                                {{ $task->title }}
                            </x-task-preview-trigger>
                            @if($task->due_datetime)
                                <p class="text-xs text-paper/40 mt-0.5">{{ $task->due_datetime->format('d/m/Y H:i') }}</p>
                            @endif
                        </div>
                    @empty
                        <p class="text-sm text-paper/40 italic">{{ __('Nenhuma tarefa concluída ainda.') }}</p>
                    @endforelse
                </div>
            </div>

            {{-- ===================== COLUNA DIREITA: CALENDÁRIO ===================== --}}
            <div class="bg-ink rounded-lg p-6" x-data="{ view: 'monthly' }">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold uppercase tracking-widest text-paper/60">
                        {{ __('Calendário') }}
                    </h3>

                    <div class="inline-flex rounded-md bg-ink-soft p-1 text-xs font-semibold uppercase tracking-widest">
                        <button type="button" x-on:click="view = 'weekly'"
                            :class="view === 'weekly' ? 'bg-ember text-paper' : 'text-paper/50'"
                            class="px-3 py-1 rounded transition">
                            {{ __('Semanal') }}
                        </button>
                        <button type="button" x-on:click="view = 'monthly'"
                            :class="view === 'monthly' ? 'bg-ember text-paper' : 'text-paper/50'"
                            class="px-3 py-1 rounded transition">
                            {{ __('Mensal') }}
                        </button>
                    </div>
                </div>

                <p class="text-xs text-paper/40 mb-3">
                    {{ __('Dias destacados possuem tarefas. Clique para ver a programação completa.') }}
                </p>

                {{-- -------- VISÃO MENSAL -------- --}}
                <div x-show="view === 'monthly'">
                    @php
                        $calendarStart = now()->startOfMonth()->startOfWeek(\Carbon\Carbon::SUNDAY);
                        $calendarEnd = now()->endOfMonth()->endOfWeek(\Carbon\Carbon::SUNDAY);
                        $currentMonth = now()->month;
                        $today = now()->format('Y-m-d');
                    @endphp

                    <p class="text-center text-sm font-semibold text-paper mb-3 capitalize">
                        {{ now()->translatedFormat('F \\d\\e Y') }}
                    </p>

                    <div class="grid grid-cols-7 gap-1 text-center text-[10px] font-semibold text-paper/40 uppercase mb-1">
                        <span>D</span><span>S</span><span>T</span><span>Q</span><span>Q</span><span>S</span><span>S</span>
                    </div>

                    <div class="grid grid-cols-7 gap-1">
                        @php $cursor = $calendarStart->copy(); @endphp
                        @while($cursor->lte($calendarEnd))
                            @php
                                $dateKey = $cursor->format('Y-m-d');
                                $hasTasks = $taskDates->has($dateKey);
                                $isCurrentMonth = $cursor->month === $currentMonth;
                                $isToday = $dateKey === $today;
                            @endphp
                            @if($hasTasks)
                                {{-- 🎯 ALTERADO: somente dias que possuem tarefas
                                     viram links para a nova agenda diária. --}}
                                <a
                                    href="{{ route('tasks.by-date', ['date' => $dateKey]) }}"
                                    title="Ver tarefas de {{ $cursor->format('d/m/Y') }}"
                                    aria-label="Ver tarefas de {{ $cursor->format('d/m/Y') }}"
                                    class="aspect-square flex flex-col items-center justify-center rounded-md text-xs font-bold ring-1 ring-ember/40 transition hover:bg-ember hover:text-paper hover:ring-ember focus:outline-none focus:ring-2 focus:ring-paper
                                        {{ $isToday ? 'bg-ember text-paper' : ($isCurrentMonth ? 'bg-ember/10 text-paper' : 'bg-ember/5 text-paper/50') }}">
                                    <span>{{ $cursor->day }}</span>
                                    <span class="w-1.5 h-1.5 rounded-full bg-ember mt-0.5 {{ $isToday ? 'bg-paper' : '' }}"></span>
                                </a>
                            @else
                                <div class="aspect-square flex flex-col items-center justify-center rounded-md text-xs
                                    {{ $isToday ? 'bg-ember text-paper font-bold' : ($isCurrentMonth ? 'text-paper' : 'text-paper/20') }}">
                                    <span>{{ $cursor->day }}</span>
                                </div>
                            @endif
                            @php $cursor->addDay(); @endphp
                        @endwhile
                    </div>
                </div>

                {{-- -------- VISÃO SEMANAL -------- --}}
                <div x-show="view === 'weekly'" style="display: none;">
                    @php
                        $weekStart = now()->startOfWeek(\Carbon\Carbon::SUNDAY);
                        $today = now()->format('Y-m-d');
                    @endphp

                    <div class="space-y-2">
                        @for($i = 0; $i < 7; $i++)
                            @php
                                $day = $weekStart->copy()->addDays($i);
                                $dateKey = $day->format('Y-m-d');
                                $count = $taskDates->get($dateKey, 0);
                                $isToday = $dateKey === $today;
                            @endphp
                            @if($count > 0)
                                {{-- 🎯 ALTERADO: na visão semanal, toda a linha do
                                     dia com tarefa também é clicável. --}}
                                <a
                                    href="{{ route('tasks.by-date', ['date' => $dateKey]) }}"
                                    class="flex items-center justify-between px-3 py-2 rounded-md transition hover:bg-ember focus:outline-none focus:ring-2 focus:ring-paper {{ $isToday ? 'bg-ember' : 'bg-ink-soft ring-1 ring-ember/30' }}">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-semibold uppercase text-paper/70">
                                            {{ $day->translatedFormat('D') }}
                                        </span>
                                        <span class="text-sm font-bold text-paper">
                                            {{ $day->format('d/m') }}
                                        </span>
                                    </div>
                                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-paper">
                                        <span class="w-1.5 h-1.5 rounded-full bg-paper"></span>
                                        {{ $count }} {{ $count === 1 ? __('tarefa') : __('tarefas') }}
                                    </span>
                                </a>
                            @else
                                <div class="flex items-center justify-between px-3 py-2 rounded-md {{ $isToday ? 'bg-ember' : 'bg-ink-soft' }}">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-semibold uppercase text-paper/70">
                                            {{ $day->translatedFormat('D') }}
                                        </span>
                                        <span class="text-sm font-bold text-paper">
                                            {{ $day->format('d/m') }}
                                        </span>
                                    </div>
                                    <span class="text-xs text-paper/30">—</span>
                                </div>
                            @endif
                        @endfor
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
