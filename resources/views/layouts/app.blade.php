<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        {{-- 🎨 ALTERADO: favicon adicionado, apontando pro tarefy-mark.svg em public/images/.
             Sem esse arquivo salvo na pasta certa, o ícone da aba continua em branco. --}}
        <link rel="icon" type="image/svg+xml" href="{{ asset('images/tarefy-logo-horizontal.svg') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        {{--
            🎨 ALTERADO: fundo geral trocado de bg-gray-100/dark:bg-gray-900 para bg-mist.
            O tema dark:* foi removido em todo o layout porque a nova identidade já é
            fixa (preto estrutural + laranja + branco) — manter dark mode misturado
            criaria uma segunda paleta concorrendo com a oficial.
        --}}
        <div class="min-h-screen bg-mist">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                {{-- 🎨 ALTERADO: header de bg-white/shadow para bg-ink com barra lateral
                     laranja, criando o "bloco estrutural preto" pedido nas diretrizes
                     e dando o maior destaque visual ao título da página. --}}
                <header class="bg-ink border-l-4 border-ember">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 text-paper">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>

            {{-- 🎯 NOVO: o painel de visualização rápida fica no layout principal
                 para poder ser aberto pelos nomes das tarefas em qualquer página. --}}
            <x-task-preview-drawer />
        </div>
    </body>
</html>
