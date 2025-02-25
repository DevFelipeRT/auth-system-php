<?php

    require_once '../includes/sessions.php';
    require_once '../includes/functions.php';
    require_once '../config/database.php';
    
    // Verifica se a requisição não é POST
    if($_SERVER['REQUEST_METHOD'] != 'POST') {
        header('Location: ../public/dashboard.php');
        return;
    }

    $loginData = [];

    // Validação de Username
    $loginData['text_username'] = [
        'value' => '',
        'message' => '',
        'type' => ''
    ];

    if (isset($_POST['text_username'])) {
        $loginData['text_username'] = validateUsername($_POST['text_username']);
    }


    // Validação de Senha
    $loginData['text_password'] = [
        'value' => '',
        'message' => '',
        'type' => ''
    ];

    if (isset($_POST['text_password'])) {
        $loginData['text_password'] = validatePassword($_POST['text_password']);
    }
    

    // Verifica se existem mensagens
    if (hasMessages($loginData)) {
        $_SESSION['loginData'] = $loginData;
        header('Location: ../public/index.php');
        exit();
    }

    // Validação com o Banco de Dados
    $loginData['login'] = [
        'value' => '',
        'message' => '',
        'type' => ''
    ];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST['text_username']);
        $password = $_POST['text_password'];

        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $username]);
            $user = $stmt->fetch();

            if ($user) {
                // Verificar tentativas de login
                if ($user['failed_login_attempts'] >= 5 && strtotime($user['last_failed_login']) > time() - 1800) {
                    $loginData['login']['message'] = "Conta bloqueada temporariamente. Tente novamente mais tarde.";
                    $loginData['login']['type'] = 'error';
                    die();
                }

                // Verificação de senha
                if (password_verify($password, $user['password_hash'])) {
                    // Resetar tentativas falhas
                    $stmt = $pdo->prepare("UPDATE users SET failed_login_attempts = 0 WHERE id = ?");
                    $stmt->execute([$user['id']]);

                    // Regenerar ID da sessão
                    session_regenerate_id(true);            
                    
                    $_SESSION['loggedin'] = true;                
                    $_SESSION['user_id'] = $user['id']; 
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['first_name'] = $user['first_name'];
                    $_SESSION['last_name'] = $user['last_name'];
                    
                    header("Location: ../public/dashboard.php");
                    exit();
                } else {
                    // Registrar tentativa falha
                    $stmt = $pdo->prepare("UPDATE users SET failed_login_attempts = failed_login_attempts + 1, last_failed_login = NOW() WHERE id = ?");
                    $stmt->execute([$user['id']]);
                    $loginData['login']['message'] = "Credenciais inválidas";
                    $loginData['login']['type'] = 'error';
                }
            } else {
                $loginData['login']['message'] = "Credenciais inválidas.";
                $loginData['login']['type'] = 'error';
            }
        } catch (PDOException $e) {
            // Registrar o erro em um log privado
            $logFile = __DIR__ . '/../logs/error_log.txt';
            $errorMessage = date('Y-m-d H:i:s') . " - Erro ao processar login: " . $e->getMessage() . "\n";
            error_log($errorMessage, 3, $logFile);

            // Definir mensagem genérica para o usuário
            $loginData['login']['message'] = 'Erro ao processar login. Tente novamente mais tarde.';
            $loginData['login']['type'] = 'error';

            // Redirecionar para a página de login
            $_SESSION['loginData'] = $loginData;
            header('Location: ../public/index.php');
            exit();
        }

        // Verifica se existem mensagens
        if (hasMessages($loginData)) {
            $_SESSION['loginData'] = $loginData;
            header('Location: ../public/index.php');
            exit();
        }
    }
    
?>

