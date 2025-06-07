// Formatar preço (como você pediu)
function formatarPrecoInput(input) {
  input.addEventListener("input", function () {
    let valor = this.value.replace(/\D/g, "");
    if (!valor) {
      this.value = "";
      return;
    }
    valor = String(Number(valor));
    while (valor.length < 3) {
      valor = "0" + valor;
    }
    let inteiro = valor.slice(0, -2);
    let centavos = valor.slice(-2);
    inteiro = inteiro.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    this.value = `R$ ${inteiro},${centavos}`;
  });
}

const precoInput = document.getElementById("preco");
formatarPrecoInput(precoInput);

const listaProdutos = document.getElementById("listaProdutos");
const buscaProduto = document.getElementById("buscaProduto");

let produtosCache = [];

// Carregar produtos do servidor
async function carregarLista(filtrar = "") {
  try {
    const response = await fetch("listar_produtos.php");
    if (!response.ok) throw new Error("Erro ao buscar produtos");
    const produtos = await response.json();
    produtosCache = produtos;

    let produtosFiltrados = produtos;
    if (filtrar) {
      const filtroLower = filtrar.toLowerCase();
      produtosFiltrados = produtos.filter(p => p.nome.toLowerCase().includes(filtroLower));
    }

    listaProdutos.innerHTML = "";

    produtosFiltrados.forEach(produto => {
      const item = document.createElement("div");
      item.className = "produto";
      item.innerHTML = `
        <img src="${produto.imagem_url}" alt="${produto.nome}" />
        <h3>${produto.nome}</h3>
        <p>${produto.preco}</p>
        <button class="pedido-btn btn-excluir" data-id="${produto.id}">Excluir</button>
      `;
      listaProdutos.appendChild(item);
    });

    // Botões excluir
    document.querySelectorAll(".btn-excluir").forEach(btn => {
      btn.addEventListener("click", async () => {
        const id = btn.getAttribute("data-id");
        if (confirm("Deseja excluir este produto?")) {
          await excluirProduto(id);
          carregarLista(buscaProduto.value);
        }
      });
    });

  } catch (error) {
    alert(error.message);
  }
}

// Enviar novo produto
document.getElementById("formProduto").addEventListener("submit", async (e) => {
  e.preventDefault();

  const nome = document.getElementById("nome").value.trim();
  const preco = document.getElementById("preco").value.trim();
  const imagemArquivo = document.getElementById("imagemArquivo").files[0];

  if (!imagemArquivo) {
    alert("Selecione uma imagem.");
    return;
  }

  const formData = new FormData();
  formData.append("nome", nome);
  formData.append("preco", preco);
  formData.append("imagemArquivo", imagemArquivo);

  try {
    const response = await fetch("adicionar_produto.php", {
      method: "POST",
      body: formData
    });

    const resultado = await response.json();

    if (resultado.sucesso) {
      alert("Produto adicionado com sucesso!");
      e.target.reset();
      carregarLista(buscaProduto.value);
    } else {
      alert("Erro ao adicionar produto: " + resultado.mensagem);
    }
  } catch (error) {
    alert("Erro ao enviar dados: " + error.message);
  }
});

// Excluir produto
async function excluirProduto(id) {
  try {
    const formData = new FormData();
    formData.append("id", id);

    const response = await fetch("excluir_produto.php", {
      method: "POST",
      body: formData
    });

    const resultado = await response.json();

    if (!resultado.sucesso) {
      alert("Erro ao excluir produto: " + resultado.mensagem);
    }
  } catch (error) {
    alert("Erro ao excluir produto: " + error.message);
  }
}

// Pesquisa dinâmica
buscaProduto.addEventListener("input", () => {
  carregarLista(buscaProduto.value);
});

// Inicializar ao carregar página
window.onload = () => carregarLista();
