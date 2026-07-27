<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-paper leading-tight">
            {{ __('Início') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

        {{-- ===================== BOAS-VINDAS ===================== --}}
        <div class="bg-ink border-l-4 border-ember rounded-lg p-8">
            <h1 class="text-2xl sm:text-3xl font-bold text-paper">
                {{ __('Bem-vindo(a), :name!', ['name' => explode(' ', Auth::user()->name)[0]]) }}
            </h1>
            <p class="mt-2 text-paper/60">
                @if($total > 0)
                    {{ __('Aqui está um resumo do que está acontecendo com suas tarefas.') }}
                @else
                    {{ __('Você ainda não tem tarefas. Que tal criar a primeira?') }}
                @endif
            </p>
            <a href="{{ route('tasks.index') }}"
                class="inline-flex items-center mt-5 px-5 py-2.5 bg-ember hover:bg-ember-dark text-paper font-semibold text-xs uppercase tracking-widest rounded-md transition ease-in-out duration-150">
                {{ $total > 0 ? __('Ver minhas tarefas') : __('Criar minha primeira tarefa') }}
            </a>
        </div>

        {{-- ===================== ESTATÍSTICAS ===================== --}}
        {{--
            🎯 Os 4 números abaixo vêm de Auth::user()->taskStats() (passado pela
            rota em routes/web.php), a MESMA função usada em "Minhas Tarefas" —
            então "Total" e "Concluídas" aqui sempre batem com os totais de lá.
            "Para fazer" = Pendente + Fazendo somados (as duas colunas que na
            tela de tarefas aparecem separadas viram uma métrica só aqui, pra
            caber nos 4 cards que você pediu).
        --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

            {{-- Total --}}
            <div class="bg-ink rounded-lg p-6">
                <div class="w-10 h-10 rounded-full bg-ember/15 flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-ember" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" />
                    </svg>
                </div>
                <p class="text-3xl font-bold text-paper">{{ $total }}</p>
                <p class="text-xs uppercase tracking-widest text-paper/50 mt-1">{{ __('Tarefas criadas') }}</p>
            </div>

            {{-- Atrasadas — card com destaque visual próprio, é a métrica mais "acionável" --}}
            <div class="bg-paper border-2 {{ $overdue > 0 ? 'border-ember' : 'border-ink/10' }} rounded-lg p-6">
                <div class="w-10 h-10 rounded-full {{ $overdue > 0 ? 'bg-ember' : 'bg-ink/10' }} flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 {{ $overdue > 0 ? 'text-paper' : 'text-ink/40' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>
                </div>
                <p class="text-3xl font-bold text-ink">{{ $overdue }}</p>
                <p class="text-xs uppercase tracking-widest text-ink/50 mt-1">{{ __('Atrasadas') }}</p>
            </div>

            {{-- Para fazer --}}
            <div class="bg-ink rounded-lg p-6">
                <div class="w-10 h-10 rounded-full bg-ember/15 flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-ember" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <p class="text-3xl font-bold text-paper">{{ $todo }}</p>
                <p class="text-xs uppercase tracking-widest text-paper/50 mt-1">{{ __('Para fazer') }}</p>
            </div>

            {{-- Concluídas --}}
            <div class="bg-ink rounded-lg p-6">
                <div class="w-10 h-10 rounded-full bg-ember/15 flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-ember" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <p class="text-3xl font-bold text-paper">{{ $completed }}</p>
                <p class="text-xs uppercase tracking-widest text-paper/50 mt-1">{{ __('Concluídas') }}</p>
            </div>
        </div>

        {{-- 🎯 ALTERADO: primeira versão da tabela de tarefas adicionada abaixo
             dos indicadores do dashboard. Ela lista as tarefas já criadas pelo
             usuário autenticado; a estrutura foi mantida simples para receber
             as próximas melhorias solicitadas sem alterar a fonte dos dados. --}}
        <div class="bg-paper rounded-lg shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-ink/10">
                <h3 class="text-sm font-semibold uppercase tracking-widest text-ink/60">
                    {{ __('Tarefas criadas') }}
                </h3>

                {{-- 🎯 ALTERADO: filtro por status adicionado à tabela do dashboard,
                     seguindo os mesmos três valores usados em "Minhas Tarefas".
                     O botão Limpar retorna à tabela completa. --}}
                <form method="GET" action="{{ route('dashboard') }}" class="mt-4 flex flex-wrap items-end gap-3">
                    <div>
                        <label for="dashboard-status" class="block text-sm font-medium text-ink mb-1">
                            Status
                        </label>
                        <select
                            id="dashboard-status"
                            name="status"
                            class="border-ink/20 bg-paper text-ink focus:ring-ember focus:border-ember rounded-md shadow-sm text-sm">
                            <option value="all" {{ $selectedStatus === 'all' ? 'selected' : '' }}>Todos</option>
                            <option value="Pendente" {{ $selectedStatus === 'Pendente' ? 'selected' : '' }}>Pendente</option>
                            <option value="Fazendo" {{ $selectedStatus === 'Fazendo' ? 'selected' : '' }}>Fazendo</option>
                            <option value="Concluída" {{ $selectedStatus === 'Concluída' ? 'selected' : '' }}>Concluída</option>
                        </select>
                    </div>

                    <div>
                        {{-- 🎯 ALTERADO: prioridade adicionada ao filtro do dashboard;
                             ela pode ser combinada com qualquer status selecionado. --}}
                        <label for="dashboard-priority" class="block text-sm font-medium text-ink mb-1">
                            Prioridade
                        </label>
                        <select
                            id="dashboard-priority"
                            name="priority"
                            class="border-ink/20 bg-paper text-ink focus:ring-ember focus:border-ember rounded-md shadow-sm text-sm">
                            <option value="all" {{ $selectedPriority === 'all' ? 'selected' : '' }}>Todas</option>
                            <option value="low" {{ $selectedPriority === 'low' ? 'selected' : '' }}>Baixa</option>
                            <option value="medium" {{ $selectedPriority === 'medium' ? 'selected' : '' }}>Média</option>
                            <option value="high" {{ $selectedPriority === 'high' ? 'selected' : '' }}>Alta</option>
                            <option value="urgent" {{ $selectedPriority === 'urgent' ? 'selected' : '' }}>Urgente!</option>
                        </select>
                    </div>

                    <button
                        type="submit"
                        class="inline-flex items-center px-4 py-2 bg-ember hover:bg-ember-dark text-paper font-semibold text-xs uppercase tracking-widest rounded-md transition ease-in-out duration-150">
                        Filtrar
                    </button>

                    <a
                        href="{{ route('dashboard') }}"
                        class="inline-flex items-center px-4 py-2 bg-paper border-2 border-ink hover:bg-ink hover:text-paper text-ink font-semibold text-xs uppercase tracking-widest rounded-md transition ease-in-out duration-150">
                        Limpar
                    </a>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-ink/10">
                    <thead class="bg-ink">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-widest text-paper/70">
                                {{ __('Tarefa') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-widest text-paper/70">
                                {{ __('Status') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-widest text-paper/70">
                                {{ __('Prioridade') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-widest text-paper/70">
                                {{ __('Vencimento') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-widest text-paper/70">
                                {{ __('Criada em') }}
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-ink/10">
                        @forelse ($tasks as $task)
                            <tr>
                                <td class="px-6 py-4 text-sm font-medium text-ink">
                                    {{-- 🎯 ALTERADO: tarefas da página inicial
                                         também abrem a página única de detalhes. --}}
                                    <a
                                        href="{{ route('tasks.show', $task) }}"
                                        class="hover:text-ember-dark hover:underline underline-offset-4 transition">
                                        {{ $task->title }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 text-sm text-ink/70">
                                    {{ $task->status }}
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    {{-- 🎯 ALTERADO: o valor técnico salvo no banco
                                         é apresentado somente pelo nome e pela cor
                                         correspondente à prioridade. --}}
                                    <x-task-priority :priority="$task->priority" />
                                </td>
                                <td class="px-6 py-4 text-sm text-ink/70 whitespace-nowrap">
                                    {{ $task->due_datetime?->format('d/m/Y H:i') ?? '—' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-ink/70 whitespace-nowrap">
                                    {{ $task->created_at?->format('d/m/Y H:i') ?? '—' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-sm text-ink/50">
                                    {{ __('Nenhuma tarefa criada até o momento.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
