<?php
    session_start();
    require_once 'conexao.php';

    // VERIFICA SE O USUARIO TEM PERMISSÃO CLIENTE
    if($_SESSION['perfil']!=4 && $_SESSION['perfil']!=1){
        echo "<script>alert('Acesso negado!');window.location.href='principal.php';</script>";
        exit();
    } 
    $usuario = []; // INICIALIZA A VARIAVEL PARA EVITAR ERROS

    // SE O FORMULARIO FOR ENVIADO, BUSCA O PRODUTO PELO ID OU NOME
    if($_SERVER["REQUEST_METHOD"]== "POST" && !empty($_POST['busca'])){
        $busca = trim($_POST['busca']);

        // VERIFICA SE A BUSCA É UM NUMERO OU UM NOME.
        if(is_numeric($busca)){
            $sql="SELECT * FROM produto WHERE id_produto = :busca ORDER BY nome_prod ASC";

            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':busca',$busca, PDO::PARAM_INT);
        } else {
            $sql="SELECT * FROM produto WHERE nome_prod LIKE :busca_nome ORDER BY nome_prod ASC";

            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':busca_nome',"%$busca%", PDO::PARAM_STR);
        }
    } else {
        $sql="SELECT * FROM produto ORDER BY nome_prod ASC";
        $stmt = $pdo->prepare($sql);
    }

    // Obtendo o nome do perfil do usuário logado
    $id_perfil = $_SESSION['perfil'];
    $sqlPerfil = "SELECT nome_perfil FROM perfil WHERE id_perfil = :id_perfil";
    $stmtPerfil = $pdo->prepare($sqlPerfil);
    $stmtPerfil->bindParam(':id_perfil',$id_perfil);
    $stmtPerfil->execute();
    $perfil = $stmtPerfil->fetch(PDO::FETCH_ASSOC);
    $nomePerfil = $perfil['nome_perfil'];
    
    // Definição das permissões por perfil
    $permissoes = [
        1 => ["Cadastrar" => ["cadastro_usuario.php", "cadastro_perfil.php", "cadastro_cliente.php",
                               "cadastro_fornecedor.php", "cadastro_produto.php", "cadastro_funcionario.php"],

              "Buscar" => ["buscar_usuario.php", "buscar_perfil.php", "buscar_cliente.php",
                           "buscar_fornecedor.php", "buscar_produto.php", "buscar_funcionario.php"],

              "Alterar" => ["alterar_usuario.php", "alterar_perfil.php", "alterar_cliente.php",
                           "alterar_fornecedor.php", "alterar_produto.php", "alterar_funcionario.php"],

              "Excluir" => ["excluir_usuario.php", "excluir_perfil.php", "excluir_cliente.php",
                           "excluir_fornecedor.php", "excluir_produto.php", "excluir_funcionario.php"]],


        2 => ["Cadastrar" => ["cadastro_cliente.php"],

              "Buscar" => ["buscar_cliente.php", "buscar_fornecedor.php", "buscar_produto.php"],

              "Alterar" => ["alterar_fornecedor.php", "alterar_produto.php"],

              "Excluir" => ["excluir_produto.php"]],


        3 => ["Cadastrar" => ["cadastro_fornecedor.php", "cadastro_produto.php"],

              "Buscar" => ["buscar_cliente.php", "buscar_fornecedor.php", "buscar_produto.php"],

              "Alterar" => ["alterar_fornecedor.php", "alterar_produto.php"],

              "Excluir" => ["excluir_produto.php"]],


        4 => ["Cadastrar" => ["cadastro_cliente.php"],

              "Buscar" => ["buscar_produto.php"]]
    ];

    // Obtendo as opções disponíveis para o perfil logado
    $opcoes_menu = $permissoes["$id_perfil"];

$stmt->execute();
$usuarios = $stmt->fetchALL(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <title>Buscar Usuario</title>
</head>
<body>
    <!-- Header  -->
    <nav>
        <ul class="menu">
            <?php foreach($opcoes_menu as $categoria =>$arquivos): ?>
                <li class="dropdown">
                    <a href="#"><?=$categoria ?></a>
                    <ul class="dropdown-menu">
                        <?php foreach($arquivos as $arquivo): ?>
                            <li>
                                <a href="<?=$arquivo ?>"><?=ucfirst(str_replace("_"," ",basename($arquivo,".php")))?></a>
                            </li>
                            <?php endforeach;?>
                    </ul>
                </li>
                <?php endforeach;?>
        </ul>
    </nav>
    <br>

    <h2>Lista de Produto</h2>
    
    <!-- Forms para buscar o ID ou NOME do produto -->
    <form action="buscar_produto.php" method="POST">
        <label for="busca">Digite o ID ou NOME</label>
        <input type="text" id="busca" name="busca">
        <button type="submit">Pesquisar</button><br>
        <a href="principal.php" class="btn btn-primary">Voltar</a>

    </form>
        
        <!-- Tabela de consulta de PRODUTOS-->
        <?php if(!empty($usuarios)): ?>
            <table class="table table-striped table-hover table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nome do Produto</th>
                        <th>Descrição</th>
                        <th>Quantidade</th>
                        <th>Valor unitario</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($usuarios as $usuario): ?>
                        <tr>
                            <td><?=htmlspecialchars($usuario['id_produto'])?></td>
                            <td><?=htmlspecialchars($usuario['nome_prod'])?></td>
                            <td><?=htmlspecialchars($usuario['descricao'])?></td>
                            <td><?=htmlspecialchars($usuario['qtde'])?></td>
                            <td><?=htmlspecialchars($usuario['valor_unit'])?></td>
                        </tr>
                    <?php endforeach;?>
                </tbody>
            </table>

            <?php else:?>
                <p>Nenhum Produto encontrado encontrado.</p>
            <?php endif;?>

    <center>
        <address><em>Dalton Marcelino / Tecnico em Desenvolvimento de Sistemas / DESN20242V1</em></address>
    </center>
</body>
</html>