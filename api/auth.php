<?php
require_once '../config/database.php';

$action = $_GET['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'register') {
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($name) || empty($email) || empty($password)) {
            sendJson(['error' => 'Todos os campos são obrigatórios'], 400);
        }

        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            sendJson(['error' => 'Email já cadastrado'], 400);
        }

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare('INSERT INTO users (name, email, password) VALUES (?, ?, ?)');
        
        if ($stmt->execute([$name, $email, $hashedPassword])) {
            sendJson(['success' => true, 'message' => 'Cadastro realizado com sucesso']);
        } else {
            sendJson(['error' => 'Erro ao cadastrar'], 500);
        }
    } elseif ($action === 'login') {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            sendJson(['error' => 'Email e senha são obrigatórios'], 400);
        }

        $stmt = $pdo->prepare('SELECT id, name, password, role FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];
            sendJson(['success' => true, 'role' => $user['role']]);
        } else {
            sendJson(['error' => 'Email ou senha inválidos'], 401);
        }
    } elseif ($action === 'logout') {
        session_destroy();
        sendJson(['success' => true]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
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

sendJson(['error' => 'Ação inválida'], 400);
