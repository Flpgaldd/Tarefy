import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

// 🎯 NOVO: contagem regressiva reutilizável da página de detalhes. O cálculo
// recebe o ISO 8601 com fuso do Laravel e exibe somente dias, horas e minutos.
window.taskCountdown = (dueAt) => ({
    days: 0,
    hours: 0,
    minutes: 0,
    expired: false,
    timer: null,

    // 🎯 ALTERADO: os métodos de ciclo de vida do Alpine iniciam e encerram o
    // relógio automaticamente quando o usuário entra ou sai da página.
    init() {
        this.start();
    },

    destroy() {
        this.stop();
    },

    start() {
        this.update();
        this.timer = window.setInterval(() => this.update(), 30000);
    },

    stop() {
        window.clearInterval(this.timer);
    },

    update() {
        const targetTimestamp = new Date(dueAt).getTime();

        if (Number.isNaN(targetTimestamp)) {
            this.stop();
            return;
        }

        const difference = targetTimestamp - Date.now();
        this.expired = difference < 0;

        // 🎯 NOVO: arredondar para cima evita mostrar zero minutos enquanto
        // ainda restam alguns segundos antes do vencimento.
        const totalMinutes = Math.ceil(Math.abs(difference) / 60000);

        this.days = Math.floor(totalMinutes / 1440);
        this.hours = Math.floor((totalMinutes % 1440) / 60);
        this.minutes = totalMinutes % 60;
    },
});

// 🎯 ALTERADO: central global usada pelo sino e pelo toast de lembretes.
// Ela consulta o backend periodicamente, mantém o contador de não lidas,
// evita repetir o mesmo toast na sessão e permite marcar notificações como lidas.
window.notificationCenter = (config) => ({
    notifications: [],
    unreadCount: 0,
    loading: true,
    open: false,
    toast: null,
    pollTimer: null,
    toastTimer: null,

    async start() {
        await this.refresh(true);

        this.pollTimer = window.setInterval(() => {
            this.refresh(true);
        }, 10000);
    },

    async request(url, options = {}) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

        const response = await fetch(url, {
            credentials: 'same-origin',
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                ...options.headers,
            },
            ...options,
        });

        if (!response.ok) {
            throw new Error(`Falha ao consultar notificações: ${response.status}`);
        }

        return response.json();
    },

    async refresh(showToast = false) {
        try {
            const data = await this.request(config.indexUrl);

            this.notifications = data.notifications ?? [];
            this.unreadCount = data.unread_count ?? 0;

            if (showToast) {
                const latestUnread = this.notifications.find((notification) => !notification.read);
                const lastToastId = window.sessionStorage.getItem('tarefy:last-notification-toast');

                if (latestUnread && latestUnread.id !== lastToastId) {
                    this.showToast(latestUnread);
                    window.sessionStorage.setItem('tarefy:last-notification-toast', latestUnread.id);
                }
            }
        } catch (error) {
            console.error(error);
        } finally {
            this.loading = false;
        }
    },

    async toggle() {
        this.open = !this.open;

        if (this.open) {
            await this.refresh(false);
        }
    },

    unreadLabel() {
        if (this.unreadCount === 0) {
            return 'Tudo em dia';
        }

        return `${this.unreadCount} ${this.unreadCount === 1 ? 'não lida' : 'não lidas'}`;
    },

    showToast(notification) {
        window.clearTimeout(this.toastTimer);
        this.toast = notification;

        this.toastTimer = window.setTimeout(() => {
            this.closeToast();
        }, 8000);
    },

    closeToast() {
        window.clearTimeout(this.toastTimer);
        this.toast = null;
    },

    async markAsRead(notification) {
        if (notification.read) {
            return;
        }

        const readUrl = config.readUrlTemplate.replace('__NOTIFICATION__', notification.id);
        const data = await this.request(readUrl, { method: 'PATCH' });

        notification.read = true;
        this.unreadCount = data.unread_count ?? Math.max(0, this.unreadCount - 1);
    },

    async openNotification(notification) {
        try {
            await this.markAsRead(notification);
        } catch (error) {
            console.error(error);
        }

        if (notification.url) {
            window.location.assign(notification.url);
        }
    },

    async markAllAsRead() {
        try {
            await this.request(config.readAllUrl, { method: 'PATCH' });
            this.notifications = this.notifications.map((notification) => ({
                ...notification,
                read: true,
            }));
            this.unreadCount = 0;
        } catch (error) {
            console.error(error);
        }
    },
});

Alpine.start();
