<?php
    // FINALIZA SESSÃO, DESTROI E REDICIONA PARA LOGIN
    session_start();
    session_destroy();
    header("Location: index.php");
    exit();
?>