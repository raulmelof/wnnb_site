/*function carregarProdutos() {
    fetch("produtos.json")
        .then(response => response.json())
        .then(produtos => {
            let produtosFiltrados = produtos.filter(produto => produto.categoria === "camisetas");
            exibirProdutos(produtosFiltrados);
        })
        .catch(error => {
            console.error("Erro ao carregar produtos:", error);
        });

        window.onload = function () {
            carregarProdutos();
        };
}*/

function carregarProdutos() {
    fetch("produtos.json")
        .then(response => response.json())
        .then(produtos => {
            window.todosOsProdutos = produtos; // Armazena os produtos na variável global
            exibirProdutos(produtos); // Exibe todos os produtos inicialmente
        })
        .catch(error => {
            console.error("Erro ao carregar produtos:", error);
        });
}

// Carregar produtos ao iniciar a página
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
            window.location.href = `produto.html?nome=${encodeURIComponent(produto.nome)}&preco=${produto.preco}&imagem=${encodeURIComponent(produto.imagem)}&descricao=${encodeURIComponent(produto.descricao || '')}`;
        });

        vitrine.appendChild(div);
    });
}

window.onload = function() {
    carregarProdutos();
};

/*function aplicarFiltros() {
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
}*/

function aplicarFiltros() {
    // Verifica se os produtos foram carregados
    if (!window.todosOsProdutos) {
        console.error("Produtos não carregados.");
        return;
    }

    // Obter valores selecionados
    const tamanhosSelecionados = [];
    document.querySelectorAll('input[type="checkbox"]:checked').forEach(checkbox => {
        tamanhosSelecionados.push(checkbox.value);
    });

    const precoMaximo = parseFloat(document.getElementById('precoRange').value);

    // Filtrar produtos
    const produtosFiltrados = window.todosOsProdutos.filter(produto => {
        const atendeTamanho = tamanhosSelecionados.length === 0 || tamanhosSelecionados.includes(produto.tamanho);
        const atendePreco = produto.preco <= precoMaximo;
        return atendeTamanho && atendePreco;
    });

    // Exibir produtos filtrados
    exibirProdutos(produtosFiltrados);
}

document.getElementById('precoRange').addEventListener('input', function () {
    document.getElementById('precoValor').textContent = this.value;
});