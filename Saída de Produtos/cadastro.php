<?php
include("conexao.php"); // Inclui o arquivo de conexão com o PostgreSQL

// Função para converter imagem para binário
function converteParaBinario($caminhoArquivo) {
    $conteudoArquivo = file_get_contents($caminhoArquivo);
    return pg_escape_bytea($conteudoArquivo); // Fechando corretamente a função
}

// Recebe os dados do formulário
$id_saida = $_POST['id_saida'];
$id_usuario = $_POST['id_usuario'];
$nome_usuario = $_POST['nome_usuario'];
$cod_produto = $_POST['cod_produto'];
$nome_produto = $_POST['nome_produto'];
$id_local = $_POST['id_local'];
$preco_custo = $_POST['preco_custo'];
$nome_local = $_POST['nome_local']; // Garantindo que esse campo é recebido corretamente
$id_estoque = $_POST['id_estoque'];
$qtd_saida = $_POST['qtd_saida'];
$valor_total = $_POST['valor_total'];
$observacao = $_POST['observacao']; // Garantindo que esse campo é recebido corretamente
$data_saida = $_POST['data_saida'];

$imagemBinaria = null; // Variável para armazenar a imagem convertida

// Verifica se o arquivo foi enviado
if (isset($_FILES['imagem'])) {
    $imagem = $_FILES['imagem'];
    // Verifica se houve algum erro no upload do arquivo
    if ($imagem['error'] != 0) {
        die("Falha ao enviar o arquivo");
    }
    // Verifica o tamanho do arquivo (máx. 2MB)
    if ($imagem['size'] > 2097152) {
        die("Arquivo muito grande! Max 2MB");
    }
    // Diretório para salvar os arquivos
    $pasta = "imagem/"; // Certifique-se de que essa pasta exista

    // Cria a pasta se ela não existir
    if (!is_dir($pasta)) {
        if (!mkdir($pasta, 0777, true)) {
            die("Falha ao criar a pasta");
        }
    }

    // Nome único para o arquivo
    $novoNomeArquivo = uniqid() . '.' . strtolower(pathinfo($imagem['name'], PATHINFO_EXTENSION));
    // Define o caminho completo para salvar o arquivo
    $caminhoArquivo = $pasta . $novoNomeArquivo;
    // Move o arquivo para o diretório de destino
    $deu_certo = move_uploaded_file($imagem["tmp_name"], $caminhoArquivo);
    if (!$deu_certo) {
        die("Falha ao mover o arquivo para o diretório");
    }

    // Converte a imagem para binário
    $imagemBinaria = converteParaBinario($caminhoArquivo);

    echo "Arquivo enviado com sucesso!<br>";
}

// Verifica se o ID da saída já existe
$check_saida_sql = "SELECT id_saida FROM saida WHERE id_saida = $1";
$result_check_saida = pg_prepare($conexao, "check_saida", $check_saida_sql);
$result_check_saida = pg_execute($conexao, "check_saida", array($id_saida));

// Condição para verificar se o ID da saída já existe
if (pg_num_rows($result_check_saida) > 0) {
    echo "Erro: Este ID de saída já está cadastrado.";
} else {
    // Verifica se a quantidade de saída, valor total, nome local e observação foram preenchidos
    if ($qtd_saida === null || $qtd_saida <= 0) {
        echo "Erro: A quantidade de saída deve ser um número positivo.";
    } elseif ($valor_total === null || $valor_total <= 0) {
        echo "Erro: O valor total deve ser um número positivo.";
    } elseif (empty($nome_local)) {
        echo "Erro: O nome do local não pode ser vazio.";
    } elseif (empty($observacao)) {
        echo "Erro: A observação não pode ser vazia.";
    } else {
        // SQL para inserir os dados na tabela "saida"
        $sql = "INSERT INTO saida (id_saida, imagem, id_usuario, nome_usuario, cod_produto, nome_produto, id_local, preco_custo, nome_local, id_estoque, qtd_saida, valor_total, observacao, data_saida) 
                VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11, $12, $13, $14)";
        $result_insert = pg_prepare($conexao, "insert_saida", $sql);

        // Passa as variáveis para a função
        $result_insert = pg_execute($conexao, "insert_saida", array($id_saida, $imagemBinaria, $id_usuario, $nome_usuario, $cod_produto, $nome_produto, $id_local, $preco_custo, $nome_local, $id_estoque, $qtd_saida, $valor_total, $observacao, $data_saida));

        // Verifica se a inserção foi bem-sucedida
        if ($result_insert) {
            echo "Saída registrada com sucesso!";
        } else {
            echo "Erro ao inserir dados: " . pg_last_error($conexao);
        }
    }
}

// Fecha a conexão
pg_close($conexao);
?>
