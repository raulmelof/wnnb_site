function obterParametrosDaURL() {
    const params = new URLSearchParams(window.location.search);
    return {
        nome: params.get("nome"),
        preco: params.get("preco"),
        imagem: params.get("imagem"),
        descricao: params.get("descricao") || "Nenhuma descrição disponível para este produto."
    };
}

function preencherDetalhesDoProduto() {
    const produto = obterParametrosDaURL();

    document.getElementById("nome-produto").innerText = produto.nome;
    document.getElementById("preco-produto").innerText = `R$ ${parseFloat(produto.preco).toFixed(2)}`;
    document.getElementById("imagem-produto").src = produto.imagem;
    document.getElementById("descricao-produto").innerText = produto.descricao;
}

window.onload = preencherDetalhesDoProduto;
