<?php

//A controller é responsável por receber as requisições do usuário, processar os dados e retornar uma resposta.
//No caso, o controller seria para gerenciar as tarefas, ou seja, criar, editar, excluir e listar as tarefas do usuário.
//O controller também pode ter métodos para filtrar as tarefas por status, ou para adicionar lembretes às tarefas.

namespace App\Http\Controllers;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskStatusRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Services\TaskDueNotificationService;

class TaskController extends Controller
{
    public function __construct(
        private readonly TaskDueNotificationService $dueNotifications,
    ) {}

    public function index()
    {   
        $user = Auth::user();

        $tasks = $user->tasks()->orderBy('due_datetime', 'asc')->get();
        


         // 🔒 ALTERADO: antes chamava $this->getTaskStats($user) (método privado
         // duplicado aqui dentro). Agora usa $user->taskStats(), a mesma fonte
         // usada pelo dashboard — garante que os números batem nas duas telas.
         $stats = $user->taskStats();
        
         return view('tasks.index', array_merge(
            ['tasks' => $tasks],    
            $stats
         ));
    }

    public function store(StoreTaskRequest $request)
    {
        $user = Auth::user();
       $task = $user->tasks()->create([
            'title' => $request->title,
            'due_datetime' => $request->due_datetime,
            // 🎯 ALTERADO: status e prioridade validados agora são gravados
            // explicitamente junto com os demais dados da nova tarefa.
            'status' => $request->status,
            'priority' => $request->priority,
            ]);

        if ($request->filled('reminder_datetime')) {
            $task->reminders()->create([
                'reminder_datetime' => $request->reminder_datetime,
            ]);
        }

            if($task)
            return redirect()->route('tasks.index')->with('msg', 'Tarefa criada com sucesso!');
        else        
            return redirect()->route('tasks.index')->with('error', 'Ocorreu um erro ao criar a tarefa.');
    
        }

    public function update(UpdateTaskRequest $request, Task $task)
    {
        Gate::authorize('update', $task);
        $task->update([
            'title' => $request->title,
            // 🎯 ALTERADO: a descrição agora pode ser atualizada diretamente
            // pela página completa de detalhes da tarefa.
            'description' => $request->description,
            'due_datetime' => $request->due_datetime,
            'status' => $request->status,
            // 🎯 ALTERADO: a edição passa a persistir a prioridade selecionada.
            'priority' => $request->priority,
        ]);

        // 🎯 ALTERADO: a edição mantém no máximo um lembrete futuro por tarefa.
        // O agendamento antigo é substituído pelo novo; deixar o campo vazio
        // remove um lembrete que ainda não foi disparado.
        $task->reminders()->delete();

        if ($request->filled('reminder_datetime')) {
            $task->reminders()->create([
                'reminder_datetime' => $request->reminder_datetime,
            ]);
        }

        // 🎯 ALTERADO: o aviso é removido quando o trabalho começou/terminou ou
        // quando o vencimento foi reagendado para o futuro. Editar apenas o nome
        // de uma tarefa ainda Pendente e vencida mantém o aviso corretamente.
        if ($task->status !== 'Pendente' || $task->due_datetime?->isFuture()) {
            $this->dueNotifications->removeDueNotificationForTask($task);
        }

        if($task)
        {
            return redirect()->route('tasks.show', $task)->with('success', 'Tarefa atualizada com sucesso!');
        } else {
            return redirect()->route('tasks.show', $task)->with('error', 'Ocorreu um erro ao atualizar a tarefa.');
        }
    }

    /**
     * 🎯 NOVO: atualiza somente o status pelo seletor da listagem, sem exigir
     * título, vencimento e prioridade novamente. A policy impede alterações
     * em tarefas pertencentes a outro usuário.
     */
    public function updateStatus(UpdateTaskStatusRequest $request, Task $task)
    {
        Gate::authorize('update', $task);

        $task->update([
            'status' => $request->status,
        ]);

        // 🎯 ALTERADO: ao começar ou concluir o trabalho, o aviso automático de
        // vencimento desaparece; lembretes normais continuam no histórico.
        if (in_array($task->status, ['Fazendo', 'Concluída'], true)) {
            $this->dueNotifications->removeDueNotificationForTask($task);
        }

        return back()->with('success', 'Status da tarefa atualizado com sucesso!');
    }

