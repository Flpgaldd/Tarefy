<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-paper leading-tight">
                {{ __('Perfil') }}
            </h2>
        </div>
    </x-slot>

    {{-- 🎨 ALTERADO: card de perfil continua estreito (max-w-3xl, faz sentido pra
         informação pessoal), mas agora é um container SEPARADO do painel abaixo —
         antes os dois dividiam o mesmo max-w-3xl, o que deixava o painel apertado
         sem necessidade. --}}
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-paper rounded-lg shadow-sm overflow-hidden">

            {{-- ===================== FOTO DE PERFIL ===================== --}}
            <div class="bg-ink px-6 pt-10 pb-16 flex flex-col items-center">
                {{-- 🎯 ALTERADO: a borda cinza da foto principal do perfil foi
                     trocada por branca, seguindo o novo padrão dos avatares. --}}
                <div class="w-28 h-28 rounded-full bg-ember border-4 border-white flex items-center justify-center overflow-hidden">
                    @if($user->avatar_url)
                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                    @else
                        <span class="text-3xl font-bold text-ink">
                            {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                        </span>
                    @endif
                </div>
            </div>

            {{-- ===================== NOME + EMAIL ===================== --}}
            <div class="px-6 -mt-8 pb-6">
                <div class="bg-paper rounded-lg text-center pt-2">
                    <h1 class="text-2xl font-bold text-ink">{{ $user->name }} </h1>
                    <p class="text-sm text-ink/60 mt-1">{{ $user->email }}</p>
                </div>
            </div>

            <div class="border-t border-ink/10"></div>

            {{-- ===================== DESCRIÇÃO (BIO) ===================== --}}
            <div class="px-6 py-6">
                <h3 class="text-sm font-semibold uppercase tracking-widest text-ink/60 mb-3">
                    {{ __('Sobre') }}
                </h3>

                @if(isset($user->bio) && $user->bio)
                    <p class="text-ink leading-relaxed">{{ $user->bio }}</p>
                @else
                    <p class="text-ink/50 italic leading-relaxed">
                        {{ __('Você ainda não escreveu nada sobre você. Conte um pouco sobre o que você faz.') }}
                    </p>
                @endif
            </div>

            <div class="border-t border-ink/10"></div>

            {{-- ===================== AÇÃO ===================== --}}
            <div class="px-6 py-6 flex justify-center">
                <a href="{{ route('profile.edit') }}"
                    class="inline-flex items-center px-6 py-2.5 bg-ember hover:bg-ember-dark text-paper font-semibold text-xs uppercase tracking-widest rounded-md transition ease-in-out duration-150">
                    {{ __('Editar Perfil') }}
                </a>
            </div>
        </div>
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
    <div class="max-w-[1600px] mx-auto px-4 sm:px-5 py-8">
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
                            <p class="text-paper/80 line-through">{{ $task->title }}</p>
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
                    {{ __('Dias com bolinha laranja têm alguma tarefa marcada.') }}
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
                            <div class="aspect-square flex flex-col items-center justify-center rounded-md text-xs
                                {{ $isToday ? 'bg-ember text-paper font-bold' : ($isCurrentMonth ? 'text-paper' : 'text-paper/20') }}">
                                <span>{{ $cursor->day }}</span>
                                @if($hasTasks)
                                    <span class="w-1.5 h-1.5 rounded-full bg-ember mt-0.5 {{ $isToday ? 'bg-paper' : '' }}"></span>
                                @endif
                            </div>
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
                            <div class="flex items-center justify-between px-3 py-2 rounded-md {{ $isToday ? 'bg-ember' : 'bg-ink-soft' }}">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-semibold uppercase text-paper/70">
                                        {{ $day->translatedFormat('D') }}
                                    </span>
                                    <span class="text-sm font-bold text-paper">
                                        {{ $day->format('d/m') }}
                                    </span>
                                </div>

                                @if($count > 0)
                                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-paper">
                                        <span class="w-1.5 h-1.5 rounded-full bg-paper"></span>
                                        {{ $count }} {{ $count === 1 ? __('tarefa') : __('tarefas') }}
                                    </span>
                                @else
                                    <span class="text-xs text-paper/30">—</span>
                                @endif
                            </div>
                        @endfor
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
