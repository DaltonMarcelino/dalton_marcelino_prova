<?php 
    session_start();
    require_once 'conexao.php';

     // Verifica se o USUARIO tem permissão de CLIENTE
     if($_SESSION['perfil']!=1 && $_SESSION['perfil']!=4){
        echo "<script>alert('Acesso Negado!'); window.location.href='principal.php';</script>";
        exit();
    }

    // Inicializa variaveis
    $usuario = null;

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if(!empty($_POST['busca_cliente'])){
            $busca = trim($_POST['busca_cliente']);

            // Verifica se a busca é um numero ou um nome
            if(is_numeric($busca)){
                $sql = "SELECT * FROM cliente WHERE id_cliente = :busca";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':busca',$busca,PDO::PARAM_INT);
            } else {
                $sql = "SELECT * FROM cliente WHERE nome_cliente LIKE :busca_nome";
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
    <h2>Alterar Cliente</h2>

    <form action="alterar_cliente.php"  method="POST">
        <label for="busca_cliente">Digite o id ou nome do usuario</label>
        <input type="text" id="busca_cliente" name="busca_cliente" required onkeyup="buscarSugestoes()" required>
 
        <!-- Div para exibir susgestões para usuarios -->
        <div id="sugestoes"></div>
        <button type="submit">Buscar</button><br>
        <a href="principal.php" class="btn btn-primary">Voltar</a>

    </form>

    <?php if($usuario): ?>
        <!-- Formulario para alterar usuario -->
        <form action="processa_alteracao_cliente.php" method="POST">
            <input type="hidden" name="id_cliente" value="<?=htmlspecialchars($usuario['id_cliente'])?>">

            <label for="nome_cliente">Nome:</label>
            <input type="text" id="nome_cliente" name="nome_cliente" value="<?=htmlspecialchars($usuario['nome_cliente'])?>" oninput="this.value=this.value.replace(/[^a-zA-ZÀ-ÿ\s]/g,'')" required>

            <label for="endereco">Endereço:</label>
            <input type="text" id="endereco" name="endereco" value="<?=htmlspecialchars($usuario['endereco'])?>" maxlength="254" required>

            <label for="telefone">Telefone:</label>
            <input type="text" id="telefone" name="telefone" value="<?=htmlspecialchars($usuario['telefone'])?>" oninput="this.value=this.value.replace(/\D/g,'').replace(/(\d{2})(\d{5})(\d{4})/,'($1) $2-$3')" 
            class="form-control" pattern="\(\d{2}\)\s\d{5}-\d{4}" title="Digite no mínimo 11 números" maxlength="15" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?=htmlspecialchars($usuario['email'])?>" required>

            <!-- Se o usuario logado for CLIENTE, exibir opção de alterar senha -->
            <?php if($_SESSION['perfil'] == 4): ?>
                <label for="nova_senha">Nova senha</label>
                <input type="password" id="nova_senha" name="nova_senha" minlength="3" maxlength="15" required title="A senha deve ter no mínimo 3 e no máximo 15 caracteres">
            <?php endif; ?>

            <button type="submit">Alterar</button>
            <button type="reset">Cancelar</button>
        </form>
    <?php endif;?>

<script>
    // Guardar valores originais
    const originalValues = {};
    window.onload = () => {
        originalValues.nome = document.getElementById("nome_cliente").value;
        originalValues.endereco = document.getElementById("endereco").value;
        originalValues.telefone = document.getElementById("telefone").value;
        originalValues.email = document.getElementById("email").value;
    };
    // Pega por ID e joga dentro de uma variavel
    function validarCliente() {
        const nome = document.getElementById("nome_cliente").value;
        const endereco = document.getElementById("endereco").value;
        const telefone = document.getElementById("telefone").value;
        const email = document.getElementById("email").value;
        const mensagem = document.getElementById("mensagem");

        // Se não houver nenhuma alteração manda uma mensagem
        if (nome === originalValues.nome &&
            endereco === originalValues.endereco &&
            telefone === originalValues.telefone &&
            email === originalValues.email) {
            mensagem.innerHTML = "⚠ Nenhuma alteração feita";
            return false; // Bloqueia envio
        }

        // Se houve alteração, limpa mensagem e envia
        mensagem.innerHTML = "";
        return true;
    }
</script>

    <center>
        <address><em>Dalton Marcelino / Tecnico em Desenvolvimento de Sistemas / DESN20242V1</em></address>
    </center>
</body>
</html>