    public function edit(Task $task){
        
    Gate::authorize('edit', $task);

        
        // 🎯 ALTERADO: a antiga tela separada de edição foi incorporada à nova
        // página de detalhes, mantendo links antigos funcionando por redireção.
        return redirect()->route('tasks.show', $task);
    }

    public function destroy(Task $task)
    {

        Gate::authorize('delete', $task);

        // 🎯 ALTERADO: elimina notificações órfãs antes de apagar a tarefa.
        $this->dueNotifications->removeAllNotificationsForTask($task);

        $deleted = $task->delete($task);

        if($deleted)
            return redirect()->route('tasks.index')->with('msg', 'Tarefa excluída com sucesso!');
        else
            return redirect()->route('tasks.index')->with('error', 'Ocorreu um erro ao excluir a tarefa.');
    }

    public function search(Request $request)
{
    $status = $request->query('status');
    $priority = $request->query('priority');
    $title  = $request->query('title');
    $user   = Auth::user();

    $query = $user->tasks();

    // Filtro por Título
    if ($title) {
        $query->where('title', 'LIKE', "%{$title}%");
    }

    // Filtro por Status
    if ($status && $status !== 'all') {
        // Se preferir usar o valor exato enviado pelo formulário:
        $query->where('status', $status);
    }

    // 🎯 ALTERADO: o filtro de "Minhas Tarefas" também aceita prioridade,
    // usando somente os quatro valores definidos no model.
    if ($priority && array_key_exists($priority, Task::PRIORITY_OPTIONS)) {
        $query->where('priority', $priority);
    }

    $tasks = $query->orderBy('due_datetime', 'asc')->get();

    $stats = $user->taskStats();

    // 🔒 ALTERADO: mesma troca — usa $user->taskStats() em vez do método
        // privado duplicado.

    return view('tasks.index', array_merge(
        ['tasks' => $tasks],
        $stats
    ));
}

    /**
     * 🎯 NOVO: exibe somente as tarefas do usuário autenticado que vencem no
     * dia selecionado no calendário, ordenadas do primeiro ao último horário.
     */
    public function byDate(string $date)
    {
        $dateValidator = Validator::make(
            ['date' => $date],
            ['date' => ['required', 'date_format:Y-m-d']],
        );

        abort_if($dateValidator->fails(), 404);

        $selectedDate = Carbon::createFromFormat(
            'Y-m-d',
            $date,
            config('app.timezone'),
        )->startOfDay();

        $tasks = Auth::user()
            ->tasks()
            ->whereDate('due_datetime', $selectedDate->format('Y-m-d'))
            ->orderBy('due_datetime')
            ->get();

        // 🎯 NOVO: pequenos totais por status ajudam o usuário a entender o dia
        // rapidamente antes de percorrer a lista cronológica.
        $dayStats = [
            'total' => $tasks->count(),
            'pending' => $tasks->where('status', 'Pendente')->count(),
            'doing' => $tasks->where('status', 'Fazendo')->count(),
            'completed' => $tasks->where('status', 'Concluída')->count(),
        ];

        return view('tasks.by-date', [
            'tasks' => $tasks,
            'selectedDate' => $selectedDate,
            'previousDate' => $selectedDate->copy()->subDay(),
            'nextDate' => $selectedDate->copy()->addDay(),
            'dayStats' => $dayStats,
        ]);
    }

    public function show(Task $task)
    {
        Gate::authorize('view', $task);

        // 🎯 ALTERADO: a rota agora entrega sua própria página com formulário
        // completo, lembrete atual e contagem regressiva do vencimento.
        $reminder = $task->reminders()
            ->orderByDesc('reminder_datetime')
            ->first();

        return view('tasks.show', compact('task', 'reminder'));
    }
}
