<?php

//A controller é responsável por receber as requisições do usuário, processar os dados e retornar uma resposta.
//No caso, o controller seria para gerenciar as tarefas, ou seja, criar, editar, excluir e listar as tarefas do usuário.
//O controller também pode ter métodos para filtrar as tarefas por status, ou para adicionar lembretes às tarefas.

namespace App\Http\Controllers;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskStatusRequest;
use App\Http\Requests\UpdateTaskRequest;

class TaskController extends Controller
{   
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

        if($task)
        {
            return redirect()->route('tasks.index')->with('success', 'Tarefa atualizada com sucesso!');
        } else {
            return redirect()->route('tasks.index')->with('error', 'Ocorreu um erro ao atualizar a tarefa.');
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

        return back()->with('success', 'Status da tarefa atualizado com sucesso!');
    }

    public function edit(Task $task){
        
    Gate::authorize('edit', $task);

        
        // 🎯 ALTERADO: o lembrete atual é enviado ao formulário para que possa
        // ser visualizado, substituído ou removido durante a edição.
        $reminder = $task->reminders()
            ->orderByDesc('reminder_datetime')
            ->first();

        return view('tasks.edit', compact('task', 'reminder'));
    }

    public function destroy(Task $task)
    {

        Gate::authorize('delete', $task);

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

    public function show(Task $task)
    {
        Gate::authorize('view', $task);

        return view('tasks.index', compact('task'));
    }
}
