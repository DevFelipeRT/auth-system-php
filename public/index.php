<?php

    require_once '../includes/sessions.php';
    require_once '../includes/functions.php';

    // Verifica se o usuário está logado
    if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
        $loginData = checkUserLogin();
        
        // Se o usuário estiver logado, redireciona para a Dashboard
        if ($loginData['loggedin']) {
            header("Location: ../public/dashboard.php");
            exit();
        }
    }

    // Recupera dados da sessão
    $loginData = [];

    if (isset($_SESSION['loginData'])){
        $loginData = $_SESSION['loginData'];
        unset($_SESSION['loginData']);
    }

    $registrationData = [];

    if (isset($_SESSION['registrationData'])){
        $registrationData = $_SESSION['registrationData'];
        unset($_SESSION['registrationData']);
    }

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Entre em sua conta.</title>
    <link rel="stylesheet" href="assets\css\style.css" />
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
</head>

<body>
    <section>
        <div class="wrapper">
            <form action="../src/login_submit.php" method="post" name="login">
                <h1>Entre em sua conta</h1>
                <div class="input-box">
                    <input type="text" name="text_username" id="text_username" placeholder="Nome de Usuário" value="<?= showValue($loginData, 'text_username') ?>" />
                    <i class="bx bxs-user"></i>
                    <?= showMessage($loginData, 'text_username') ?>
                </div>
                <div class="input-box">
                    <input type="password" name="text_password" id="id_password" placeholder="Senha" />
                    <i class="bx bx-key"></i>
                    <?= showMessage($loginData, 'text_password') ?>
                </div>
                <div class="auth-link">
                    <small><a href="register.php">Criar uma conta.</a></small>
                </div>
                <input type="submit" value="Entrar" class="btn" />
                <?= showMessage($loginData, 'login') ?>
                <?= showMessage($registrationData, 'register') ?>
            </form>
        </div>
    </section>
</body>

</html>