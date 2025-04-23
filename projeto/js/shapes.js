function carregarProdutos() {
    fetch("produtos_public.json")
        .then(response => response.json())
        .then(produtos => {
            window.todosOsProdutos = produtos.filter(produto => produto.categoria === "shapes");
            exibirProdutos(window.todosOsProdutos);
        })
        .catch(error => {
            console.error("Erro ao carregar produtos:", error);
        });
}

window.onload = function () {
    carregarProdutos();
};

function exibirProdutos(produtos) {
    let vitrine = document.getElementById("vitrine");
    vitrine.innerHTML = "";

    produtos.forEach(produto => {
        let div = document.createElement("div");
        div.classList.add("produto");

        div.innerHTML = `
            <div class="imagem-container">
                <img src="${produto.imagem}" alt="${produto.nome}" class="imagem-normal">
                ${produto.imagemHover ? `<img src="${produto.imagemHover}" alt="${produto.nome}" class="imagem-hover">` : ""}
            </div>
            <h3>${produto.nome}</h3>
            <p>R$ ${produto.preco.toFixed(2)}</p>
        `;

        div.addEventListener("click", function () {
            window.location.href = `../public/produto.php?nome=${encodeURIComponent(produto.nome)}&preco=${produto.preco}&imagem=${encodeURIComponent(produto.imagem)}&descricao=${encodeURIComponent(produto.descricao || '')}`;
        });

        vitrine.appendChild(div);
    });
}

function aplicarFiltros() {
    if (!window.todosOsProdutos) {
        console.error("Produtos não carregados.");
        return;
    }

    const tamanhosSelecionados = [];
    document.querySelectorAll('input[type="checkbox"]:checked').forEach(checkbox => {
        tamanhosSelecionados.push(checkbox.value);
    });

    const precoMaximo = parseFloat(document.getElementById('precoRange').value);

    const produtosFiltrados = window.todosOsProdutos.filter(produto => {
        const atendeTamanho = tamanhosSelecionados.length === 0 || tamanhosSelecionados.includes(produto.tamanho);
        const atendePreco = produto.preco <= precoMaximo;
        return atendeTamanho && atendePreco;
    });

    exibirProdutos(produtosFiltrados);
}

document.getElementById('precoRange').addEventListener('input', function () {
    document.getElementById('precoValor').textContent = this.value;
});
