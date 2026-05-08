<?php
/**
 * Configuração de conexão com o banco de dados e funções auxiliares.
 */

// Inicia a sessão para controle de autenticação
session_start();

// Configurações do banco de dados MySQL (XAMPP padrão)
$host = 'localhost';
$dbname = 'barberia';
$username = 'root';
$password = '';

try {
    // Cria a conexão usando PDO (PHP Data Objects) para maior segurança
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    
    // Configura o PDO para lançar exceções em caso de erro
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Configura o fetch mode padrão para Array Associativo
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Retorna erro amigável em caso de falha na conexão
    die(json_encode(['error' => 'Falha na conexão com o banco de dados.']));
}

/**
 * Verifica se o usuário atual está logado.
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Verifica se o usuário logado possui nível de administrador.
 * @return bool
 */
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

/**
 * Envia uma resposta em formato JSON e encerra a execução.
 * @param mixed $data Dados a serem enviados
 * @param int $statusCode Código HTTP da resposta
 */
function sendJson($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
