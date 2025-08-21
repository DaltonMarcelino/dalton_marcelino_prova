<?php
    session_start();
    require_once 'conexao.php';

    // VERIFICA SE O USUARIO TEM PERMISSÃO
    // SUPONDO QUE O PERFIL 4 SEJA O CLIENTE E ADM

    if($_SESSION['perfil']!=1 && $_SESSION['perfil']!=4 ){
        echo "Acesso Negado!";
    }

    if($_SERVER['REQUEST_METHOD']== "POST"){
        $nome_cliente = $_POST['nome_cliente'];
        $endereco = $_POST['endereco'];
        $telefone = $_POST['telefone'];
        $email = $_POST['email'];
    
        $sql = "INSERT INTO cliente(nome_cliente, endereco, telefone, email, id_funcionario_responsavel) 
                VALUES (:nome_cliente, :endereco, :telefone, :email, :id_funcionario_responsavel)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nome_cliente',$nome_cliente);
        $stmt->bindParam(':endereco',$endereco);
        $stmt->bindParam(':telefone',$telefone);
        $stmt->bindParam(':email',$email);
        $stmt->bindParam(':id_funcionario_responsavel',$id_funcionario_responsavel);
    
        if($stmt->execute()){
            echo "<script>alert('Cliente cadastrado com sucesso!');</script>";
        }
        else{
            echo "<script>alert('Erro ao cadastrar cliente!');</script>";
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
    <link rel="stylesheet" href="styles.css">
    <link href="bootstrap.min.css" rel="stylesheet">
    <link src="validacoes.js" rel="text/javacript">
    <link rel="stylesheet" href="styles.css">
    
    <title>Cadastrar Usuario</title>
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
    <h2>Cadastrar Cliente</h2>
    <form action="cadastro_cliente.php" method="POST" onsubmit="return validarCliente()">
    <label for="nome_cliente">Nome:</label>
    <input type="text" id="nome_cliente" name="nome_cliente" oninput="this.value=this.value.replace(/[^a-zA-ZÀ-ÿ\s]/g,'')" required>

    <label for="endereco">Endereço:</label>
    <input type="text" id="endereco" name="endereco" required>

    <label for="telefone">Telefone:</label>
    <input type="text" id="telefone" name="telefone" oninput="this.value=this.value.replace(/\D/g,'').replace(/(\d{2})(\d{5})(\d{4})/,'($1) $2-$3')" maxlength="15" required>

    <label for="email">E-mail:</label>
    <input type="email" id="email" name="email" required>

    <button type="submit">Salvar</button>
    <button type="reset">Cancelar</button>
    <br>
    <a href="principal.php" class="btn btn-primary">Voltar</a>
</form>

    <center>
        <address><em>Dalton Marcelino / Tecnico em Desenvolvimento de Sistemas / DESN20242V1</em></address>
    </center>

</body>
</html>