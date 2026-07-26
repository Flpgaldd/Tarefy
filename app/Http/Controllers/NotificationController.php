<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    /**
     * 🎯 NOVO: fornece as notificações mais recentes do usuário autenticado
     * para o sino do header e informa separadamente a quantidade não lida.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $notifications = $user->notifications()
            ->latest()
            ->limit(20)
            ->get()
            ->map(fn (DatabaseNotification $notification): array => [
                'id' => $notification->id,
                'title' => $notification->data['title'] ?? 'Lembrete de tarefa',
                'message' => $notification->data['message'] ?? '',
                'task_title' => $notification->data['task_title'] ?? null,
                'url' => $notification->data['url'] ?? route('tasks.index'),
                'read' => $notification->read_at !== null,
                'created_at' => $notification->created_at?->diffForHumans(),
            ]);

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $user->unreadNotifications()->count(),
        ]);
    }

    /**
     * 🎯 NOVO: marca uma notificação como lida somente quando ela pertence ao
     * usuário da sessão, impedindo acesso a notificações de outras contas.
     */
    public function markAsRead(Request $request, string $notification): JsonResponse
    {
        $item = $request->user()
            ->notifications()
            ->findOrFail($notification);

        $item->markAsRead();

        return response()->json([
            'success' => true,
            'unread_count' => $request->user()->unreadNotifications()->count(),
        ]);
    }

    /**
     * 🎯 NOVO: permite limpar o contador do sino marcando todas as notificações
     * do usuário autenticado como lidas em uma única ação.
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        $request->user()->unreadNotifications()->update([
            'read_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'unread_count' => 0,
        ]);
    }
}
