<?php

namespace Tests\Feature;

use App\Jobs\SendReminderTask;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskWorkflowTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 🎯 NOVO: garante que o formulário e a validação usem Brasília, sem somar
     * três horas ao menor horário permitido pelo campo `datetime-local`.
     */
    public function test_task_form_uses_brasilia_timezone_without_three_hour_offset(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 7, 26, 16, 30, 0, 'America/Sao_Paulo'));

        $user = User::factory()->create();

        $this->assertSame('America/Sao_Paulo', config('app.timezone'));

        $this
            ->actingAs($user)
            ->get(route('tasks.index'))
            ->assertOk()
            ->assertSee('min="2026-07-26T16:30"', false);

        $response = $this
            ->actingAs($user)
            ->post(route('tasks.store'), [
                'title' => 'Tarefa no horário de Brasília',
                'due_datetime' => '2026-07-26T16:31',
                'status' => 'Pendente',
                'priority' => Task::PRIORITY_MEDIUM,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('tasks.index'));

        $this->assertDatabaseHas('tasks', [
            'user_id' => $user->id,
            'title' => 'Tarefa no horário de Brasília',
            'due_datetime' => '2026-07-26 16:31:00',
        ]);
    }

    /**
     * 🎯 NOVO: garante que a prioridade escolhida na criação chega ao banco,
     * incluindo o novo nível Urgente.
     */
    public function test_task_priority_is_persisted_when_task_is_created(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('tasks.store'), [
                'title' => 'Resolver incidente crítico',
                'due_datetime' => now()->addDay()->format('Y-m-d\TH:i'),
                'status' => 'Pendente',
                'priority' => Task::PRIORITY_URGENT,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('tasks.index'));

        $this->assertDatabaseHas('tasks', [
            'user_id' => $user->id,
            'title' => 'Resolver incidente crítico',
            'priority' => Task::PRIORITY_URGENT,
        ]);
    }

    /**
     * 🎯 NOVO: cobre a rota usada pelo select rápido e confirma que somente o
     * status é alterado sem perder os demais dados da tarefa.
     */
    public function test_task_status_can_be_updated_from_the_quick_selector(): void
    {
        $user = User::factory()->create();
        $task = $user->tasks()->create([
            'title' => 'Preparar apresentação',
            'due_datetime' => now()->addDay(),
            'status' => 'Pendente',
            'priority' => Task::PRIORITY_HIGH,
        ]);

        $response = $this
            ->actingAs($user)
            ->patch(route('tasks.status.update', $task), [
                'status' => 'Fazendo',
            ]);

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'Fazendo',
            'priority' => Task::PRIORITY_HIGH,
        ]);
    }

    /**
     * 🎯 NOVO: valida que o filtro do dashboard mostra apenas o status
     * selecionado e não mistura as outras tarefas do mesmo usuário.
     */
    public function test_dashboard_tasks_can_be_filtered_by_status(): void
    {
        $user = User::factory()->create();

        $user->tasks()->create([
            'title' => 'Tarefa pendente do filtro',
            'due_datetime' => now()->addDay(),
            'status' => 'Pendente',
            'priority' => Task::PRIORITY_LOW,
        ]);

        $user->tasks()->create([
            'title' => 'Tarefa em andamento do filtro',
            'due_datetime' => now()->addDays(2),
            'status' => 'Fazendo',
            'priority' => Task::PRIORITY_MEDIUM,
        ]);

        $response = $this
            ->actingAs($user)
            ->get(route('dashboard', ['status' => 'Fazendo']));

        $response
            ->assertOk()
            ->assertSee('Tarefa em andamento do filtro')
            ->assertDontSee('Tarefa pendente do filtro');
    }

    /**
     * 🎯 NOVO: confirma que prioridade pode ser combinada com o filtro do
     * dashboard e que os rótulos aparecem sem número ou hífen.
     */
    public function test_dashboard_tasks_can_be_filtered_by_priority(): void
    {
        $user = User::factory()->create();

        $user->tasks()->create([
            'title' => 'Tarefa de prioridade baixa',
            'due_datetime' => now()->addDay(),
            'status' => 'Pendente',
            'priority' => Task::PRIORITY_LOW,
        ]);

        $user->tasks()->create([
            'title' => 'Tarefa de prioridade urgente',
            'due_datetime' => now()->addDays(2),
            'status' => 'Pendente',
            'priority' => Task::PRIORITY_URGENT,
        ]);

        $response = $this
            ->actingAs($user)
            ->get(route('dashboard', ['priority' => Task::PRIORITY_URGENT]));

        $response
            ->assertOk()
            ->assertSee('Tarefa de prioridade urgente')
            ->assertDontSee('Tarefa de prioridade baixa')
            ->assertSee('Urgente!')
            ->assertDontSee('4 - Urgente!');
    }

    /**
     * 🎯 NOVO: valida o fluxo completo do job até a tabela notifications e os
     * endpoints usados pelo sino para listar e marcar o lembrete como lido.
     */
    public function test_task_reminder_becomes_a_database_notification(): void
    {
        $user = User::factory()->create();
        $task = $user->tasks()->create([
            'title' => 'Enviar relatório mensal',
            'due_datetime' => now()->addDay(),
            'status' => 'Pendente',
            'priority' => Task::PRIORITY_HIGH,
        ]);

        (new SendReminderTask(
            userId: $user->id,
            taskId: $task->id,
            taskTitle: $task->title,
            dueAt: $task->due_datetime->toIso8601String(),
        ))->handle();

        $notification = $user->unreadNotifications()->first();

        $this->assertNotNull($notification);
        $this->assertSame('Enviar relatório mensal', $notification->data['task_title']);

        $this
            ->actingAs($user)
            ->getJson(route('notifications.index'))
            ->assertOk()
            ->assertJsonPath('unread_count', 1)
            ->assertJsonPath('notifications.0.task_title', 'Enviar relatório mensal');

        $this
            ->actingAs($user)
            ->patchJson(route('notifications.read', $notification->id))
            ->assertOk()
            ->assertJsonPath('unread_count', 0);

        $this->assertNotNull($notification->fresh()->read_at);
    }

    /**
     * 🎯 NOVO: garante que editar uma tarefa substitui o lembrete anterior e
     * mantém um único agendamento futuro associado à tarefa.
     */
    public function test_task_reminder_can_be_replaced_when_task_is_edited(): void
    {
        $user = User::factory()->create();
        $task = $user->tasks()->create([
            'title' => 'Revisar planejamento',
            'due_datetime' => now()->addDays(3),
            'status' => 'Pendente',
            'priority' => Task::PRIORITY_MEDIUM,
        ]);

        $task->reminders()->create([
            'reminder_datetime' => now()->addHours(2),
        ]);

        $newReminder = now()->addDay()->startOfMinute();

        $response = $this
            ->actingAs($user)
            ->put(route('tasks.update', $task), [
                'title' => $task->title,
                'due_datetime' => now()->addDays(3)->format('Y-m-d\TH:i'),
                'status' => 'Fazendo',
                'priority' => Task::PRIORITY_HIGH,
                'reminder_datetime' => $newReminder->format('Y-m-d\TH:i'),
            ]);

        $response->assertSessionHasNoErrors();

        $this->assertSame(1, $task->reminders()->count());
        $this->assertSame(
            $newReminder->format('Y-m-d H:i'),
            $task->reminders()->first()->reminder_datetime->format('Y-m-d H:i'),
        );
    }
}
