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
            'due_datetime' => $request->due_datetime
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
        ]);

        if($task)
        {
            return redirect()->route('tasks.index')->with('success', 'Tarefa atualizada com sucesso!');
        } else {
            return redirect()->route('tasks.index')->with('error', 'Ocorreu um erro ao atualizar a tarefa.');
        }
    }
    public function edit(Task $task){
        
    Gate::authorize('edit', $task);

        
        return view('tasks.edit', compact('task'));
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
