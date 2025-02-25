<?php 

    /**
     * Formats and returns an HTML message based on the provided field data.
     *
     * @param array $data The data array containing messages and their types.
     * @param string $field The field to check for a message.
     * @return string Returns an HTML span element with the formatted message or an empty string if no message is set.
     */
    function showMessage(array $data, string $field): string {
        // Check if the field has a message
        if (!isset($data[$field]['message']) || empty($data[$field]['message'])) {
            return '';
        }

        // Define class based on the message type
        $messageClass = match ($data[$field]['type'] ?? 'info') {
            'error' => 'text-danger',   // Error messages get the 'text-danger' class
            'success' => 'text-success', // Success messages get the 'text-success' class
            default => 'text-info',      // Default class for informational messages
        };

        // Format and return the message as an HTML span element
        return sprintf(
            '<span class="%s"><small>%s</small></span>',
            $messageClass,
            htmlspecialchars($data[$field]['message'], ENT_QUOTES, 'UTF-8')
        );
    }


    /**
     * Checks if there is any error or success message in the provided data.
     *
     * @param array $data Validation data, with messages associated with each field.
     * @return bool Returns true if any field contains a message, otherwise false.
     */
    function hasMessages(array $data): bool {
        foreach ($data as $fieldData) {
            // Check if the field has a non-empty message
            if (!empty($fieldData['message'])) {
                return true;
            }
        }
        return false;
    }

    /**
     * Returns the value entered by the user for the specified field.
     *
     * @param array $inputs The array of input values.
     * @param string $field The name of the field whose value is to be returned.
     * @return string The field value, or an empty string if not defined or is empty.
     */
    function showValue(array $inputs, string $field): string {
        // Check if the field exists in the inputs array and if the value is not empty
        if (isset($inputs[$field]) && !empty($inputs[$field]['value'])) {
            // Return the value escaped to prevent XSS vulnerabilities
            return htmlspecialchars($inputs[$field]['value'], ENT_QUOTES, 'UTF-8');
        }
        
        // Return an empty string if the field doesn't exist or is empty
        return '';
    }

    /**
     * Validates the password based on certain rules.
     *
     * @param string $password The password to be validated.
     * @return array An array containing the password value, error message (if any), and message type.
     */
    function validatePassword(string $password): array {
        // Initialize the result array
        $result = [
            'value' => $password,
            'message' => '',
            'type' => ''
        ];

        // Check if the password field is empty
        if (empty($password)) {
            $result['message'] = 'O preenchimento de "Senha" é obrigatório.';
            $result['type'] = 'error';
        }
        // Check if the password length is less than 8 characters
        elseif (strlen($password) < 8) {
            $result['message'] = 'Sua senha deve conter no mínimo 8 caracteres.';
            $result['type'] = 'error';
        }

        return $result;
    }

    /**
     * Function to validate the username input.
     *
     * @param string $username The username to be validated.
     * @return array An array containing the validation result:
     *               - 'value' => the username
     *               - 'message' => a validation message
     *               - 'type' => type of message (e.g., 'error')
     */
    function validateUsername(string $username): array {
        // Initialize the result array with default values
        $result = [
            'value' => $username,
            'message' => '',
            'type' => ''
        ];

        // Trim the username to remove unnecessary spaces
        $username = trim($username);

        // Check if the username is empty
        if (empty($username)) {
            $result['message'] = 'O preenchimento de "Nome de Usuário" é obrigatório.';
            $result['type'] = 'error';
        }
        // Check if it is a valid email format
        elseif (filter_var($username, FILTER_VALIDATE_EMAIL)) {
            return $result;
        }
        // Check if the username length is between 6 and 24 characters
        elseif (strlen($username) < 6 || strlen($username) > 24) {
            $result['message'] = 'Seu nome de usuário deve conter entre 6 e 24 caracteres.';
            $result['type'] = 'error';
        }
        // Check if the username contains only allowed characters (letters, numbers, underscore)
        elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            $result['message'] = 'O nome de usuário só pode conter letras, números e sublinhados.';
            $result['type'] = 'error';
        }

        // Return the result with validation outcome
        return $result;
    }
    
    /**
     * Function to check if the user is logged in.
     * 
     * @return array The login data or error information if the user is not logged in.
     */
    function checkUserLogin(): array {
        // Initialize an empty array for login data
        $loginData = [
            'loggedin'   => false,
            'user_id'    => null,
            'username'   => '',
            'email'      => '',
            'first_name' => '',
            'last_name'  => ''
        ];

        // Check if the session has been started and the user is logged in
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Check if the user is logged in
        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
            // Sanitize session data before using it
            $loginData = [
                'loggedin'   => $_SESSION['loggedin'],
                'user_id'    => (int) $_SESSION['user_id'],  // Cast to integer for security
                'username'   => htmlspecialchars($_SESSION['username']), // Escape to prevent XSS
                'email'      => filter_var($_SESSION['email'], FILTER_VALIDATE_EMAIL), // Validate email format
                'first_name' => htmlspecialchars($_SESSION['first_name']), // Escape to prevent XSS
                'last_name'  => htmlspecialchars($_SESSION['last_name']) // Escape to prevent XSS
            ];
        } else {
            // If the user is not logged in, set an error message
            $loginData['login'] = [
                'value' => '',
                'message' => 'Você não está logado!',
                'type' => 'error'
            ];

            // Store login data in session to display error on the login page
            $_SESSION['loginData'] = $loginData;

            // // Redirect to the login page
            // header('Location: ../public/index.php');
            // exit();
        }

        // Return the login data
        return $loginData;
    }

?>