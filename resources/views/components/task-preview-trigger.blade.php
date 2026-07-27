@props(['task'])

@php
    // 🎯 NOVO: os dados necessários para a visualização rápida são preparados
    // no servidor. O painel recebe somente textos de leitura e nenhuma rota ou
    // informação que permita alterar a tarefa.
    $taskPreview = [
        'id' => $task->id,
        'title' => $task->title,
        'description' => $task->description,
        'status' => $task->status,
        'priority' => $task->priority,
        'priorityLabel' => \App\Models\Task::PRIORITY_OPTIONS[$task->priority]
            ?? \App\Models\Task::PRIORITY_OPTIONS[\App\Models\Task::PRIORITY_MEDIUM],
        'dueAt' => $task->due_datetime?->format('d/m/Y \à\s H:i'),
        'createdAt' => $task->created_at?->format('d/m/Y \à\s H:i'),
        'updatedAt' => $task->updated_at?->format('d/m/Y \à\s H:i'),
    ];
@endphp

{{-- 🎯 NOVO: este botão substitui os links existentes nos nomes das tarefas.
     O evento abre o painel lateral sem navegar para a página de edição. --}}
<button
    type="button"
    x-data
    {{-- 🎯 ALTERADO: o JSON fica em um atributo escapado pelo Blade. Isso evita
         que aspas no título ou na descrição quebrem o comando de clique. --}}
    data-task-preview="{{ json_encode($taskPreview, JSON_UNESCAPED_UNICODE) }}"
    x-on:click="$dispatch('open-task-preview', JSON.parse($el.dataset.taskPreview))"
    aria-label="Visualizar rapidamente a tarefa {{ $task->title }}"
    {{ $attributes }}>
    {{ $slot }}
</button>
