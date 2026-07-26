<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            {{-- 🎨 ALTERADO: checkbox de indigo para ember (borda e anel de foco);
                 texto do label de text-gray-600/dark:text-gray-400 para text-ink/60. --}}
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-ink/30 text-ember shadow-sm focus:ring-ember" name="remember">
                <span class="ms-2 text-sm text-ink/60">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('register'))
                {{-- 🎯 ALTERADO: link "Registrar conta" adicionado à esquerda de
                     "Esqueceu a senha?", preservando o alinhamento, as cores e
                     o espaçamento que a área de ações do login já possuía. --}}
                <a class="me-4 underline text-sm text-ink/60 hover:text-ember-dark rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ember" href="{{ route('register') }}">
                    Registrar conta
                </a>
            @endif

            @if (Route::has('password.request'))
                {{-- 🎨 ALTERADO: link sublinhado de cinza/indigo para text-ink/60,
                     hover em ember-dark e anel de foco em ember. --}}
                <a class="underline text-sm text-ink/60 hover:text-ember-dark rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ember" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
