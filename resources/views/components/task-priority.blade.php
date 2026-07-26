@props(['priority'])

{{-- 🎯 ALTERADO: componente único para exibir as prioridades somente pelo nome,
     a mesma cor em todas as tabelas e listas. Valores antigos ou inesperados
     recebem o tratamento visual da prioridade Média como fallback seguro. --}}
@php
    $priorityDetails = match ($priority) {
        \App\Models\Task::PRIORITY_LOW => [
            'label' => \App\Models\Task::PRIORITY_OPTIONS[\App\Models\Task::PRIORITY_LOW],
            'class' => 'text-green-600',
        ],
        \App\Models\Task::PRIORITY_HIGH => [
            'label' => \App\Models\Task::PRIORITY_OPTIONS[\App\Models\Task::PRIORITY_HIGH],
            'class' => 'text-orange-600',
        ],
        \App\Models\Task::PRIORITY_URGENT => [
            'label' => \App\Models\Task::PRIORITY_OPTIONS[\App\Models\Task::PRIORITY_URGENT],
            'class' => 'text-red-700',
        ],
        default => [
            'label' => \App\Models\Task::PRIORITY_OPTIONS[\App\Models\Task::PRIORITY_MEDIUM],
            'class' => 'text-yellow-600',
        ],
    };
@endphp

<span {{ $attributes->class(['font-semibold', $priorityDetails['class']]) }}>
    {{ $priorityDetails['label'] }}
</span>
