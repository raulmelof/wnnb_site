<section>
    <header>
        <h2 class="h4 text-dark">
            Atualizar Senha
        </h2>

        <p class="mt-1 text-muted">
            Certifique-se de que sua conta esteja usando uma senha longa e aleatória para se manter segura.
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-4">
        @csrf
        @method('put')

        <div class="mb-3">
            <label for="update_password_current_password" class="form-label">Senha Atual</label>
            <input id="update_password_current_password" name="current_password" type="password" class="form-control" autocomplete="current-password">
            @error('current_password', 'updatePassword')
                 <div class="text-danger mt-2">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="update_password_password" class="form-label">Nova Senha</label>
            <input id="update_password_password" name="password" type="password" class="form-control" autocomplete="new-password">
             @error('password', 'updatePassword')
                 <div class="text-danger mt-2">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="update_password_password_confirmation" class="form-label">Confirmar Senha</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="form-control" autocomplete="new-password">
             @error('password_confirmation', 'updatePassword')
                 <div class="text-danger mt-2">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex align-items-center gap-4">
            <button type="submit" class="btn btn-primary">Salvar</button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-success"
                >Salvo.</p>
            @endif
        </div>
    </form>
</section>