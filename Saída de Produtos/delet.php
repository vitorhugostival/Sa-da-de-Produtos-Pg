<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atualizar Estoque</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="estilo.css">
</head>
<body>

<?php
    include('conexao.php'); // Inclui a conexão com o PostgreSQL

    $deletar = $_POST['deletar']; // Recebe o ID do produto a ser deletado

    // Verifica se o produto (id_saida) existe antes de tentar deletar
    $sql_verificar = "SELECT * FROM saida WHERE id_saida = $1";
    $result_verificar = pg_prepare($conexao, "verificar_saida", $sql_verificar);
    $result_verificar = pg_execute($conexao, "verificar_saida", array($deletar));

    if (pg_num_rows($result_verificar) > 0) {
        // Se o produto existir, realiza a exclusão
        $sql = "DELETE FROM saida WHERE id_saida = $1";
        $result = pg_prepare($conexao, "delete_saida", $sql);
        $result = pg_execute($conexao, "delete_saida", array($deletar));

        if ($result) {
            echo "<h1>Produto excluído com sucesso</h1>";
        } else {
            echo "<h1>Erro ao excluir o produto: " . pg_last_error($conexao) . "</h1>";
        }
    } else {
        // Se o produto não existir, exibe mensagem de erro
        echo "<h1>Erro: Produto com ID $deletar não encontrado</h1>";
    }

    // Fecha a conexão com o banco de dados
    pg_close($conexao);
?>

</body>
</html>
