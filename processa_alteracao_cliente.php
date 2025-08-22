<?php
session_start();
require 'conexao.php';

// Verifica se o usuario é ADM, se não for da acesso negado.
if ($_SESSION['perfil'] != 1) {
    echo "<script>alert('Acesso Negado!');window.location.href='principal.php';</script>";
    exit();
}
// Pega por $_POST
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $id_cliente = $_POST['id_cliente'];
    $nome_cliente = $_POST['nome_cliente'];
    $endereco = $_POST['endereco'];
    $telefone = $_POST['telefone'];
    $email = $_POST['email'];
    $id_funcionario_responsavel = $_POST['id_funcionario_responsavel'];

    // Atualiza os dados do cliente
    $sql = "UPDATE cliente 
            SET nome_cliente = :nome_cliente, 
                endereco = :endereco, 
                telefone = :telefone, 
                email = :email, 
                id_funcionario_responsavel = :id_funcionario_responsavel
            WHERE id_cliente = :id";

    // PROTEGE E ENCAPSULA SQL PARA EVITAR sqlInjection
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nome_cliente', $nome_cliente);
    $stmt->bindParam(':endereco', $endereco);
    $stmt->bindParam(':telefone', $telefone);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':id_funcionario_responsavel', $id_funcionario_responsavel);
    $stmt->bindParam(':id', $id_cliente);

    if ($stmt->execute()) {
        echo "<script>alert('Cliente atualizado com sucesso!');window.location.href='buscar_cliente.php';</script>";
    } else {
        echo "<script>alert('Erro ao atualizar cliente!');window.location.href='alterar_cliente.php?id=$id_cliente';</script>";
    }
}
?>
