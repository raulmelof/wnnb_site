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
            <div class="imagem-container" style="cursor:pointer;">
                <img src="${produto.imagem}" alt="${produto.nome}" class="imagem-normal">
                ${produto.imagemHover ? `<img src="${produto.imagemHover}" alt="${produto.nome}" class="imagem-hover">` : ""}
            </div>
            <h3>${produto.nome}</h3>
            <p>R$ ${produto.preco.toFixed(2)}</p>
            <button class="btn btn-primary btn-sm" onclick='adicionarAoCarrinho(${JSON.stringify(produto).replace(/'/g, "\\'")})'>Adicionar ao carrinho</button>
        `;

        div.querySelector('.imagem-container').addEventListener("click", function () {
            window.location.href = `parts/produto.php?nome=${encodeURIComponent(produto.nome)}&preco=${produto.preco}&imagem=${encodeURIComponent(produto.imagem)}&descricao=${encodeURIComponent(produto.descricao || '')}`;
        });

        vitrine.appendChild(div);
    });
}

function adicionarAoCarrinho(produto) {
    let carrinho = JSON.parse(localStorage.getItem("carrinho")) || [];
    
    // Ensure the image path is correct
    produto.imagem = produto.imagem.startsWith("http") ? produto.imagem : `projeto/imagens/${produto.imagem}`;
    
    carrinho.push(produto);
    localStorage.setItem("carrinho", JSON.stringify(carrinho));
    alert(`${produto.nome} foi adicionado ao carrinho!`);
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
