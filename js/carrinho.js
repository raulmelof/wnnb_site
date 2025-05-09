function carregarCarrinho() {
    const carrinho = JSON.parse(localStorage.getItem("carrinho")) || [];
    const container = document.getElementById("carrinho-itens");
    const totalContainer = document.getElementById("carrinho-total");

    container.innerHTML = "";
    let total = 0;

    if (carrinho.length === 0) {
        container.innerHTML = "<p>Seu carrinho está vazio.</p>";
        totalContainer.textContent = "";
        return;
    }

    carrinho.forEach((item, index) => {
        total += item.preco;

        const div = document.createElement("div");
        div.className = "card";
        div.innerHTML = `
            <img src="${item.imagem}" alt="${item.nome}">
            <div>
                <h5>${item.nome}</h5>
                <p>R$ ${item.preco.toFixed(2)}</p>
            </div>
            <button onclick="removerItem(${index})">Remover</button>
        `;
        container.appendChild(div);
    });

    totalContainer.textContent = `Total: R$ ${total.toFixed(2)}`;
}

function removerItem(index) {
    let carrinho = JSON.parse(localStorage.getItem("carrinho")) || [];
    carrinho.splice(index, 1);
    localStorage.setItem("carrinho", JSON.stringify(carrinho));
    carregarCarrinho();
}

function finalizarCompra() {
    alert("Compra finalizada! Obrigado.");
    localStorage.removeItem("carrinho");
    carregarCarrinho();
}

window.onload = carregarCarrinho;