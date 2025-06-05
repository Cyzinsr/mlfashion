function carregarProdutos() {
    const produtosContainer = document.querySelector(".produtos");
    produtosContainer.innerHTML = ""; // Limpa os produtos atuais
  
    let produtos = JSON.parse(localStorage.getItem("produtos")) || [];
  
    if (produtos.length === 0) {
      produtosContainer.innerHTML = `
        <p>Nenhum produto cadastrado.</p>
      `;
      return;
    }
  
    produtos.forEach(produto => {
      const divProduto = document.createElement("div");
      divProduto.classList.add("produto");
  
      divProduto.innerHTML = `
        <img src="${produto.imagemBase64}" alt="${produto.nome}" />
        <h3>${produto.nome}</h3>
        <p>${produto.preco}</p>
      `;
  
      produtosContainer.appendChild(divProduto);
    });
  }
  
  window.addEventListener("load", carregarProdutos);
  