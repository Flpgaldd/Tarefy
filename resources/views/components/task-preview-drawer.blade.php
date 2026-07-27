{{-- 🎯 NOVO: painel lateral compartilhado para consultar uma tarefa sem sair da
     página atual. Não existem formulários, selects ou links de edição nele. --}}
<div
    x-data="taskPreviewDrawer()"
    x-on:open-task-preview.window="show($event.detail)"
    x-on:keydown.escape.window="close()"
    x-cloak>
    <div
        x-show="open"
        class="fixed inset-0 z-[80]"
        role="dialog"
        aria-modal="true"
        aria-labelledby="task-preview-title">
        {{-- 🎯 NOVO: a camada escura fecha a visualização ao ser clicada e mantém
             o foco visual na pequena aba aberta à direita. --}}
        <button
            type="button"
            x-show="open"
            x-transition.opacity
            x-on:click="close()"
            class="absolute inset-0 h-full w-full cursor-default bg-ink/65"
            aria-label="Fechar visualização rápida da tarefa">
        </button>

        {{-- 🎯 NOVO: a transição desloca a aba pela direita e funciona tanto em
             telas grandes quanto no celular, onde ela ocupa quase toda a largura. --}}
        <aside
            x-show="open"
            x-transition:enter="transform transition ease-out duration-300"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transform transition ease-in duration-200"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            class="absolute inset-y-0 right-0 flex w-[calc(100%_-_1.5rem)] max-w-md flex-col bg-paper shadow-2xl">
            <div class="h-1.5 shrink-0 bg-ember"></div>

            <div class="flex items-start justify-between gap-4 border-b border-ink/10 bg-ink px-6 py-5">
                <div class="min-w-0">
                    <p class="text-[10px] font-semibold uppercase tracking-[0.2em] text-ember">
                        Visualização rápida
                    </p>
                    <h2
                        id="task-preview-title"
                        class="mt-2 break-words text-xl font-bold text-paper"
                        x-text="task?.title">
                    </h2>
                </div>

                <button
                    type="button"
                    x-ref="closeButton"
                    x-on:click="close()"
                    class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full border border-paper/15 text-paper/65 transition hover:border-ember hover:bg-ember hover:text-paper focus:outline-none focus:ring-2 focus:ring-ember"
                    aria-label="Fechar visualização rápida">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto px-6 py-6">
                <template x-if="task">
                    <div class="space-y-6">
                        {{-- 🎯 NOVO: status e prioridade são apresentados como
                             etiquetas informativas, sem permitir alterações. --}}
                        <div class="flex flex-wrap items-center gap-2">
                            <span
                                class="inline-flex rounded-full border px-3 py-1 text-xs font-bold uppercase tracking-wider"
                                x-bind:class="statusClasses()"
                                x-text="task.status">
                            </span>
                            <span
                                class="text-sm font-semibold"
                                x-bind:class="priorityClasses()"
                                x-text="task.priorityLabel">
                            </span>
                        </div>

                        <section>
                            <h3 class="text-xs font-semibold uppercase tracking-widest text-ink/45">
                                Descrição
                            </h3>
                            <p
                                class="mt-3 whitespace-pre-line break-words text-sm leading-relaxed"
                                x-bind:class="task.description ? 'text-ink/75' : 'text-ink/40 italic'"
                                x-text="task.description || 'Esta tarefa ainda não possui descrição.'">
                            </p>
                        </section>

                        {{-- 🎯 NOVO: datas importantes ficam organizadas em cartões
                             separados para facilitar uma consulta rápida. --}}
                        <section class="grid gap-3">
                            {{-- 🎯 ALTERADO: o vencimento usa um fundo laranja
                                 claro para manter contraste com o texto escuro. --}}
                            <div class="rounded-lg border border-ember/20 bg-orange-50 px-4 py-4">
                                <p class="text-[10px] font-semibold uppercase tracking-widest text-ember-dark">
                                    Vencimento
                                </p>
                                <p class="mt-1 text-sm font-bold text-ink" x-text="task.dueAt || 'Não informado'"></p>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div class="rounded-lg border border-ink/10 px-4 py-4">
                                    <p class="text-[10px] font-semibold uppercase tracking-widest text-ink/40">
                                        Criada em
                                    </p>
                                    <p class="mt-1 text-xs font-semibold text-ink/70" x-text="task.createdAt || 'Não informado'"></p>
                                </div>
                                <div class="rounded-lg border border-ink/10 px-4 py-4">
                                    <p class="text-[10px] font-semibold uppercase tracking-widest text-ink/40">
                                        Atualizada em
                                    </p>
                                    <p class="mt-1 text-xs font-semibold text-ink/70" x-text="task.updatedAt || 'Não informado'"></p>
                                </div>
                            </div>
                        </section>

                        <div class="rounded-lg border border-ember/25 bg-ember/10 px-4 py-4">
                            <p class="text-xs leading-relaxed text-ink/65">
                                Esta aba é somente para consulta. Para alterar a tarefa, use
                                <strong class="text-ink">Ver detalhes</strong> na página Minhas Tarefas.
                            </p>
                        </div>
                    </div>
                </template>
            </div>
        </aside>
    </div>
</div>
