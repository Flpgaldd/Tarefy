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
        ->with('task')
        ->where('reminder_datetime', '<=', now())
        ->each(function (TaskReminder $reminder): void {
            if ($reminder->task) {
                SendReminderTask::dispatch($reminder->task->title);
            }

            $reminder->delete();
        });
})
    ->name('dispatch-task-reminders')
    ->withoutOverlapping()
    ->everyMinute();
