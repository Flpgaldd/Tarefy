<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TaskDueNotification extends Notification
{
    use Queueable;

    public function __construct(
        public int $taskId,
        public string $taskTitle,
        public ?string $dueAt,
    ) {}

    /**
     * 🎯 NOVO: o aviso automático de vencimento fica salvo no banco para ser
     * exibido tanto no sino quanto no toast, independentemente da página aberta.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * 🎯 NOVO: `kind` diferencia este aviso do lembrete escolhido pelo usuário,
     * permitindo usar destaque vermelho e removê-lo sem apagar outros avisos.
     *
     * @return array<string, int|string|null>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'kind' => 'due',
            'task_id' => $this->taskId,
            'task_title' => $this->taskTitle,
            'title' => 'Tarefa vencida',
            'message' => "A tarefa “{$this->taskTitle}” venceu e ainda está Pendente. Altere o status para Fazendo para remover este aviso.",
            'due_at' => $this->dueAt,
            'url' => route('tasks.show', $this->taskId),
        ];
    }
}
