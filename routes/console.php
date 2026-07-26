<?php

use App\Jobs\SendReminderTask;
use App\Models\TaskReminder;
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
