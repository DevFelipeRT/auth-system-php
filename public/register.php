<?php

    require_once '../includes/sessions.php';
    require_once '../includes/functions.php';

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
        <title>Crie sua conta.</title>
        <link rel="stylesheet" href="assets/css/style.css" />
        <link
          href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css"
          rel="stylesheet"
        />
    </head>
    <body>
        <section>
            <div class="wrapper">
                <form action="../src/register_submit.php" method="post">
                    <h1>Crie sua conta</h1>

                    <!-- Nome de Usuário -->
                    <div class="input-box">
                        <input
                          type="text"
                          name="text_username"
                          id="text_username"
                          placeholder="Nome de Usuário"
                          value="<?= showValue($registrationData, 'text_username') ?>"
                        />
                        <i class="bx bxs-user"></i>
                    </div>
                    <?= showMessage($registrationData, 'text_username') ?>

                    <!-- Senha -->
                    <div class="input-box">
                        <input
                          type="password"
                          name="text_password"
                          id="text_password"
                          placeholder="Senha"
                          value="<?= showValue($registrationData, 'text_password') ?>"
                        />
                        <i class="bx bx-key"></i>
                    </div>
                    <?= showMessage($registrationData, 'text_password') ?>

                    <!-- Nome -->
                    <div class="input-box">
                        <input
                          type="text"
                          name="text_first_name"
                          id="text_first_name"
                          placeholder="Nome"
                          value="<?= showValue($registrationData, 'text_first_name') ?>"
                        />
                    </div>
                    <?= showMessage($registrationData, 'text_first_name') ?>

                    <!-- Sobrenome -->
                    <div class="input-box">
                        <input
                          type="text"
                          name="text_last_name"
                          id="text_last_name"
                          placeholder="Sobrenome"
                          value="<?= showValue($registrationData, 'text_last_name') ?>"
                        />
                    </div>
                    <?= showMessage($registrationData, 'text_last_name') ?>

                    <!-- E-mail -->
                    <div class="input-box">
                        <input
                          type="email"
                          name="text_email"
                          id="id_email"
                          placeholder="Email"
                          value="<?= showValue($registrationData, 'text_email') ?>"
                        />
                        <i class="bx bxs-envelope"></i>
                    </div>
                    <?= showMessage($registrationData, 'text_email') ?>

                    <div class="auth-link">
                        <small><a href="index.php">Ja tem uma conta? Faça login.</a></small>
                    </div>

                    <input type="submit" value="Cadastre-se" class="btn" />
                    <?= showMessage($registrationData, 'register') ?>
                </form>
            </div>
        </section>
    </body>
</html>