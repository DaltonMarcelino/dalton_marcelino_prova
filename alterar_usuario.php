<?php 
    session_start();
    require_once 'conexao.php';

     // Verifica se o USUARIO tem permissão de ADM
     if($_SESSION['perfil'] !=1){
        echo "<script>alert('Acesso Negado!'); window.location.href='principal.php';</script>";
        exit();
    }

    // Inicializa variaveis
    $usuario = null;

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if(!empty($_POST['busca_usuario'])){
            $busca = trim($_POST['busca_usuario']);

            // Verifica se a busca é um numero ou um nome
            if(is_numeric($busca)){
                $sql = "SELECT * FROM usuario WHERE id_usuario = :busca";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':busca',$busca,PDO::PARAM_INT);
            } else {
                $sql = "SELECT * FROM usuario WHERE nome LIKE :busca_nome";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':busca_nome',"%$busca%",PDO::PARAM_STR);
            }

            $stmt->execute();
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            // Se o usuario não for encontrado, exibe um alerta
            if(!$usuario){
                echo "<script>alert('Usuario não encontrado!');</script>";
            }
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
 
               "Buscar" => ["buscar_produto.php"]],
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
    <title>Alterar usuario</title>
    <!-- Certifique-se de que o javascript esteja carregando corretamente -->
    <script src="script.js"></script>
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
    <h2>Alterar usuario</h2>
    
    <!-- Forms para buscar o ID ou NOME do usuario -->
    <form action="alterar_usuario.php"  method="POST">
        <label for="busca_usuario">Digite o id ou nome do usuario</label>
        <input type="text" id="busca_usuario" name="busca_usuario" required onkeyup="buscarSugestoes()">
 
        <!-- Div para exibir susgestões para usuarios -->
        <div id="sugestoes"></div>
        <button type="submit">Buscar</button><br>
        <a href="principal.php" class="btn btn-primary">Voltar</a>

    </form>

    <?php if($usuario): ?>
        <!-- Formulario para alterar usuario -->
        <form action="processa_alteracao_usuario.php" method="POST">
            <input type="hidden" name="id_usuario" value="<?=htmlspecialchars($usuario['id_usuario'])?>">

            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" value="<?=htmlspecialchars($usuario['nome'])?>" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?=htmlspecialchars($usuario['email'])?>" required>

            <label for="id_perfil">Perfil:</label>
            <select name="id_perfil" id="id_perfil">
                <option value="1" <?=$usuario['id_perfil'] == 1 ?'select':''?>>Administrado</option>
                <option value="2" <?=$usuario['id_perfil'] == 2 ?'select':''?>>Secretaria</option>
                <option value="3" <?=$usuario['id_perfil'] == 3 ?'select':''?>>Almoxarife</option>
                <option value="4" <?=$usuario['id_perfil'] == 4 ?'select':''?>>Cliente</option>
            </select>

            <!-- Se o usuario logado for ADM, exibir opção de alterar senha -->
            <?php if($_SESSION['perfil'] == 1): ?>
                <label for="nova_senha">Nova senha</label>
                <input type="password" id="nova_senha" name="nova_senha" minlength="3" maxlength="15" required title="A senha deve ter no mínimo 3 e no máximo 15 caracteres">
            <?php endif; ?>

            <button type="submit">Alterar</button>
            <button type="reset">Cancelar</button>
        </form>
    <?php endif;?>

    <center>
        <address><em>Dalton Marcelino / Tecnico em Desenvolvimento de Sistemas / DESN20242V1</em></address>
    </center>
</body>
</html>