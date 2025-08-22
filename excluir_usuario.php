<?php
session_start();
    require 'conexao.php';

    // VERIFICA SE O USUARIO TEM PERMISSÃO DE ADM OU SECRETARIA
    if($_SESSION['perfil']!=1){
        echo "<script>alert('Acesso negado!');window.location.href='principal.php';</script>";
        exit();
    } 
    
    // Inicializa variavel para armazenar usuarios
    $usuarios = [];

    // Buscar todos os usuarios cadastrados em ordem alfabetica
    $sql="SELECT * FROM usuario ORDER BY nome ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $usuarios = $stmt->fetchALL(PDO::FETCH_ASSOC);
        
    // Se um id for passado via GET exclui o usuario
    if(isset($_GET['id']) && is_numeric($_GET['id'])){
        $id_usuario = $_GET['id'];

        // Exclui o usuario do banco de dados
        $sql = "DELETE FROM usuario WHERE id_usuario = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id',$id_usuario,PDO::PARAM_INT);

        if($stmt->execute()){
            echo "<script>alert('Usuario excluido com sucesso!');window.location.href='excluir_usuario.php';</script>";
        } else{
            echo "<script>alert('Erro ao excluir o usuario!');</script>";
        }
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

              "Buscar" => ["buscar_produto.php"],

              "Alterar" => ["alterar_cliente.php"]],
    ];

    // Obtendo as opções disponíveis para o perfil logado
    $opcoes_menu = $permissoes["$id_perfil"];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <title>Excluir Usuario</title>
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

    <h2>Excluir Usuario</h2><br>
    <?php if(!empty($usuarios)): ?>
        <!-- Tabela com Bootstrap 5 -->
<div class="table-responsive">
    <table class="table table-bordered table-hover table-striped align-middle">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Perfil</th>
                <th>Email</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>

            <!-- TABELA DE EXCLUIR USUARIOS -->
            <?php foreach($usuarios as $usuario): ?>
                <tr>
                    <td><?= htmlspecialchars($usuario['id_usuario'])?></td>
                    <td><?= htmlspecialchars($usuario['nome'])?></td>
                    <td><?= htmlspecialchars($usuario['id_perfil'])?></td>
                    <td><?= htmlspecialchars($usuario['email'])?></td>
                    <td>
                        <a href="excluir_usuario.php?id=<?= htmlspecialchars($usuario['id_usuario'])?>" 
                           class="btn btn-sm btn-danger"
                           onclick="return confirm('Tem certeza que deseja excluir este usuário?')">Excluir</a>
                    </td>
                </tr>
            <?php endforeach;?>
        </tbody>
    </table>
</div>

        <?php else: ?>
            <p>Nenhum usuário encontrado</p>
    <?php endif; ?>

    <a href="principal.php" class="btn btn-primary">Voltar</a>
    <br><br>
    
    <center>
        <address><em>Dalton Marcelino / Tecnico em Desenvolvimento de Sistemas / DESN20242V1</em></address>
    </center>

        
</body>
</html>