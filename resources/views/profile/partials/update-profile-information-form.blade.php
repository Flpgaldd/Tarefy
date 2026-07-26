<section>
    <header>
        {{-- 🎨 ALTERADO: mesma troca de cor de texto do restante do app. --}}
        <h2 class="text-lg font-medium text-ink">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-ink/60">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data" x-data="{ preview: null }">
        {{-- 🎯 ALTERADO: enctype="multipart/form-data" adicionado — sem isso, o
             navegador NÃO envia o arquivo de imagem, só o nome dele como texto.
             x-data="{ preview: null }" guarda a prévia da foto escolhida antes
             mesmo de salvar (ver campo de foto logo abaixo). --}}
        @csrf
        @method('patch')

        {{-- ===================== FOTO DE PERFIL ===================== --}}
        <div>
            <x-input-label value="{{ __('Foto de perfil') }}" />

            <div class="mt-2 flex items-center gap-4">
                {{-- Prévia: mostra a foto atual do usuário; se ele escolher um
                     arquivo novo, troca pra prévia dele em tempo real (via Alpine),
                     sem precisar salvar o formulário pra ver como vai ficar. --}}
                {{-- 🎯 ALTERADO: tanto a foto atual quanto o fallback da prévia
                     agora possuem borda branca no lugar da borda cinza. --}}
                <div class="relative w-20 h-20 shrink-0">
                    <img
                        :src="preview ?? '{{ $user->avatar_url ?? '' }}'"
                        x-show="preview || {{ $user->avatar_url ? 'true' : 'false' }}"
                        class="w-20 h-20 rounded-full object-cover border-2 border-white"
                        alt="{{ __('Foto de perfil') }}">

                    <div
                        x-show="!preview && {{ $user->avatar_url ? 'false' : 'true' }}"
                        class="w-20 h-20 rounded-full bg-ember border-2 border-white flex items-center justify-center">
                        <span class="text-xl font-bold text-ink">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </span>
                    </div>
                </div>

                <div>
                    <label for="avatar"
                        class="inline-flex items-center px-4 py-2 bg-paper border-2 border-ink hover:bg-ink hover:text-paper text-ink font-semibold text-xs uppercase tracking-widest rounded-md cursor-pointer transition ease-in-out duration-150">
                        {{ __('Escolher foto') }}
                    </label>
                    <input
                        id="avatar"
                        name="avatar"
                        type="file"
                        accept="image/png, image/jpeg"
                        class="hidden"
                        x-on:change="
                            const file = $event.target.files[0];
                            preview = file ? URL.createObjectURL(file) : null;
                        ">
                    <p class="text-xs text-ink/50 mt-1.5">{{ __('JPG ou PNG, até 2MB.') }}</p>
                </div>
            </div>

            <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
        </div>

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-ink">
                        {{ __('Your email address is unverified.') }}

                        {{-- 🎨 ALTERADO: link sublinhado de text-gray-600/focus:ring-indigo-500
                             para text-ink/60 com hover e foco em ember. --}}
                        <button form="send-verification" class="underline text-sm text-ink/60 hover:text-ember-dark rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ember">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        {{-- 🎨 ALTERADO: de text-green-600 (cor fora da paleta) para text-ember-dark. --}}
                        <p class="mt-2 font-medium text-sm text-ember-dark">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        {{-- ===================== SOBRE VOCÊ (BIO) ===================== --}}
        <div x-data="{ bio: {{ Illuminate\Support\Js::from(old('bio', $user->bio ?? '')) }} }">
            <x-input-label for="bio" :value="__('Sobre você')" />
            <textarea id="bio" name="bio" rows="4" maxlength="500"
                x-model="bio"
                placeholder="{{ __('Conte um pouco sobre você...') }}"
                class="mt-1 block w-full border-ink/20 bg-paper text-ink focus:border-ember focus:ring-ember rounded-md shadow-sm">{{ old('bio', $user->bio) }}</textarea>
            <p class="text-xs text-ink/40 mt-1 text-right" x-text="bio.length + '/500'"></p>
            <x-input-error class="mt-2" :messages="$errors->get('bio')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                {{-- 🎨 ALTERADO: mesma troca de "Saved." para ember-dark. --}}
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm font-medium text-ember-dark"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
