<?php

    require_once '../includes/sessions.php';

    // Destruir sessão
    $_SESSION = array();
    session_destroy();

    // Destruir cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    header("Location: ../public/index.php");
    exit();

?>
