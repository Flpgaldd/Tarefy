<?php

namespace App\Services;

use App\Models\Task;
use App\Notifications\TaskDueNotification;

class TaskDueNotificationService
{
    /**
     * 🎯 NOVO: cria uma única notificação para cada tarefa que venceu enquanto
     * ainda estava Pendente. O scheduler chama este método a cada minuto.
     */
    public function notifyOverduePendingTasks(): int
    {
        $createdNotifications = 0;

        Task::query()
            ->with('user')
            ->where('status', 'Pendente')
            ->where('due_datetime', '<=', now())
            ->whereHas('user')
            ->eachById(function (Task $task) use (&$createdNotifications): void {
                $alreadyExists = $task->user
                    ->notifications()
                    ->where('type', TaskDueNotification::class)
                    ->where('data->task_id', $task->id)
                    ->exists();

                if ($alreadyExists) {
                    return;
                }

                $task->user->notify(new TaskDueNotification(
                    taskId: $task->id,
                    taskTitle: $task->title,
                    dueAt: $task->due_datetime?->toIso8601String(),
                ));

                $createdNotifications++;
            });

        return $createdNotifications;
    }

    /**
     * 🎯 NOVO: remove somente o aviso automático de vencimento. O lembrete
     * configurado pelo usuário permanece no histórico de notificações.
     */
    public function removeDueNotificationForTask(Task $task): void
    {
        $task->user
            ?->notifications()
            ->where('type', TaskDueNotification::class)
            ->where('data->task_id', $task->id)
            ->delete();
    }

    /**
     * 🎯 NOVO: ao excluir uma tarefa, remove todos os avisos ligados a ela para
     * não deixar notificações apontando para uma página que não existe mais.
     */
    public function removeAllNotificationsForTask(Task $task): void
    {
        $task->user
            ?->notifications()
            ->where('data->task_id', $task->id)
            ->delete();
    }
}
