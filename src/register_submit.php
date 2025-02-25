<?php 

    require_once '../includes/sessions.php';
    require_once '../includes/functions.php';
    require_once '../config/database.php';

    // Verifica se a requisição não é POST
    if ($_SERVER['REQUEST_METHOD'] != 'POST') {
        header('Location: ../public/register.php');
        exit();
    }

    $registrationData = [];

    // Validação de Username
    $registrationData['text_username'] = [
        'value' => '',
        'message' => '',
        'type' => ''
    ];

    if (isset($_POST['text_username'])) {
        $registrationData['text_username'] = validateUsername($_POST['text_username']);
    }

    // Validação de Senha
    $registrationData['text_password'] = [
        'value' => '',
        'message' => '',
        'type' => ''
    ];

    if (isset($_POST['text_password'])) {
        $registrationData['text_password'] = validatePassword($_POST['text_password']);
    }

    // Validação de Nome
    $registrationData['text_first_name'] = [
        'value' => '',
        'message' => '',
        'type' => ''
    ];

    if (empty($_POST['text_first_name'])) {
        $registrationData['text_first_name']['message'] = 'O preenchimento de "Nome" é obrigatório.';
        $registrationData['text_first_name']['type'] = 'error';
    } else {
        $registrationData['text_first_name']['value'] = $_POST['text_first_name'];
    }

    // Validação de Sobrenome
    $registrationData['text_last_name'] = [
        'value' => '',
        'message' => '',
        'type' => ''
    ];

    if (empty($_POST['text_last_name'])) {
        $registrationData['text_last_name']['message'] = 'O preenchimento de "Sobrenome" é obrigatório.';
        $registrationData['text_last_name']['type'] = 'error';
    } else {
        $registrationData['text_last_name']['value'] = $_POST['text_last_name'];
    }

    // Validação de Email
    $registrationData['text_email'] = [
        'value' => '',
        'message' => '',
        'type' => ''
    ];

    if (empty($_POST['text_email'])) {
        $registrationData['text_email']['message'] = 'O preenchimento de "Email" é obrigatório.';
        $registrationData['text_email']['type'] = 'error';
    } else {
        $registrationData['text_email']['value'] = $_POST['text_email'];

        // Validando o formato do e-mail
        if (!filter_var($_POST['text_email'], FILTER_VALIDATE_EMAIL)) {
            $registrationData['text_email']['message'] = 'O e-mail informado é inválido.';
            $registrationData['text_email']['type'] = 'error';
        }
    }


    // Verifica se existem mensagens
    if (hasMessages($registrationData)) {
        $_SESSION['registrationData'] = $registrationData;
        header('Location: ../public/register.php');
        exit();
    }

    // Acesso ao Banco de Dados
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        // Filtrando e sanitizando os dados de entrada com métodos adequados
        $username = filter_input(INPUT_POST, 'text_username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $email = filter_input(INPUT_POST, 'text_email', FILTER_SANITIZE_EMAIL);
        $password = $_POST['text_password'];
        $firstName = filter_input(INPUT_POST, 'text_first_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $lastName = filter_input(INPUT_POST, 'text_last_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    
        // Verificando se os dados são válidos
        if (empty($username) || empty($email) || empty($password) || empty($firstName) || empty($lastName)) {
            $registrationData['register']['message'] = 'Preencha todos os campos.';
            $registrationData['register']['type'] = 'error';
            
            $_SESSION['registrationData'] = $registrationData;

            header('Location: ../public/register.php');
            exit();
        }

        // Verificar se o e-mail já existe
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $emailExists = $stmt->fetchColumn() > 0;

        // Verificar se o username já existe
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $usernameExists = $stmt->fetchColumn() > 0;

        if ($emailExists || $usernameExists) {
            if ($emailExists && $usernameExists) {
                $registrationData['register']['message'] = 'Nome de usuário e e-mail já cadastrados.';
            } elseif ($emailExists) {
                $registrationData['register']['message'] = 'E-mail já cadastrado.';
            } else {
                $registrationData['register']['message'] = 'Nome de usuário já cadastrado.';
            }

            $registrationData['register']['type'] = 'error';
            $_SESSION['registrationData'] = $registrationData;
            header('Location: ../public/register.php');
            exit();
        }
    
        // Criptografando a senha
        $pwHash = password_hash($password, PASSWORD_DEFAULT);
    
        // Iniciar a transação
        $pdo->beginTransaction();
    
        try {
            // Preparar e executar a consulta SQL
            $stmt = $pdo->prepare(
                "INSERT INTO users (username, email, password_hash, first_name, last_name) VALUES (?, ?, ?, ?, ?)"
            );
            $stmt->execute([$username, $email, $pwHash, $firstName, $lastName]);
    
            // Validar se a inserção foi bem-sucedida antes de fazer o commit
            if ($stmt->rowCount() > 0) {
                // Confirmar a transação
                $pdo->commit();
    
                // Definir mensagem de sucesso
                $registrationData['register']['message'] = 'Usuário cadastrado com sucesso!';
                $registrationData['register']['type'] = 'success';
    
                // Redirecionar para a página inicial em caso de sucesso
                $_SESSION['registrationData'] = $registrationData;
                header('Location: ../public/index.php');
                exit();
            } else {
                // Caso a inserção não tenha ocorrido, desfaz a transação
                $pdo->rollBack();
    
                // Definir mensagem de erro
                $registrationData['register']['message'] = 'Erro ao cadastrar usuário.';
                $registrationData['register']['type'] = 'error';
    
                // Redirecionar para a página de registro
                $_SESSION['registrationData'] = $registrationData;
                header('Location: ../public/register.php');
                exit();
            }
    
        } catch (Exception $e) {
            // Desfazer a transação em caso de erro
            $pdo->rollBack();

            // Registrar o erro em um log privado
            $logFile = __DIR__ . '/../logs/error_log.txt';
            $errorMessage = date('Y-m-d H:i:s') . " - Erro no cadastro: ". $e->getMessage() . "\n";
            error_log($errorMessage, 3, $logFile);

            // Definir mensagem genérica para o usuário
            $registrationData['register']['message'] = 'Erro ao cadastrar usuário. Tente novamente mais tarde.';
            $registrationData['register']['type'] = 'error';

            // Redirecionar para a página de registro
            $_SESSION['registrationData'] = $registrationData;
            header('Location: ../public/register.php');
            exit();
        }
    }
    
?>