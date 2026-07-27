<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// 🎯 ALTERADO: antes era só `return view('dashboard');`, sem nenhum dado.
// Agora busca as estatísticas reais do usuário logado através de
// User::taskStats() — a mesma fonte usada em "Minhas Tarefas", garantindo que
// os números do dashboard sempre coincidam com os da outra tela.
Route::get('/dashboard', function (Request $request) {
    $user = Auth::user();
    $stats = $user->taskStats();

    // 🎯 ALTERADO: o status recebido pelo filtro é limitado aos três valores
    // válidos. A consulta continua partindo do relacionamento do usuário para
    // nunca mostrar tarefas de outra conta.
    $selectedStatus = $request->query('status', 'all');
    $selectedPriority = $request->query('priority', 'all');
    $allowedStatuses = ['Pendente', 'Fazendo', 'Concluída'];
    $allowedPriorities = array_keys(\App\Models\Task::PRIORITY_OPTIONS);

    $tasksQuery = $user->tasks()->latest();

    if (in_array($selectedStatus, $allowedStatuses, true)) {
        $tasksQuery->where('status', $selectedStatus);
    } else {
        $selectedStatus = 'all';
    }

    // 🎯 ALTERADO: o filtro do dashboard combina status e prioridade. Valores
    // desconhecidos são ignorados para manter a consulta previsível e segura.
    if (in_array($selectedPriority, $allowedPriorities, true)) {
        $tasksQuery->where('priority', $selectedPriority);
    } else {
        $selectedPriority = 'all';
    }

    $tasks = $tasksQuery->get();

    return view('dashboard', [
        ...$stats,
        'tasks' => $tasks,
        'selectedStatus' => $selectedStatus,
        'selectedPriority' => $selectedPriority,
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // 🎯 NOVO: endpoints consumidos pelo sino global de notificações.
    Route::get('/notifications', [NotificationController::class, 'index'])
        ->name('notifications.index');
    Route::patch('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])
        ->name('notifications.read-all');
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])
        ->name('notifications.read');

    Route::get('/perfil', [ProfileController::class, 'index'])->name('profile.perfil');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::prefix('tasks')->middleware('auth')->group(function () {
    Route::get('/search', [TaskController::class, 'search'])->name('tasks.search');
    Route::get('/', [TaskController::class, 'index'])->name('tasks.index');
    Route::post('/', [TaskController::class, 'store'])->name('tasks.store');
    // 🎯 NOVO: página organizada com todas as tarefas do dia escolhido no
    // calendário do perfil. A restrição evita datas em formatos inesperados.
    Route::get('/date/{date}', [TaskController::class, 'byDate'])
        ->where('date', '\d{4}-\d{2}-\d{2}')
        ->name('tasks.by-date');
    // 🎯 NOVO: endpoint dedicado ao seletor rápido de status da listagem.
    Route::patch('/{task}/status', [TaskController::class, 'updateStatus'])
        ->name('tasks.status.update');
    Route::get('/{task}', [TaskController::class, 'show'])->name('tasks.show');
    Route::put('/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::get('/{task}/edit', [TaskController::class, 'edit'])->name('tasks.edit');
    Route::delete('/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
});

require __DIR__.'/auth.php';    
