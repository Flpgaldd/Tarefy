<x-app-layout>
    {{--
        🎨 ALTERADO (estrutural): no arquivo original, TODO o conteúdo da página
        (formulário de criar tarefa + <main>/@yield) estava, por engano, dentro do
        <x-slot name="header">. Isso fazia a página inteira renderizar dentro da
        faixa de cabeçalho, quebrando a hierarquia visual. Aqui o <x-slot header>
        guarda só o título (função correta do slot); todo o resto foi movido para
        o corpo da página, abaixo. Nenhuma rota, nome de campo, @csrf, @method,
        condicional ou variável foi alterada — só a posição no HTML.
    --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-paper leading-tight">
            {{ __('Criar Tarefa') }}
        </h2>
    </x-slot>

    @if(session('msg') || session('error') || session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">
            {{-- 🎨 ALTERADO: mensagens de sessão agora em cartões, não texto solto.
                 "error" mantém vermelho (semântica de falha); "msg"/"success" usam laranja. --}}
            @if(session('msg'))<p class="mb-2 px-4 py-3 rounded-md bg-ember/10 border border-ember text-ember-dark text-sm font-medium">{{ session('msg') }}</p>@endif
            @if(session('error'))<p class="mb-2 px-4 py-3 rounded-md bg-red-50 border border-red-700 text-red-700 text-sm font-medium">{{ session('error') }}</p>@endif
            @if(session('success'))<p class="mb-2 px-4 py-3 rounded-md bg-ember/10 border border-ember text-ember-dark text-sm font-medium">{{ session('success') }}</p>@endif
        </div>
    @endif

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

        {{-- ===================== CRIAR TAREFA ===================== --}}
        {{-- 🎨 ALTERADO: cartão branco com borda esquerda laranja (mesmo padrão do
             header preto), inputs com classes de foco em ember, label em preto. --}}
        <div class="bg-paper border-l-4 border-ember rounded-lg shadow-sm">
            <div class="p-6">
                <h3 class="text-sm font-semibold uppercase tracking-widest text-ink/60 mb-4">{{ __('Nova tarefa') }}</h3>
                <form method="POST" action="{{ route('tasks.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block font-medium text-sm text-ink mb-1">Nome da tarefa</label>
                        <input type="text" name="title" placeholder="Nova tarefa" required
                            class="w-full border-ink/20 bg-paper text-ink focus:border-ember focus:ring-ember rounded-md shadow-sm">
                    </div>
                    <div class="flex flex-wrap gap-4">
                        <div>
                            <label class="block font-medium text-sm text-ink mb-1">Data de vencimento</label>
                            {{-- 🔒 ALTERADO: min/max adicionados. Impede o usuário de escolher uma
                                 data inválida direto no calendário do navegador, refletindo a mesma
                                 regra aplicada no backend (ValidTaskDueDate): não antes de hoje/ano
                                 atual, não além de 1 ano a partir de agora. --}}
                            <input type="datetime-local" name="due_datetime" required
                                min="{{ now()->format('Y-m-d\TH:i') }}"
                                max="{{ now()->addYear()->format('Y-m-d\TH:i') }}"
                                class="border-ink/20 bg-paper text-ink focus:border-ember focus:ring-ember rounded-md shadow-sm">
                        </div>
                        <div>
                            <label class="block font-medium text-sm text-ink mb-1">Adicionar lembrete</label>
                            <input type="datetime-local" name="reminder_datetime"
                                class="border-ink/20 bg-paper text-ink focus:border-ember focus:ring-ember rounded-md shadow-sm">
                        </div>
                        <div x-data="{ priority: '{{ old('priority', \App\Models\Task::PRIORITY_MEDIUM) }}' }">
                            {{-- 🎯 ALTERADO: prioridade adicionada à criação da tarefa.
                                 O texto do select muda de cor conforme o nível escolhido
                                 e o valor selecionado é enviado para a coluna `priority`. --}}
                            <label for="priority" class="block font-medium text-sm text-ink mb-1">
                                Prioridade
                            </label>
                            <select
                                id="priority"
                                name="priority"
                                x-model="priority"
                                :class="{
                                    'text-green-600': priority === 'low',
                                    'text-yellow-600': priority === 'medium',
                                    'text-orange-600': priority === 'high',
                                    'text-red-700': priority === 'urgent'
                                }"
                                class="border-ink/20 bg-paper focus:ring-ember focus:border-ember rounded-md shadow-sm font-semibold">
                                {{-- 🎯 ALTERADO: números e hífens removidos dos
                                     rótulos; os valores salvos no banco permanecem iguais. --}}
                                <option class="text-green-600" value="low">Baixa</option>
                                <option class="text-yellow-600" value="medium">Média</option>
                                <option class="text-orange-600" value="high">Alta</option>
                                <option class="text-red-700" value="urgent">Urgente!</option>
                            </select>
                            <x-input-error :messages="$errors->get('priority')" class="mt-2" />
                        </div>
                    </div>
                    <input type="hidden" name="status" value="Pendente">

                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-ember border border-transparent rounded-md font-semibold text-xs text-paper uppercase tracking-widest hover:bg-ember-dark focus:outline-none focus:ring-2 focus:ring-ink focus:ring-offset-2 transition ease-in-out duration-150">
                        {{-- 🎨 ALTERADO: de style inline verde (#66db42) para classe bg-ember,
                             alinhado com o botão primário do resto do app. --}}
                        Criar tarefa
                    </button>
                </form>
            </div>
        </div>

        {{-- ===================== FILTROS ===================== --}}
        {{-- 🎨 ALTERADO: mesmo padrão de cartão, título em caixa alta discreto
             (eyebrow) em vez de <h4>/<hr> soltos. --}}
        <div class="bg-paper rounded-lg shadow-sm">
            <div class="p-6">
                <h3 class="text-sm font-semibold uppercase tracking-widest text-ink/60 mb-4">{{ __('Filtros') }}</h3>
                <form method="GET" action="{{ route('tasks.search') }}" class="space-y-4">
                    @csrf
                    <div class="flex flex-wrap items-end gap-4">
                        <div>
                            <label for="status" class="block text-sm font-medium text-ink mb-1">Status</label>
                            <select name="status" id="status"
                                class="border-ink/20 bg-paper text-ink focus:ring-ember focus:border-ember rounded-md shadow-sm text-sm">
                                <option value="all">Todos</option>
                                <option value="Pendente" {{ request('status') == 'Pendente' ? 'selected' : '' }}>Pendente</option>
                                <option value="Fazendo" {{ request('status') == 'Fazendo' ? 'selected' : '' }}>Fazendo</option>
                                <option value="Concluída" {{ request('status') == 'Concluída' ? 'selected' : '' }}>Concluída</option>
                            </select>
                        </div>
                        <div>
                            {{-- 🎯 ALTERADO: prioridade adicionada ao filtro de
                                 "Minhas Tarefas" e combinada com status e busca. --}}
                            <label for="filter-priority" class="block text-sm font-medium text-ink mb-1">
                                Prioridade
                            </label>
                            <select
                                name="priority"
                                id="filter-priority"
                                class="border-ink/20 bg-paper text-ink focus:ring-ember focus:border-ember rounded-md shadow-sm text-sm">
                                <option value="all">Todas</option>
                                <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Baixa</option>
                                <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Média</option>
                                <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>Alta</option>
                                <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Urgente!</option>
                            </select>
                        </div>
                        <div>
                            <label for="title" class="block text-sm font-medium text-ink mb-1">Buscar</label>
                            <input type="text" id="title" name="title" placeholder="Buscar tarefa" value="{{ request('title') }}"
                                class="border-ink/20 bg-paper text-ink focus:ring-ember focus:border-ember rounded-md shadow-sm text-sm h-[38px]">
                        </div>
                        <div class="flex gap-2">
                            {{-- 🎨 ALTERADO: "Filtrar" de bg-blue-500 para bg-ember (ação principal
                                 do filtro); "Limpar" vira contorno preto (ação secundária). --}}
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-ember hover:bg-ember-dark text-paper font-semibold text-xs uppercase tracking-widest rounded-md transition ease-in-out duration-150">
                                Filtrar
                            </button>
                            <a href="{{ route('tasks.index') }}">
                                <button type="button"
                                    class="inline-flex items-center px-4 py-2 bg-paper border-2 border-ink hover:bg-ink hover:text-paper text-ink font-semibold text-xs uppercase tracking-widest rounded-md transition ease-in-out duration-150">
                                    Limpar
                                </button>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- ===================== ESTATÍSTICAS ===================== --}}
        {{-- 🎨 ALTERADO: de parágrafos empilhados para 4 cartões pretos em grid,
             com o número em laranja grande — são os dados que devem ter "maior
             destaque visual" conforme pedido no briefing. --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="bg-ink rounded-lg p-5 text-center">
                <p class="text-3xl font-bold text-ember">{{ $total }}</p>
                <p class="text-xs uppercase tracking-widest text-paper/60 mt-1">Total</p>
            </div>
            <div class="bg-ink rounded-lg p-5 text-center">
                <p class="text-3xl font-bold text-ember">{{ $pending }}</p>
                <p class="text-xs uppercase tracking-widest text-paper/60 mt-1">Pendentes</p>
            </div>
            <div class="bg-ink rounded-lg p-5 text-center">
                <p class="text-3xl font-bold text-ember">{{ $doing }}</p>
                <p class="text-xs uppercase tracking-widest text-paper/60 mt-1">Fazendo</p>
            </div>
            <div class="bg-ink rounded-lg p-5 text-center">
                <p class="text-3xl font-bold text-ember">{{ $completed }}</p>
                <p class="text-xs uppercase tracking-widest text-paper/60 mt-1">Concluídas</p>
            </div>
        </div>

        {{-- ===================== LISTA DE TAREFAS ===================== --}}
        {{-- 🎨 ALTERADO: de <div>/<hr> repetidos para cartões individuais com borda
             lateral colorida por status (preto=Pendente, laranja=Fazendo,
             preto+selo=Concluída), badge de status e ações alinhadas à direita. --}}
        <div class="space-y-3">
            @foreach($tasks as $task)
                @php
                    $isLate = $task->due_datetime && \Carbon\Carbon::parse($task->due_datetime)->isPast() && $task->status !== 'Concluída';
                    $borderColor = match($task->status) {
                        'Fazendo' => 'border-ember',
                        'Concluída' => 'border-ink',
                        default => 'border-ink/20',
                    };
                    // 🎯 ALTERADO: classes específicas deixam o seletor de status
                    // maior, mais legível e com cor, ponto e borda coerentes.
                    $statusSelectClass = match($task->status) {
                        'Fazendo' => 'border-orange-300 bg-orange-50 text-orange-700',
                        'Concluída' => 'border-green-300 bg-green-50 text-green-700',
                        default => 'border-slate-300 bg-slate-50 text-slate-700',
                    };
                    $statusDotClass = match($task->status) {
                        'Fazendo' => 'bg-orange-500',
                        'Concluída' => 'bg-green-500',
                        default => 'bg-slate-500',
                    };
                @endphp
                <div class="bg-paper border-l-4 {{ $borderColor }} rounded-lg shadow-sm p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            {{-- 🎯 ALTERADO: clicar no nome agora abre somente a
                                 aba lateral de consulta, sem entrar na edição. --}}
                            <x-task-preview-trigger
                                :task="$task"
                                class="text-left font-bold text-base text-ink hover:text-ember-dark hover:underline underline-offset-4 transition {{ $task->status === 'Concluída' ? 'line-through text-ink/50' : '' }}">
                                {{ $task->title }}
                            </x-task-preview-trigger>
                            {{-- 🎯 ALTERADO: o badge fixo de status virou um select
                                 que envia a alteração automaticamente para uma rota
                                 PATCH exclusiva ao escolher um dos três status. --}}
                            <form method="POST" action="{{ route('tasks.status.update', $task) }}" class="relative inline-flex items-center">
                                @csrf
                                @method('PATCH')
                                <label for="task-status-{{ $task->id }}" class="sr-only">
                                    Alterar status de {{ $task->title }}
                                </label>
                                <span class="pointer-events-none absolute start-3 w-2 h-2 rounded-full {{ $statusDotClass }}"></span>
                                <select
                                    id="task-status-{{ $task->id }}"
                                    name="status"
                                    onchange="this.form.submit()"
                                    class="min-w-36 border-2 rounded-lg py-2 ps-7 pe-9 text-xs font-bold uppercase tracking-wider shadow-sm cursor-pointer transition hover:shadow-md focus:ring-2 focus:ring-ember focus:border-ember {{ $statusSelectClass }}">
                                    <option value="Pendente" {{ $task->status === 'Pendente' ? 'selected' : '' }}>Pendente</option>
                                    <option value="Fazendo" {{ $task->status === 'Fazendo' ? 'selected' : '' }}>Fazendo</option>
                                    <option value="Concluída" {{ $task->status === 'Concluída' ? 'selected' : '' }}>Concluída</option>
                                </select>
                            </form>

                            {{-- 🎯 ALTERADO: prioridade exibida ao lado do status
                                 com a cor padronizada pelo componente compartilhado. --}}
                            <x-task-priority :priority="$task->priority" class="text-xs" />
                            @if($isLate)
                                <span class="text-[11px] font-bold uppercase tracking-widest text-ember-dark">⚠ Atrasada</span>
                            @endif
                        </div>
                        <p class="text-sm text-ink/60 mt-1">{{ $task->due_datetime }}</p>
                    </div>
                    <div class="flex items-center gap-3 shrink-0">
                        {{-- 🎯 ALTERADO: este permanece como o único acesso da
                             lista à página completa, onde a edição é permitida. --}}
                        <a href="{{ route('tasks.show', $task) }}"
                            class="text-xs font-semibold uppercase tracking-widest text-ink hover:text-ember-dark transition">
                            Ver detalhes
                        </a>
                        <button type="button"
                            onclick="document.getElementById('confirm-task-deletion-{{ $task->id }}').showModal()"
                            class="inline-flex items-center px-3 py-1.5 bg-red-700 hover:bg-red-800 text-paper font-semibold text-xs uppercase tracking-widest rounded-md transition ease-in-out duration-150">
                            Excluir
                        </button>
                    </div>
                </div>

                <dialog
                    id="confirm-task-deletion-{{ $task->id }}"
                    aria-labelledby="confirm-task-deletion-title-{{ $task->id }}"
                    class="fixed top-1/2 left-1/2 m-0 w-[calc(100%_-_2rem)] max-w-2xl -translate-x-1/2 -translate-y-1/2 p-0 bg-transparent backdrop:bg-ink/75">
                    <div class="bg-paper border-t-4 border-ember rounded-lg overflow-hidden shadow-xl">
                        <div class="p-6">
                            <h2 id="confirm-task-deletion-title-{{ $task->id }}" class="text-lg font-semibold text-ink">
                                Excluir tarefa?
                            </h2>

                            <p class="mt-2 text-sm text-ink/60">
                                Você tem certeza de que deseja excluir
                                <strong class="text-ink">“{{ $task->title }}”</strong>?
                                Essa ação não poderá ser desfeita.
                            </p>

                            <div class="mt-6 flex justify-end gap-3">
                                <x-secondary-button
                                    type="button"
                                    onclick="this.closest('dialog').close()">
                                    Cancelar
                                </x-secondary-button>

                                <form method="POST" action="{{ route('tasks.destroy', $task->id) }}">
                                    @csrf
                                    @method('DELETE')

                                    <x-danger-button type="submit">
                                        Sim, excluir
                                    </x-danger-button>
                                </form>
                            </div>
                        </div>
                    </div>
                </dialog>
            @endforeach
        </div>
    </div>
</x-app-layout>
