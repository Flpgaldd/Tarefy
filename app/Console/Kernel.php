<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\TaskReminder;
use App\Jobs\SendReminderTask;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->call(function ()     {

    $reminders = TaskReminder::where('reminder_datetime', '<=', now())->get();

    foreach($reminders as $reminder){
        // 🎯 ALTERADO: assinatura atualizada para gerar a mesma notificação
        // persistente usada pelo scheduler principal de routes/console.php.
        if ($reminder->task) {
            SendReminderTask::dispatch(
                userId: $reminder->task->user_id,
                taskId: $reminder->task->id,
                taskTitle: $reminder->task->title,
                dueAt: $reminder->task->due_datetime?->toIso8601String(),
            );
        }

        $reminder->delete();
        }


    })->everyMinute();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
        }

}
