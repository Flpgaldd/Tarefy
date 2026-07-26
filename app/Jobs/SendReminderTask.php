<?php

// 🎯 ALTERADO: o job em background agora cria o lembrete dentro do próprio
// site, em vez de apenas registrar uma mensagem no log ou depender de e-mail.

namespace App\Jobs;

use App\Models\User;
use App\Notifications\TaskReminderNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Bus\Dispatchable;

class SendReminderTask implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    // 🎯 ALTERADO: o job recebe dados suficientes para localizar o usuário e
    // criar a notificação mesmo depois que o agendamento original for removido.
    public function __construct(
        public int $userId,
        public int $taskId,
        public string $taskTitle,
        public ?string $dueAt,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // 🎯 ALTERADO: o antigo registro no log foi substituído por uma
        // notificação persistente, visível no sino e no toast global.
        $user = User::query()->find($this->userId);

        if (! $user) {
            return;
        }

        $user->notify(new TaskReminderNotification(
            taskId: $this->taskId,
            taskTitle: $this->taskTitle,
            dueAt: $this->dueAt,
        ));
    }
}
