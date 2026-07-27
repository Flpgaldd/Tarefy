<?php

use App\Jobs\SendReminderTask;
use App\Models\TaskReminder;
use App\Services\TaskDueNotificationService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function (): void {
    TaskReminder::query()
        ->with('task.user')
        ->where('reminder_datetime', '<=', now())
        ->each(function (TaskReminder $reminder): void {
            if ($reminder->task) {
                // 🎯 ALTERADO: o job recebe usuário, tarefa, título e vencimento
                // para gerar a notificação persistente exibida dentro do site.
                SendReminderTask::dispatch(
                    userId: $reminder->task->user_id,
                    taskId: $reminder->task->id,
                    taskTitle: $reminder->task->title,
                    dueAt: $reminder->task->due_datetime?->toIso8601String(),
                );
            }

            $reminder->delete();
        });
})
    ->name('dispatch-task-reminders')
    ->withoutOverlapping()
    ->everyMinute();

// 🎯 NOVO: a cada minuto procura tarefas vencidas que continuam Pendentes.
// Cada tarefa recebe no máximo um aviso, controlado pelo serviço compartilhado.
Schedule::call(function (): void {
    app(TaskDueNotificationService::class)->notifyOverduePendingTasks();
})
    ->name('dispatch-due-task-notifications')
    ->withoutOverlapping()
    ->everyMinute();
