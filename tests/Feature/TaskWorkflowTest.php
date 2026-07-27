<?php

namespace Tests\Feature;

use App\Jobs\SendReminderTask;
use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskDueNotification;
use App\Services\TaskDueNotificationService;
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
     * 🎯 NOVO: garante que a página exclusiva apresente os detalhes e todos os
     * campos editáveis, sem permitir que outro usuário veja a mesma tarefa.
     */
    public function test_task_details_page_is_editable_only_by_its_owner(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $task = $owner->tasks()->create([
            'title' => 'Planejar entrega do projeto',
            'description' => 'Reunir todas as informações da entrega.',
            'due_datetime' => now()->addDays(2),
            'status' => 'Pendente',
            'priority' => Task::PRIORITY_HIGH,
        ]);

        $this
            ->actingAs($owner)
            ->get(route('tasks.show', $task))
            ->assertOk()
            ->assertSee('Detalhes da Tarefa')
            ->assertSee('Planejar entrega do projeto')
            ->assertSee('Reunir todas as informações da entrega.')
            ->assertSee('Tempo restante')
            ->assertSee('name="title"', false)
            ->assertSee('name="status"', false)
            ->assertSee('name="priority"', false)
            ->assertSee('name="due_datetime"', false)
            ->assertSee('name="reminder_datetime"', false);

        $this
            ->actingAs($otherUser)
            ->get(route('tasks.show', $task))
            ->assertForbidden();
    }

    /**
     * 🎯 NOVO: cobre a edição centralizada na página de detalhes, incluindo
     * nome, descrição, status, prioridade, vencimento e lembrete.
     */
    public function test_all_task_details_can_be_updated_from_the_details_page(): void
    {
        $user = User::factory()->create();
        $task = $user->tasks()->create([
            'title' => 'Nome antigo',
            'due_datetime' => now()->addDays(2),
            'status' => 'Pendente',
            'priority' => Task::PRIORITY_LOW,
        ]);
        $newDueDate = now()->addDays(4)->startOfMinute();
        $newReminderDate = now()->addDay()->startOfMinute();

        $response = $this
            ->actingAs($user)
            ->put(route('tasks.update', $task), [
                'title' => 'Nome atualizado',
                'description' => 'Descrição atualizada pela página de detalhes.',
                'due_datetime' => $newDueDate->format('Y-m-d\TH:i'),
                'reminder_datetime' => $newReminderDate->format('Y-m-d\TH:i'),
                'status' => 'Fazendo',
                'priority' => Task::PRIORITY_URGENT,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('tasks.show', $task));

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Nome atualizado',
            'description' => 'Descrição atualizada pela página de detalhes.',
            'status' => 'Fazendo',
            'priority' => Task::PRIORITY_URGENT,
            'due_datetime' => $newDueDate->format('Y-m-d H:i:s'),
        ]);

        $this->assertDatabaseHas('task_reminders', [
            'task_id' => $task->id,
            'reminder_datetime' => $newReminderDate->format('Y-m-d H:i:s'),
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
     * 🎯 NOVO: garante que os nomes das tarefas abram a visualização rápida nas
     * listas, enquanto somente "Ver detalhes" em Minhas Tarefas mantém o link
     * para a página completa que contém a edição.
     */
    public function test_task_names_open_read_only_preview_and_only_details_link_opens_full_page(): void
    {
        $user = User::factory()->create();
        $task = $user->tasks()->create([
            'title' => 'Conferir relatório financeiro',
            'description' => 'Validar valores e anexos antes do envio.',
            'due_datetime' => now()->addDay()->setTime(14, 30),
            'status' => 'Concluída',
            'priority' => Task::PRIORITY_HIGH,
        ]);
        $detailsHref = 'href="'.route('tasks.show', $task).'"';

        $this
            ->actingAs($user)
            ->get(route('tasks.index'))
            ->assertOk()
            ->assertSee('open-task-preview')
            ->assertSee('data-task-preview')
            ->assertSee('JSON.parse($el.dataset.taskPreview)', false)
            ->assertSee('Esta aba é somente para consulta.')
            ->assertSee('Ver detalhes')
            ->assertSee($detailsHref, false);

        // 🎯 NOVO: dashboard, perfil e agenda diária exibem a mesma consulta
        // lateral, mas não oferecem um atalho direto para a página de edição.
        foreach ([
            route('dashboard'),
            route('profile.perfil'),
            route('tasks.by-date', ['date' => $task->due_datetime->format('Y-m-d')]),
        ] as $listUrl) {
            $this
                ->actingAs($user)
                ->get($listUrl)
                ->assertOk()
                ->assertSee('open-task-preview')
                ->assertSee('Conferir relatório financeiro')
                ->assertDontSee($detailsHref, false);
        }
    }

    /**
     * 🎯 NOVO: o calendário do perfil aponta para uma agenda diária que mostra
     * apenas tarefas do usuário e da data escolhida, em ordem de horário.
     */
    public function test_profile_calendar_opens_tasks_organized_by_selected_date(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $selectedDate = now()->addDays(3)->startOfDay();

        $afternoonTask = $user->tasks()->create([
            'title' => 'Reunião da tarde',
            'description' => 'Revisar o planejamento.',
            'due_datetime' => $selectedDate->copy()->setTime(15, 30),
            'status' => 'Fazendo',
            'priority' => Task::PRIORITY_HIGH,
        ]);

        $morningTask = $user->tasks()->create([
            'title' => 'Revisão da manhã',
            'due_datetime' => $selectedDate->copy()->setTime(9, 0),
            'status' => 'Pendente',
            'priority' => Task::PRIORITY_LOW,
        ]);

        $user->tasks()->create([
            'title' => 'Tarefa de outro dia',
            'due_datetime' => $selectedDate->copy()->addDay()->setTime(10, 0),
            'status' => 'Pendente',
            'priority' => Task::PRIORITY_MEDIUM,
        ]);

        $otherUser->tasks()->create([
            'title' => 'Tarefa de outro usuário',
            'due_datetime' => $selectedDate->copy()->setTime(8, 0),
            'status' => 'Pendente',
            'priority' => Task::PRIORITY_URGENT,
        ]);

        $dateRoute = route('tasks.by-date', [
            'date' => $selectedDate->format('Y-m-d'),
        ]);

        $this
            ->actingAs($user)
            ->get(route('profile.perfil'))
            ->assertOk()
            ->assertSee($dateRoute);

        $this
            ->actingAs($user)
            ->get($dateRoute)
            ->assertOk()
            ->assertSee('Tarefas do dia')
            ->assertSeeInOrder([
                $morningTask->title,
                $afternoonTask->title,
            ])
            ->assertSee('09:00')
            ->assertSee('15:30')
            ->assertDontSee('Tarefa de outro dia')
            ->assertDontSee('Tarefa de outro usuário')
            // 🎯 ALTERADO: a agenda diária não leva mais diretamente à edição;
            // os nomes agora abrem o painel lateral compartilhado.
            ->assertSee('open-task-preview')
            ->assertDontSee('href="'.route('tasks.show', $morningTask).'"', false)
            ->assertDontSee('href="'.route('tasks.show', $afternoonTask).'"', false);
    }

    /**
     * 🎯 NOVO: datas inexistentes não são normalizadas silenciosamente para
     * outro dia e retornam 404.
     */
    public function test_daily_task_page_rejects_an_invalid_date(): void
    {
        $user = User::factory()->create();

        $this
            ->actingAs($user)
            ->get('/tasks/date/2026-02-31')
            ->assertNotFound();
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
            ->assertJsonPath('notifications.0.task_title', 'Enviar relatório mensal')
            ->assertJsonPath('notifications.0.kind', 'reminder')
            ->assertJsonPath('notifications.0.url', route('tasks.show', $task));

        $this
            ->actingAs($user)
            ->patchJson(route('notifications.read', $notification->id))
            ->assertOk()
            ->assertJsonPath('unread_count', 0);

        $this->assertNotNull($notification->fresh()->read_at);
    }

    /**
     * 🎯 NOVO: uma tarefa vencida e Pendente recebe apenas um aviso adicional.
     * Ao mudar para Fazendo, esse aviso some sem apagar lembretes comuns.
     */
    public function test_due_notification_is_unique_and_disappears_when_task_starts(): void
    {
        $user = User::factory()->create();
        $task = $user->tasks()->create([
            'title' => 'Atividade que venceu',
            'due_datetime' => now()->subMinute(),
            'status' => 'Pendente',
            'priority' => Task::PRIORITY_HIGH,
        ]);
        $service = app(TaskDueNotificationService::class);

        $this->assertSame(1, $service->notifyOverduePendingTasks());
        $this->assertSame(0, $service->notifyOverduePendingTasks());

        $notification = $user->notifications()
            ->where('type', TaskDueNotification::class)
            ->first();

        $this->assertNotNull($notification);
        $this->assertSame('due', $notification->data['kind']);
        $this->assertSame(route('tasks.show', $task), $notification->data['url']);

        // 🎯 ALTERADO: usa o formulário completo da página de detalhes e mantém
        // o prazo vencido original, comprovando que só mudar para Fazendo é válido.
        $this
            ->actingAs($user)
            ->put(route('tasks.update', $task), [
                'title' => $task->title,
                'description' => $task->description,
                'due_datetime' => $task->due_datetime->format('Y-m-d\TH:i'),
                'status' => 'Fazendo',
                'priority' => $task->priority,
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('tasks.show', $task));

        $this->assertDatabaseMissing('notifications', [
            'id' => $notification->id,
        ]);
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
