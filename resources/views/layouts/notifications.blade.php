{{-- 🎯 NOVO: central global de notificações. O mesmo componente funciona no
     desktop e no mobile, mostra o contador no sino, lista os lembretes salvos
     e exibe o toast fixo no canto superior direito em qualquer página logada. --}}
<div
    x-data="notificationCenter({
        indexUrl: @js(route('notifications.index')),
        readUrlTemplate: @js(route('notifications.read', ['notification' => '__NOTIFICATION__'])),
        readAllUrl: @js(route('notifications.read-all'))
    })"
    x-init="start()"
    @keydown.escape.window="open = false"
    class="relative ms-auto flex items-center">

    <button
        type="button"
        @click="toggle()"
        class="relative inline-flex items-center justify-center w-10 h-10 rounded-full text-paper hover:text-ember hover:bg-ink-soft focus:outline-none focus:ring-2 focus:ring-ember transition"
        aria-label="Abrir notificações"
        :aria-expanded="open">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75V9a6 6 0 00-12 0v.75a8.967 8.967 0 01-2.312 6.022 23.848 23.848 0 005.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
        </svg>

        <span
            x-cloak
            x-show="unreadCount > 0"
            x-text="unreadCount > 99 ? '99+' : unreadCount"
            class="absolute -top-1 -end-1 min-w-5 h-5 px-1 rounded-full bg-red-600 border-2 border-ink text-[10px] font-bold text-white flex items-center justify-center">
        </span>
    </button>

    <div
        x-cloak
        x-show="open"
        x-transition.origin.top.right
        @click.outside="open = false"
        class="absolute z-[90] top-full end-0 mt-2 w-[calc(100vw_-_2rem)] max-w-sm overflow-hidden rounded-xl border border-ember/30 bg-paper shadow-2xl">

        <div class="flex items-center justify-between gap-3 px-4 py-3 bg-ink">
            <div>
                <h2 class="text-sm font-bold text-paper">Notificações</h2>
                <p class="text-xs text-paper/50" x-text="unreadLabel()"></p>
            </div>

            <button
                type="button"
                x-show="unreadCount > 0"
                @click="markAllAsRead()"
                class="text-xs font-semibold text-ember hover:text-paper transition">
                Marcar todas como lidas
            </button>
        </div>

        <div class="max-h-96 overflow-y-auto divide-y divide-ink/10">
            <div x-show="loading" class="px-4 py-8 text-center text-sm text-ink/50">
                Carregando notificações...
            </div>

            <template x-if="!loading && notifications.length === 0">
                <div class="px-4 py-8 text-center">
                    <svg class="w-8 h-8 mx-auto text-ink/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75V9a6 6 0 00-12 0v.75a8.967 8.967 0 01-2.312 6.022 23.848 23.848 0 005.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0" />
                    </svg>
                    <p class="mt-2 text-sm text-ink/50">Nenhuma notificação.</p>
                </div>
            </template>

            <template x-for="notification in notifications" :key="notification.id">
                <button
                    type="button"
                    @click="openNotification(notification)"
                    class="w-full px-4 py-3 text-left transition hover:bg-ember/5"
                    :class="notification.read ? 'bg-paper' : 'bg-ember/10'">
                    <div class="flex items-start gap-3">
                        <span
                            class="mt-1.5 w-2.5 h-2.5 rounded-full shrink-0"
                            :class="notification.read ? 'bg-ink/20' : 'bg-ember'">
                        </span>
                        <span class="min-w-0">
                            <span class="block text-sm font-bold text-ink" x-text="notification.title"></span>
                            <span class="block mt-1 text-sm text-ink/70 leading-snug" x-text="notification.message"></span>
                            <span class="block mt-1.5 text-xs text-ink/40" x-text="notification.created_at"></span>
                        </span>
                    </div>
                </button>
            </template>
        </div>
    </div>

    <div
        x-cloak
        x-show="toast"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-x-6"
        x-transition:enter-end="opacity-100 translate-x-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-x-0"
        x-transition:leave-end="opacity-0 translate-x-6"
        class="fixed z-[100] top-20 end-4 w-[calc(100vw_-_2rem)] max-w-sm overflow-hidden rounded-xl border-l-4 border-ember bg-ink shadow-2xl">
        <div class="flex items-start gap-3 p-4">
            <span class="w-10 h-10 rounded-full bg-ember/15 text-ember flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2m5-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </span>

            <button type="button" @click="openNotification(toast)" class="min-w-0 flex-1 text-left">
                <span class="block text-sm font-bold text-paper" x-text="toast?.title"></span>
                <span class="block mt-1 text-sm text-paper/70 leading-snug" x-text="toast?.message"></span>
            </button>

            <button type="button" @click="closeToast()" class="text-paper/50 hover:text-paper" aria-label="Fechar notificação">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>
</div>
