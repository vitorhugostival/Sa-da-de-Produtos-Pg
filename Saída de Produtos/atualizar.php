<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atualizar Dados</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="estilo.css">
</head>
<body>

<?php
include('conexao.php'); 

// Recebe os dados do formulário
$id_saida = $_POST['NOVOid_saida'];
$id_usuario = $_POST['NOVOid_usuario'];
$nome_usuario = $_POST['NOVOnome_usuario'];
$cod_produto = $_POST['NOVOcod_produto'];
$nome_produto = $_POST['NOVOnome_produto'];
$id_local = $_POST['NOVOid_local'];
$preco_custo = $_POST['NOVOpreco_custo'];
$nome_local = $_POST['NOVOnome_local'];
$id_estoque = $_POST['NOVOid_estoque'];
$qtd_saida = $_POST['NOVOqtd_saida'];
$valor_total = $_POST['NOVOvalor_total'];
$observacao = $_POST['NOVOobservacao'];
$data_saida = $_POST['NOVOdata_saida'];

// Verifica se uma nova imagem foi enviada
$imagem = null;
if (isset($_FILES['NOVOimagem']) && $_FILES['NOVOimagem']['error'] == 0) {
    // Lê o conteúdo da imagem
    $imagem = pg_escape_bytea(file_get_contents($_FILES['NOVOimagem']['tmp_name']));
}

// Atualiza os dados no banco de dados
$sql = "UPDATE saida SET id_usuario = $1, nome_usuario = $2, cod_produto = $3, nome_produto = $4, id_local = $5, preco_custo = $6, nome_local = $7, id_estoque = $8, qtd_saida = $9, valor_total = $10, observacao = $11, data_saida = $12" .
       ($imagem ? ", imagem = $13" : "") . 
       " WHERE id_saida = $14";

$params = [
    $id_usuario,
    $nome_usuario,
    $cod_produto,
    $nome_produto,
    $id_local,
    $preco_custo,
    $nome_local,
    $id_estoque,
    $qtd_saida,
    $valor_total,
    $observacao,
    $data_saida
];

// Se a imagem foi enviada, adiciona ao array de parâmetros
if ($imagem) {
    $params[] = $imagem; // Adiciona a imagem
}
$params[] = $id_saida; // Adiciona o id_saida ao final

$result = pg_query_params($conexao, $sql, $params);

if ($result) {
    echo "Dados atualizados no estoque.<br><br>";
} else {
    echo "Erro na atualização do estoque: " . pg_last_error($conexao);
}

pg_close($conexao);
?>

</body>
</html>
