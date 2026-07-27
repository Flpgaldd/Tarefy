<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-paper leading-tight">
                {{ __('Detalhes da Tarefa') }}
            </h2>

            <a
                href="{{ route('tasks.index') }}"
                class="text-xs font-semibold uppercase tracking-widest text-paper/60 hover:text-ember transition">
                Voltar para tarefas
            </a>
        </div>
    </x-slot>

    {{-- 🎯 NOVO: esta página reúne visualização, contagem regressiva e edição
         completa da tarefa. Assim, todas as listas e notificações podem apontar
         para um único local, sem obrigar o usuário a procurar a tela de edição. --}}
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        @if(session('success') || session('msg') || session('error'))
            <div class="space-y-2">
                @if(session('success'))
                    <p class="px-4 py-3 rounded-lg bg-green-50 border border-green-600 text-green-700 text-sm font-medium">
                        {{ session('success') }}
                    </p>
                @endif
                @if(session('msg'))
                    <p class="px-4 py-3 rounded-lg bg-ember/10 border border-ember text-ember-dark text-sm font-medium">
                        {{ session('msg') }}
                    </p>
                @endif
                @if(session('error'))
                    <p class="px-4 py-3 rounded-lg bg-red-50 border border-red-700 text-red-700 text-sm font-medium">
                        {{ session('error') }}
                    </p>
                @endif
            </div>
        @endif

        @if ($errors->any())
            <div class="px-4 py-3 rounded-lg bg-red-50 border border-red-700">
                <p class="text-sm font-bold text-red-700">Confira os campos destacados:</p>
                <ul class="mt-2 list-disc list-inside text-sm text-red-700 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="overflow-hidden rounded-xl bg-ink shadow-lg">
            <div class="grid lg:grid-cols-[1.35fr_1fr]">
                <div class="p-6 sm:p-8 border-b lg:border-b-0 lg:border-e border-paper/10">
                    <div class="flex flex-wrap items-center gap-3">
                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold uppercase tracking-widest
                            {{ $task->status === 'Fazendo' ? 'bg-orange-500/20 text-orange-300' : '' }}
                            {{ $task->status === 'Concluída' ? 'bg-green-500/20 text-green-300' : '' }}
                            {{ $task->status === 'Pendente' ? 'bg-paper/10 text-paper/70' : '' }}">
                            {{ $task->status }}
                        </span>
                        <x-task-priority :priority="$task->priority" class="text-sm" />
                    </div>

                    <h1 class="mt-5 text-2xl sm:text-4xl font-bold text-paper break-words">
                        {{ $task->title }}
                    </h1>

                    <p class="mt-3 text-sm text-paper/55">
                        Vencimento em
                        <strong class="text-paper">
                            {{ $task->due_datetime?->format('d/m/Y \à\s H:i') }}
                        </strong>
                    </p>

                    @if($task->description)
                        <p class="mt-6 text-paper/70 leading-relaxed whitespace-pre-line">{{ $task->description }}</p>
                    @else
                        <p class="mt-6 text-paper/35 italic">Esta tarefa ainda não possui descrição.</p>
                    @endif
                </div>

                {{-- 🎯 NOVO: contagem regressiva feita no navegador, usando o
                     vencimento com o offset -03:00 enviado pelo Laravel. Ela
                     mostra somente dias, horas e minutos, sem poluir com segundos. --}}
                <div
                    x-data="taskCountdown(@js($task->due_datetime?->toIso8601String()))"
                    class="p-6 sm:p-8">
                    <p
                        class="text-xs font-semibold uppercase tracking-[0.2em]"
                        :class="expired ? 'text-red-400' : 'text-ember'"
                        x-text="expired ? 'Prazo encerrado há' : 'Tempo restante'">
                    </p>

                    <div class="mt-5 grid grid-cols-3 gap-3">
                        <div class="rounded-lg bg-paper/5 border border-paper/10 px-3 py-4 text-center">
                            <span class="block text-3xl font-bold text-paper" x-text="days"></span>
                            <span class="block mt-1 text-[10px] uppercase tracking-widest text-paper/45">Dias</span>
                        </div>
                        <div class="rounded-lg bg-paper/5 border border-paper/10 px-3 py-4 text-center">
                            <span class="block text-3xl font-bold text-paper" x-text="hours"></span>
                            <span class="block mt-1 text-[10px] uppercase tracking-widest text-paper/45">Horas</span>
                        </div>
                        <div class="rounded-lg bg-paper/5 border border-paper/10 px-3 py-4 text-center">
                            <span class="block text-3xl font-bold text-paper" x-text="minutes"></span>
                            <span class="block mt-1 text-[10px] uppercase tracking-widest text-paper/45">Minutos</span>
                        </div>
                    </div>

                    @if($task->status === 'Pendente')
                        {{-- 🎯 NOVO: explica exatamente quando o aviso extra será
                             criado e como o usuário pode removê-lo do sino. --}}
                        <div class="mt-5 rounded-lg border border-ember/40 bg-ember/10 p-4">
                            <p class="text-sm font-semibold text-ember">Tarefa ainda pendente</p>
                            <p class="mt-1 text-xs leading-relaxed text-paper/60">
                                Se ela continuar Pendente no vencimento, um aviso será exibido.
                                Ao mudar para Fazendo, esse aviso desaparecerá.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </section>

        <section class="bg-paper border-l-4 border-ember rounded-xl shadow-sm p-6 sm:p-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-6">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-widest text-ember">Editar tarefa</p>
                    <h2 class="mt-1 text-xl font-bold text-ink">Informações e datas</h2>
                </div>
                <p class="text-xs text-ink/45">Última atualização: {{ $task->updated_at?->format('d/m/Y H:i') }}</p>
            </div>

            {{-- 🎯 NOVO: todos os campos editáveis ficam na própria página de
                 detalhes: nome, descrição, status, prioridade, prazo e lembrete. --}}
            <form
                method="POST"
                action="{{ route('tasks.update', $task) }}"
                x-data="{
                    status: @js(old('status', $task->status)),
                    priority: @js(old('priority', $task->priority ?? \App\Models\Task::PRIORITY_MEDIUM))
                }"
                class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label for="task-title" class="block text-sm font-medium text-ink mb-1">Nome da tarefa</label>
                    <input
                        id="task-title"
                        type="text"
                        name="title"
                        value="{{ old('title', $task->title) }}"
                        required
                        class="w-full border-ink/20 bg-paper text-ink focus:border-ember focus:ring-ember rounded-md shadow-sm">
                    <x-input-error :messages="$errors->get('title')" class="mt-2" />
                </div>

                <div>
                    <label for="task-description" class="block text-sm font-medium text-ink mb-1">Descrição</label>
                    <textarea
                        id="task-description"
                        name="description"
                        rows="5"
                        maxlength="5000"
                        placeholder="Adicione informações importantes sobre a tarefa..."
                        class="w-full border-ink/20 bg-paper text-ink focus:border-ember focus:ring-ember rounded-md shadow-sm">{{ old('description', $task->description) }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>

                <div class="grid sm:grid-cols-2 gap-5">
                    <div>
                        <label for="task-status" class="block text-sm font-medium text-ink mb-1">Status</label>
                        <select
                            id="task-status"
                            name="status"
                            x-model="status"
                            :class="{
                                'border-slate-300 bg-slate-50 text-slate-700': status === 'Pendente',
                                'border-orange-300 bg-orange-50 text-orange-700': status === 'Fazendo',
                                'border-green-300 bg-green-50 text-green-700': status === 'Concluída'
                            }"
                            class="w-full border-2 rounded-lg py-2.5 font-bold focus:ring-2 focus:ring-ember focus:border-ember">
                            <option value="Pendente">Pendente</option>
                            <option value="Fazendo">Fazendo</option>
                            <option value="Concluída">Concluída</option>
                        </select>
                        <x-input-error :messages="$errors->get('status')" class="mt-2" />
                    </div>

                    <div>
                        <label for="task-priority" class="block text-sm font-medium text-ink mb-1">Prioridade</label>
                        <select
                            id="task-priority"
                            name="priority"
                            x-model="priority"
                            :class="{
                                'text-green-600': priority === 'low',
                                'text-yellow-600': priority === 'medium',
                                'text-orange-600': priority === 'high',
                                'text-red-700': priority === 'urgent'
                            }"
                            class="w-full border-ink/20 bg-paper focus:ring-ember focus:border-ember rounded-lg py-2.5 shadow-sm font-bold">
                            @foreach(\App\Models\Task::PRIORITY_OPTIONS as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('priority')" class="mt-2" />
                    </div>
                </div>

                <div class="grid sm:grid-cols-2 gap-5">
                    <div>
                        <label for="task-due-datetime" class="block text-sm font-medium text-ink mb-1">
                            Data e horário de vencimento
                        </label>
                        <input
                            id="task-due-datetime"
                            type="datetime-local"
                            name="due_datetime"
                            value="{{ old('due_datetime', $task->due_datetime?->format('Y-m-d\TH:i')) }}"
                            {{-- 🎯 ALTERADO: para uma tarefa vencida, omitir o
                                 `min` permite manter o prazo original ao salvar
                                 somente o novo status; o backend bloqueia outra data passada. --}}
                            @if(! $task->due_datetime?->isPast())
                                min="{{ now()->format('Y-m-d\TH:i') }}"
                            @endif
                            max="{{ now()->addYear()->format('Y-m-d\TH:i') }}"
                            required
                            class="w-full border-ink/20 bg-paper text-ink focus:border-ember focus:ring-ember rounded-md shadow-sm">
                        <x-input-error :messages="$errors->get('due_datetime')" class="mt-2" />
                    </div>

                    <div>
                        <label for="task-reminder-datetime" class="block text-sm font-medium text-ink mb-1">
                            Data e horário do lembrete
                        </label>
                        <input
                            id="task-reminder-datetime"
                            type="datetime-local"
                            name="reminder_datetime"
                            value="{{ old('reminder_datetime', $reminder?->reminder_datetime?->format('Y-m-d\TH:i')) }}"
                            min="{{ now()->format('Y-m-d\TH:i') }}"
                            max="{{ old('due_datetime', $task->due_datetime?->format('Y-m-d\TH:i')) }}"
                            class="w-full border-ink/20 bg-paper text-ink focus:border-ember focus:ring-ember rounded-md shadow-sm">
                        <p class="mt-1 text-xs text-ink/45">Deixe vazio para remover o lembrete agendado.</p>
                        <x-input-error :messages="$errors->get('reminder_datetime')" class="mt-2" />
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-2">
                    <p class="text-xs text-ink/45">
                        Criada em {{ $task->created_at?->format('d/m/Y H:i') }} · Tarefa #{{ $task->id }}
                    </p>

                    <button
                        type="submit"
                        class="inline-flex justify-center items-center px-5 py-3 bg-ember hover:bg-ember-dark text-paper font-semibold text-xs uppercase tracking-widest rounded-md transition">
                        Salvar alterações
                    </button>
                </div>
            </form>
        </section>
    </div>
</x-app-layout>
