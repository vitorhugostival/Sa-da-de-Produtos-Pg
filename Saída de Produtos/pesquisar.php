<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesquisar Produtos</title>
    <link rel="stylesheet" type="text/css" href="estilo.css">
</head>
<body>
    <h1>Pesquisar Produtos Cadastrados</h1>

    <!-- Formulário para pesquisa -->
    <form action="" method="post">
        <label for="nome_produto">Nome do Produto</label>
        <input type="text" name="nome_produto" id="nome_produto" placeholder="Nome do Produto" required>
        <br><br>

        <input type="submit" value="Pesquisar">
    </form>

<?php
include('conexao.php');

// Inicializa a variável de pesquisa
$nome_produto = '';

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome_produto = $_POST['nome_produto'];
}

// Monta a consulta SQL para buscar os produtos
$sql = "SELECT id_saida, imagem, id_usuario, nome_usuario, cod_produto, nome_produto, id_local, preco_custo, nome_local, id_estoque, qtd_saida, valor_total, observacao, data_saida FROM saida";

// Se o nome do produto foi fornecido, adiciona um filtro à consulta
if (!empty($nome_produto)) {
    $sql .= " WHERE nome_produto ILIKE '%$nome_produto%'";
}

$resultado = pg_query($conexao, $sql);

// Verifica se há resultados e os exibe em uma tabela
if (pg_num_rows($resultado) > 0) {
    echo "<table class='table'>
            <thead>
                <tr>
                    <th>ID Saída</th>
                    <th>Imagem</th>
                    <th>ID Usuário</th>
                    <th>Nome do Usuário</th>
                    <th>Código do Produto</th>
                    <th>Nome do Produto</th>
                    <th>ID Local</th>
                    <th>Preço de Custo</th>
                    <th>Nome do Local</th>
                    <th>ID Estoque</th>
                    <th>Quantidade de Saída</th>
                    <th>Valor Total</th>
                    <th>Observação</th>
                    <th>Data de Saída</th>
                </tr>
            </thead>
            <tbody>";
    
    while ($row = pg_fetch_assoc($resultado)) {
        // Verifica se a imagem existe e gera a tag img
        $imgTag = '';
        if (!empty($row['imagem'])) {
            $imgBase64 = base64_encode(pg_unescape_bytea($row['imagem'])); // Convertendo binário para base64
            $imgTag = "<img src='data:image/jpeg;base64," . $imgBase64 . "' width='100' height='100' alt='Imagem do Produto' />";
        } else {
            $imgTag = "<span>Sem imagem</span>"; // Caso não tenha imagem
        }

        echo "<tr>
                <td>" . htmlspecialchars($row['id_saida']) . "</td>
                <td>" . $imgTag . "</td>
                <td>" . htmlspecialchars($row['id_usuario']) . "</td>
                <td>" . htmlspecialchars($row['nome_usuario']) . "</td>
                <td>" . htmlspecialchars($row['cod_produto']) . "</td>
                <td>" . htmlspecialchars($row['nome_produto']) . "</td>
                <td>" . htmlspecialchars($row['id_local']) . "</td>
                <td>" . htmlspecialchars($row['preco_custo']) . "</td>
                <td>" . htmlspecialchars($row['nome_local']) . "</td>
                <td>" . htmlspecialchars($row['id_estoque']) . "</td>
                <td>" . htmlspecialchars($row['qtd_saida']) . "</td>
                <td>" . htmlspecialchars($row['valor_total']) . "</td>
                <td>" . htmlspecialchars($row['observacao']) . "</td>
                <td>" . htmlspecialchars($row['data_saida']) . "</td>
              </tr>";
    }

    echo "</tbody></table>";
} else {
    echo "Zero Resultados";
}

pg_close($conexao);
?>

</body>
</html>
