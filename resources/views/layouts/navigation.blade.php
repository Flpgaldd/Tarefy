{{-- 🎨 ALTERADO: nav de bg-white/dark:bg-gray-800 para bg-ink com texto branco.
     É o principal "grande bloco estrutural preto" pedido no briefing — fica fixo
     em todas as páginas e ancora a identidade visual. --}}
{{-- 🎯 ALTERADO: os dados do usuário são preparados uma única vez para o header.
     O nome completo continua salvo normalmente no banco, mas a navegação exibe
     somente o primeiro nome e usa sua inicial quando não existe foto de perfil. --}}
@php
    $navigationUser = Auth::user();
    $navigationFirstName = preg_split('/\s+/', trim($navigationUser->name))[0] ?? 'Usuário';
    $navigationAvatarInitial = mb_strtoupper(mb_substr($navigationFirstName, 0, 1));
@endphp

<nav x-data="{ open: false }" class="bg-ink border-b border-ink-soft">
    <!-- Primary Navigation Menu -->
    <div class="max-w-2x1 mx-auto px-3 sm:px-3 lg:px-4">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        {{-- 🎨 ALTERADO: logo genérico do Breeze trocado pela marca Tarefy.
                             Uso a versão "inverse" (quadrado laranja + check preto) porque
                             a navbar é preta — a versão padrão (quadrado preto) ficaria
                             invisível sobre o próprio fundo preto. --}}
                        <x-application-logo-horizontal-inverse class="block h-10 w-25 flex items-center h-10 w-25" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Home') }}
                    </x-nav-link>
                    <x-nav-link :href="route('tasks.index')" :active="request()->routeIs('tasks.index')">
                        {{ __('Minhas Tarefas') }}
                    </x-nav-link>
                     <x-nav-link :href="route('profile.perfil')" :active="request()->routeIs('profile.perfil')">
                        {{ __('Perfil') }}
                    </x-nav-link>
                </div>
            </div>

            {{-- 🎯 ALTERADO: sino global inserido antes do nome e da foto do
                 usuário. No mobile ele permanece ao lado do menu hambúrguer. --}}
            @include('layouts.notifications')

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        {{-- 🎨 ALTERADO: botão do usuário em texto branco sobre preto,
                             com hover laranja em vez de cinza. --}}
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-paper bg-ink hover:text-ember focus:outline-none transition ease-in-out duration-150">
                            {{-- 🎯 ALTERADO: avatar circular adicionado ao lado do nome no
                                 header desktop. Sem foto, o círculo mostra a inicial do
                                 primeiro nome e mantém o mesmo destaque laranja do tema. --}}
                            {{-- 🎯 ALTERADO: a borda cinza do avatar foi substituída por
                                 uma borda branca para destacar a foto sobre o header preto. --}}
                            <span class="w-8 h-8 rounded-full bg-ember border-2 border-white flex items-center justify-center overflow-hidden shrink-0">
                                @if ($navigationUser->avatar_url)
                                    <img
                                        src="{{ $navigationUser->avatar_url }}"
                                        alt="Foto de perfil de {{ $navigationFirstName }}"
                                        class="w-full h-full object-cover"
                                    >
                                @else
                                    <span class="text-xs font-bold text-ink">
                                        {{ $navigationAvatarInitial }}
                                    </span>
                                @endif
                            </span>

                            <div class="ms-2">{{ $navigationFirstName }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.perfil')">
                            {{ __('Perfil') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                {{-- 🎨 ALTERADO: ícone hamburguer branco/laranja no hover, sobre fundo preto. --}}
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-paper hover:text-ember hover:bg-ink-soft focus:outline-none focus:bg-ink-soft focus:text-ember transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-ink">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        {{-- 🎨 ALTERADO: bloco de identificação do usuário (mobile) em branco sobre preto. --}}
        <div class="pt-4 pb-1 border-t border-ink-soft">
            {{-- 🎯 ALTERADO: a identificação no menu mobile replica o avatar
                 circular e o primeiro nome usados no header desktop. --}}
            <div class="px-4 flex items-center gap-3">
                {{-- 🎯 ALTERADO: a versão mobile também usa borda branca para
                     manter o mesmo acabamento visual do avatar no desktop. --}}
                <span class="w-10 h-10 rounded-full bg-ember border-2 border-white flex items-center justify-center overflow-hidden shrink-0">
                    @if ($navigationUser->avatar_url)
                        <img
                            src="{{ $navigationUser->avatar_url }}"
                            alt="Foto de perfil de {{ $navigationFirstName }}"
                            class="w-full h-full object-cover"
                        >
                    @else
                        <span class="text-sm font-bold text-ink">
                            {{ $navigationAvatarInitial }}
                        </span>
                    @endif
                </span>

                <div>
                    <div class="font-medium text-base text-paper">{{ $navigationFirstName }}</div>
                    <div class="font-medium text-sm text-paper/60">{{ $navigationUser->email }}</div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
