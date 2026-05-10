<?php

/**
 * API de Autenticação - Gerencia login, registro e sessões.
 */
require_once '../config/database.php';

// Obtém a ação solicitada via URL (ex: auth.php?action=login)
$action = $_GET['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // AÇÃO: Cadastro de novo usuário
    if ($action === 'register') {
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $phone = $_POST['phone'] ?? '';

        // Validação simples de campos vazios
        if (empty($name) || empty($email) || empty($password) || empty($phone)) {
            sendJson(['error' => 'Todos os campos são obrigatórios'], 400);
        }

        // Verifica se o e-mail já existe no banco
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            sendJson(['error' => 'Email já cadastrado'], 400);
        }

        // Verifica se o telefone já existe no banco
        $stmt = $pdo->prepare('SELECT id FROM users WHERE phone = ?');
        $stmt->execute([$phone]);
        if ($stmt->fetch()) {
            sendJson(['error' => 'Telefone já cadastrado'], 400);
        }

        // Criptografa a senha antes de salvar
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Insere o novo usuário (tipo padrão: cliente)
        $stmt = $pdo->prepare('INSERT INTO users (name, email, password, phone) VALUES (?, ?, ?, ?)');

        if ($stmt->execute([$name, $email, $hashedPassword, $phone])) {
            sendJson(['success' => true, 'message' => 'Cadastro realizado com sucesso']);
        } else {
            sendJson(['error' => 'Erro ao cadastrar'], 500);
        }
    }
    // AÇÃO: Login de usuário existente
    elseif ($action === 'login') {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            sendJson(['error' => 'Email e senha são obrigatórios'], 400);
        }

        // Busca o usuário pelo e-mail
        $stmt = $pdo->prepare('SELECT id, name, password, role FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // Verifica se o usuário existe e se a senha está correta
        if ($user && password_verify($password, $user['password'])) {
            // Cria a sessão do usuário
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];
            sendJson(['success' => true, 'role' => $user['role']]);
        } else {
            sendJson(['error' => 'Email ou senha inválidos'], 401);
        }
    }
    // AÇÃO: Logout (encerra sessão)
    elseif ($action === 'logout') {
        session_destroy();
        sendJson(['success' => true]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {

    // AÇÃO: Retorna dados do usuário logado no momento
    if ($action === 'me') {
        if (isLoggedIn()) {
            sendJson([
                'logged_in' => true,
                'user' => [
                    'id' => $_SESSION['user_id'],
                    'name' => $_SESSION['user_name'],
                    'role' => $_SESSION['user_role']
                ]
            ]);
        } else {
            sendJson(['logged_in' => false]);
        }
    }
}

// Resposta padrão caso nenhuma ação seja correspondida
sendJson(['error' => 'Ação inválida'], 400);
