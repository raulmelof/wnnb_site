<main>
        <div class="form-container">
            <div class="form-tabs">
                <button class="tab-button active" onclick="showForm('login')">Login</button>
                <button class="tab-button" onclick="showForm('register')">Cadastro</button>
            </div>

            <form id="login" class="form-content">
                <h2>Entrar</h2>
                <div class="form-group">
                    <label for="loginEmail">Email</label>
                    <input type="email" id="loginEmail" required>
                </div>
                <div class="form-group">
                    <label for="loginPassword">Senha</label>
                    <input type="password" id="loginPassword" required>
                </div>
                <button type="submit" class="form-btn">Entrar</button>
                <p class="switch-form">Não tem uma conta? <a href="#" onclick="showForm('register')">Cadastre-se</a></p>
            </form>

            <form id="register" class="form-content hidden">
                <h2>Cadastro</h2>
                <div class="form-group">
                    <label for="registerName">Nome Completo</label>
                    <input type="text" id="registerName" required>
                </div>
                <div class="form-group">
                    <label for="registerEmail">Email</label>
                    <input type="email" id="registerEmail" required>
                </div>
                <div class="form-group">
                    <label for="registerPassword">Senha</label>
                    <input type="password" id="registerPassword" required>
                </div>
                <div class="form-group">
                    <label for="registerPasswordConfirm">Confirmar Senha</label>
                    <input type="password" id="registerPasswordConfirm" required>
                </div>
                <button type="submit" class="form-btn">Cadastrar</button>
                <p class="switch-form">Já tem uma conta? <a href="#" onclick="showForm('login')">Entrar</a></p>
            </form>
        </div>
    </main>