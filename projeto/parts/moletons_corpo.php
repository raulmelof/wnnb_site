<div class="container">
    <aside class="sidebar">
        <div class="brand-link">
            <span class="brand-text font-weight-dark">Filtros</span>
        </div>

        <div class="form-group p-3">
            <label>Tamanho</label>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="tamanhoP" value="P">
                <label class="form-check-label" for="tamanhoP">P</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="tamanhoM" value="M">
                <label class="form-check-label" for="tamanhoM">M</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="tamanhoG" value="G">
                <label class="form-check-label" for="tamanhoG">G</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="tamanhoGG" value="GG">
                <label class="form-check-label" for="tamanhoGG">GG</label>
            </div>
        </div>

        <div class="form-group p-3">
            <label>Preço</label>
            <input type="range" class="form-range" id="precoRange" min="0" max="500" step="10">
            <p>Valor máximo: R$ <span id="precoValor">500</span></p>
        </div>

        <div class="form-group p-3">
            <button class="btn btn-primary btn-block" onclick="aplicarFiltros()">Aplicar Filtros</button>
        </div>
    </aside>

    <div class="content" id="vitrine"></div>
</div>
