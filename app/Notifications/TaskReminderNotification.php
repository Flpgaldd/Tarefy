<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TaskReminderNotification extends Notification
{
    use Queueable;

    public function __construct(
        public int $taskId,
        public string $taskTitle,
        public ?string $dueAt,
    ) {}

    /**
     * 🎯 NOVO: o lembrete usa o canal database para permanecer disponível no
     * sino mesmo depois que o usuário fecha ou troca de página.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * 🎯 NOVO: estrutura persistida na tabela notifications e consumida pelo
     * toast global e pelo dropdown do sino.
     *
     * @return array<string, int|string|null>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'task_id' => $this->taskId,
            'task_title' => $this->taskTitle,
            'title' => 'Lembrete de tarefa',
            'message' => "Está na hora de lembrar da tarefa “{$this->taskTitle}”.",
            'due_at' => $this->dueAt,
            'url' => route('tasks.index'),
        ];
    }
}
