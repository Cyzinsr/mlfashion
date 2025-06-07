 function carregarProdutos() {
    const produtosContainer = document.querySelector(".produtos");
    produtosContainer.innerHTML = ""; // Limpa produtos já existentes no HTML
  
    let produtos = JSON.parse(localStorage.getItem("produtos")) || [];
  
    // Se quiser, você pode manter os produtos "fixos" que já estão no HTML, mas aqui vou substituir por todos do localStorage:
    produtos.forEach(produto => {
      const divProduto = document.createElement("div");
      divProduto.classList.add("produto");
  
      divProduto.innerHTML = 
        <img src="${produto.imagemBase64}" alt="${produto.nome}" />
        <h3>${produto.nome}</h3>
        <p>${produto.preco}</p>
      ;
  
      produtosContainer.appendChild(divProduto);
    });
  }
  
  window.addEventListener("load", carregarProdutos);
