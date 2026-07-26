{{--
    🎨 ALTERADO (estrutural): este arquivo não usava <x-app-layout> (era só HTML
    solto, sem navbar/header), diferente de todas as outras páginas do sistema.
    Envolvi o conteúdo no mesmo layout usado no resto do app para manter a
    identidade visual consistente. A action do form, os campos (title,
    due_datetime, status), @csrf e @method('PUT') são exatamente os mesmos —
    só o wrapper visual foi adicionado.
--}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-paper leading-tight">
            {{ __('Editar Tarefa') }}
        </h2>
    </x-slot>

    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="bg-paper border-l-4 border-ember rounded-lg shadow-sm p-6">

            {{-- 🎨 ALTERADO: lista de erros de <ul> sem estilo para cartão vermelho
                 discreto (mesma convenção usada nas outras telas). --}}
            @if ($errors->any())
                <div class="mb-4 px-4 py-3 rounded-md bg-red-50 border border-red-700">
                    <ul class="text-sm text-red-700 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('tasks.update', $task->id) }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block font-medium text-sm text-ink mb-1">Nome da tarefa</label>
                    <input type="text" name="title" value="{{ $task->title }}"
                        class="w-full border-ink/20 bg-paper text-ink focus:border-ember focus:ring-ember rounded-md shadow-sm">
                </div>

                <div>
                    <label class="block font-medium text-sm text-ink mb-1">Data de vencimento</label>
                    {{--
                        🔒 ALTERADO (2 coisas):
                        1. min/max adicionados, mesma regra do form de criar tarefa.
                        2. value="{{ $task->due_datetime }}" foi trocado por
                           ->format('Y-m-d\TH:i'). $task->due_datetime é um Carbon
                           (tem $casts no model), então o value original saía como
                           "2026-07-22 10:30:00" — formato que <input type="datetime-local">
                           NÃO reconhece (precisa ser "2026-07-22T10:30"). Na prática,
                           o campo provavelmente aparecia vazio ao abrir a tela de edição.
                    --}}
                    <input type="datetime-local" name="due_datetime"
                        value="{{ $task->due_datetime?->format('Y-m-d\TH:i') }}"
                        min="{{ now()->format('Y-m-d\TH:i') }}"
                        max="{{ now()->addYear()->format('Y-m-d\TH:i') }}"
                        class="border-ink/20 bg-paper text-ink focus:border-ember focus:ring-ember rounded-md shadow-sm">
                </div>

                <div>
                    <label class="block font-medium text-sm text-ink mb-1">Status</label>
                    {{-- 🎨 ALTERADO: select estilizado; as 3 <option> e seus values
                         continuam idênticos ao original. --}}
                    <select name="status"
                        class="border-ink/20 bg-paper text-ink focus:ring-ember focus:border-ember rounded-md shadow-sm">
                        <option value="Pendente" {{ $task->status === 'Pendente' ? 'selected' : '' }}>Pendente</option>
                        <option value="Fazendo" {{ $task->status === 'Fazendo' ? 'selected' : '' }}>Fazendo</option>
                        <option value="Concluída" {{ $task->status === 'Concluída' ? 'selected' : '' }}>Concluída</option>
                    </select>
                </div>

                <button type="submit"
                    class="inline-flex items-center px-4 py-2 bg-ember border border-transparent rounded-md font-semibold text-xs text-paper uppercase tracking-widest hover:bg-ember-dark focus:outline-none focus:ring-2 focus:ring-ink focus:ring-offset-2 transition ease-in-out duration-150">
                    Atualizar
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
