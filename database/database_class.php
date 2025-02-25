<?php 

namespace App\Database;

use PDO;
use PDOException;
use stdClass;

class Database 
{
    // Properties
    private $_host;
    private $_database;
    private $_username;
    private $_password;
    private $_fetchMode;
    private $_pdoConnection;

    public function __construct($dbConfig, $fetchMode = 'object')
    {
        // Set database configurations
        $this->_host = $dbConfig['host'];
        $this->_database = $dbConfig['database'];
        $this->_username = $dbConfig['username'];
        $this->_password = $dbConfig['password'];

        //Set return type
        if(!empty($fetchMode) && $fetchMode == 'object'){
            $this->_fetchMode = PDO::FETCH_OBJ;
        } else {
            $this->_fetchMode = PDO::FETCH_ASSOC;
        }
    }

    // Error logging method
    private function _logError($error)
    {
        // Prepare log message with detailed information
        $logMessage = "[" . date("Y-m-d H:i:s") . "] "
                    . "Status: " . $error->status . ", "
                    . "Message: " . $error->message . ", "
                    . "SQL: " . $error->sql . ", "
                    . "Results: " . var_export($error->results, true) . ", "
                    . "Affected Rows: " . $error->affectedRows . ", "
                    . "Last Inserted ID: " . $error->lastInsertedId . "\n";

        // Log to file
        file_put_contents(__DIR__ . '/error_log.txt', $logMessage, FILE_APPEND);
    }

    // PDO Connection
    private function _pdoConnection()
    {
        try {
            // Check if we already have an active connection to avoid unnecessary reconnections
            if ($this->_pdoConnection) {
                return $this->_pdoConnection;
            }

            // Create the DSN string for MySQL connection
            $dsn = "mysql:host={$this->_host};dbname={$this->_database}";

            // Create a new PDO instance
            $connection = new PDO(
                $dsn, 
                $this->_username, 
                $this->_password, 
                array(PDO::ATTR_PERSISTENT => true)
            );

            // Define the fetch mode
            $connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, $this->_fetchMode);

            // Store the connection to avoid multiple connections
            $this->_pdoConnection = $connection;

            return $connection;
        } catch (PDOException $err){
            // Log the error with detailed information
            $errorResult = $this->_result('error', $err->getMessage(), 'Connection failed', null, 0, null);
            $this->_logError($errorResult);

            // Show a generic error message to the user
            echo "We're experiencing technical issues. Please try again later.";

            // Optionally, return null or rethrow the exception if needed
            return null;
        }
    }

    // Query method
    public function executeQuery($sql, $parameters = null)
    {
        $results = null;

        // Executes a query with results.
        try {
            // Attempt to establish a PDO connection using the private connection method
            $pdo = $this->_pdoConnection();

            // If the connection is successful, proceed with the query execution
            if ($pdo) {
                // Prepare the SQL statement using the PDO connection
                $stmt = $pdo->prepare($sql);

                // Check if there are any parameters to bind to the query
                if (!empty($parameters)) {
                    $stmt->execute($parameters);
                } else {
                    $stmt->execute();
                }

                // Fetch all results from the executed query with the defined fetch mode
                $results = $stmt->fetchAll($this->_fetchMode);
            }
        } catch (PDOException $err){
            // Close connection
            $pdo = null;
            
            // Create error result object
            $errorResult = $this->_result('error', $err->getMessage(), $sql, null, 0, null);

            // Log detailed error information
            $this->_logError($errorResult);

            // Show user-friendly error message
            echo "We're experiencing technical issues. Please try again later.";

            return null;
        }

        // Close connection
        $pdo = null;

        // Return the results
        return $this->_result('success', 'success', $sql, $results, $stmt->rowCount(), null);
    }

    // Non-query method
    public function executeNonQuery($sql, $parameters = null)
    {
        // Executes a query without results.
        try {
            // Attempt to establish a PDO connection using the private connection method
            $pdo = $this->_pdoConnection();

            // If the connection is successful, proceed with the query execution
            if ($pdo) {
                //Initiate transaction
                $pdo->beginTransaction();

                // Prepare the SQL statement using the PDO connection
                $stmt = $pdo->prepare($sql);

                // Check if there are any parameters to bind to the query
                if (!empty($parameters)) {
                    $stmt->execute($parameters);
                } else {
                    $stmt->execute();
                }

                // Last inserted id
                $lastInsertedId = $pdo->lastInsertId();

                // Finish transaction
                $pdo->commit();
            }
        } catch (PDOException $err){
            // Undo all sql operations
            $pdo->rollBack();

            // Close connection
            $pdo = null;
            
            // Create error result object
            $errorResult = $this->_result('error', $err->getMessage(), $sql, null, 0, $lastInsertedId);

            // Log detailed error information
            $this->_logError($errorResult);

            // Show user-friendly error message
            echo "We're experiencing technical issues. Please try again later.";

            return null;
        }

        // Close connection
        $pdo = null;

        // Return the results
        return $this->_result('success', 'success', $sql, null, $stmt->rowCount(), null);
    }

    // Result method
    private function _result($status, $message, $sql, $results, $affectedRows, $lastInsertedId)
    {
        $tmp = new stdClass();
        $tmp->status = $status;
        $tmp->message = $message;
        $tmp->sql = $sql;
        $tmp->results = $results;
        $tmp->affectedRows = $affectedRows;
        $tmp->lastInsertedId = $lastInsertedId;
        return $tmp;
    }

}

?>