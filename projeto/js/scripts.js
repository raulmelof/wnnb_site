function mostrarSeção(seção) {
    const seções = document.querySelectorAll('.seção');
    seções.forEach(seção => {
        seção.style.display = 'none';
    });

    document.getElementById(seção).style.display = 'block';
}

function carregarProdutos() {
    fetch("produtos.json")
        .then(response => response.json())
        .then(produtos => {
            window.todosOsProdutos = produtos;
            exibirProdutos(produtos);
        });
}

function exibirProdutos(produtos) {
    let vitrine = document.getElementById("vitrine");
    vitrine.innerHTML = "";

    produtos.forEach(produto => {
        let div = document.createElement("div");
        div.classList.add("produto");
        div.setAttribute("data-categoria", produto.categoria);

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


function filtrarProdutos(categoria, event) {
    if (event) event.preventDefault();
    let produtosFiltrados = window.todosOsProdutos.filter(produto => produto.categoria === categoria);
    exibirProdutos(produtosFiltrados);
}

function mostrarTodosOsProdutos(event) {
    if (event) event.preventDefault();
    exibirProdutos(window.todosOsProdutos);
}

document.getElementById("busca").addEventListener("input", function () {
    let termo = this.value.toLowerCase();
    let produtos = document.querySelectorAll(".produto");

    produtos.forEach(produto => {
        let nomeProduto = produto.querySelector("h3").innerText.toLowerCase();
        if (nomeProduto.includes(termo)) {
            produto.style.display = "block";
        } else {
            produto.style.display = "none";
        }
    });
});



window.onload = function() {
    carregarProdutos();
    mostrarSeção('camisetas');
};

window.addEventListener('scroll', function() {
    let bannerHeight = document.getElementById('fullbanner').offsetHeight;
    if (window.scrollY >= bannerHeight) {
        document.body.classList.add('scrolled');
    } else {
        document.body.classList.remove('scrolled');
    }
});

function redirectToLogin() {
    window.location.href = 'login.html';
}
