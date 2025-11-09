<section class="space-y-6">
    <header>
        <h2 class="h4 text-dark">
            Deletar Conta
        </h2>

        <p class="mt-1 text-muted">
            Depois que sua conta for excluída, todos os seus recursos e dados serão apagados permanentemente. Antes de excluir sua conta, faça o download de todos os dados ou informações que deseja reter.
        </p>
    </header>

    {{-- O Breeze usa um Modal para confirmação. Vamos estilizar o botão para o Bootstrap --}}
    <button 
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="btn btn-danger"
    >Deletar Conta</button>

    {{-- O HTML do Modal continuará usando classes do Tailwind, mas o botão principal estará correto --}}
    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                Você tem certeza que quer deletar sua conta?
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Depois que sua conta for excluída, todos os seus recursos e dados serão apagados permanentemente. Por favor, digite sua senha para confirmar que você gostaria de excluir permanentemente sua conta.
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="Password" class="sr-only" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-3/4"
                    placeholder="Password"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-danger-button class="ml-3">
                    {{ __('Delete Account') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>