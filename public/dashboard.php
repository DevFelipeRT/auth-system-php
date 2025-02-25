<?php 

    require_once '../includes/sessions.php';
    require_once '../includes/functions.php';

    $loginData = [];

    // Verifica se o usuário está logado
    $loginData = checkUserLogin();

    // Se o usuário não estiver logado, redireciona para a página de login
    if (!$loginData['loggedin']) {
        header("Location: ../public/index.php");
        exit();
    }
    
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
</head>
<body>
    <?php 
        echo '<h1>Bem-Vindo <strong>' . $loginData['first_name'] . ' ' . $loginData['last_name'] . '.</strong></h1><br>';
    ?>
    <a href="../src/logout_submit.php">Logout</a>
</body>
</html>