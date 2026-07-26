<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 🎯 NOVO: dados pro painel abaixo do perfil (dashboard + lista de
        // concluídas + calendário). taskStats() é a mesma fonte usada no
        // dashboard principal e em "Minhas Tarefas" — os números continuam
        // batendo em todo lugar.
        $completedTasks = $user->tasks()
            ->completed()
            ->orderBy('due_datetime', 'desc')
            ->get();

        // Agrupa as tarefas por dia (chave "AAAA-MM-DD"), pra marcar no
        // calendário quais dias têm alguma tarefa vencendo.
        $taskDates = $user->tasks()
            ->whereNotNull('due_datetime')
            ->get()
            ->groupBy(fn ($task) => $task->due_datetime->format('Y-m-d'))
            ->map(fn ($tasks) => $tasks->count());

        return view('profile.perfil', [
            'user' => $user,
            'stats' => $user->taskStats(),
            'completedTasks' => $completedTasks,
            'taskDates' => $taskDates,
        ]);
    }
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        // 'avatar' fica de fora do fill() — não é uma coluna real do usuário
        // (é o campo do arquivo enviado), quem grava o CAMINHO no banco é o
        // bloco abaixo, depois de mover o arquivo fisicamente.
        $request->user()->fill($request->safe()->except('avatar'));

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        // 🎯 ALTERADO: antes usava $file->store('avatars', 'public'), que salva
        // em storage/app/public e depende do symlink de "php artisan storage:link"
        // pra ficar acessível pelo navegador. Trocado por move() direto pra
        // dentro de public/avatars — o arquivo fica visível sem symlink nenhum,
        // é só um arquivo comum dentro da pasta pública do projeto.
        if ($request->hasFile('avatar')) {
            // Apaga a foto antiga antes de salvar a nova, pra não acumular
            // arquivo órfão no servidor a cada troca de foto.
            if ($request->user()->avatar_path) {
                $oldPath = public_path($request->user()->avatar_path);

                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }

            $file = $request->file('avatar');
            $filename = Str::uuid() . '.' . $file->extension();

            // move() cria a pasta public/avatars automaticamente se ela
            // ainda não existir — não precisa criar na mão antes.
            $file->move(public_path('avatars'), $filename);

            $request->user()->avatar_path = 'avatars/' . $filename;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // 🎯 ALTERADO: apaga o arquivo direto de public/avatars (não mais do
        // disco "storage"), consistente com a troca acima.
        if ($user->avatar_path) {
            $path = public_path($user->avatar_path);

            if (file_exists($path)) {
                @unlink($path);
            }
        }

        Auth::logout();

        $user->delete($user);

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
