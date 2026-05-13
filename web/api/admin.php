<?php

/**
 * API Administrativa - Exclusiva para o barbeiro gerenciar o negócio.
 */
require_once '../config/database.php';

// CORS Headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Proteção: Apenas administradores podem acessar estes endpoints
if (!isAdmin()) {
    sendJson(['error' => 'Acesso negado'], 403);
}

$action = $_GET['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    // AÇÃO: Busca notificações de agendamentos recentes
    if ($action === 'notifications') {
        // Pega os 5 agendamentos mais recentes (independente da data do agendamento)
        $stmt = $pdo->query('
            SELECT a.id, u.name as client_name, u.phone as client_phone, s.name as service_name, a.appointment_date, a.appointment_time, a.created_at
            FROM appointments a
            JOIN users u ON a.user_id = u.id
            JOIN services s ON a.service_id = s.id
            ORDER BY a.created_at DESC
            LIMIT 5
        ');
        sendJson(['notifications' => $stmt->fetchAll()]);
    }
    // AÇÃO: Calcula estatísticas financeiras do mês atual
    elseif ($action === 'monthly_stats') {
        $month = date('m');
        $year = date('Y');

        // horário atual de Brasília
        $now = date('Y-m-d H:i:s');

        $stmt = $pdo->prepare('
        SELECT 
            COUNT(a.id) as total_appointments,
            COALESCE(SUM(
                CASE 
                    WHEN TIMESTAMP(a.appointment_date, a.appointment_time) <= ?
                    THEN s.price
                    ELSE 0
                END
            ), 0) as total_revenue
        FROM appointments a
        JOIN services s ON a.service_id = s.id
        WHERE MONTH(a.appointment_date) = ?
        AND YEAR(a.appointment_date) = ?
        AND a.status != "cancelado"
    ');

        $stmt->execute([$now, $month, $year]);
        $stats = $stmt->fetch();

        sendJson([
            'stats' => [
                'total_appointments' => (int)$stats['total_appointments'],
                'total_revenue' => (float)$stats['total_revenue']
            ]
        ]);
    }
    // AÇÃO: Lista todos os agendamentos cadastrados (Histórico Global)
    elseif ($action === 'all_appointments') {
        $stmt = $pdo->query('
            SELECT a.id, u.name as client_name, u.phone as client_phone, s.name as service_name, s.price, a.appointment_date, a.appointment_time, a.status 
            FROM appointments a 
            JOIN users u ON a.user_id = u.id
            JOIN services s ON a.service_id = s.id 
            ORDER BY a.appointment_date DESC, a.appointment_time DESC
        ');
        sendJson(['appointments' => $stmt->fetchAll()]);
    }
    // AÇÃO: Limpa todos os dados (apaga agendamentos do banco)
    elseif ($action === 'clear_data') {
        $stmt = $pdo->prepare('DELETE FROM appointments');
        if ($stmt->execute()) {
            sendJson(['success' => true, 'message' => 'Dados limpados com sucesso']);
        } else {
            sendJson(['error' => 'Erro ao limpar dados'], 500);
        }
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // AÇÃO: Atualiza agendamento (data, hora) e marca para renotificação
    if ($action === 'update_appointment') {
        $appointment_id = $_POST['appointment_id'] ?? '';
        $new_date = $_POST['new_date'] ?? '';
        $new_time = $_POST['new_time'] ?? '';

        if (empty($appointment_id) || empty($new_date) || empty($new_time)) {
            sendJson(['error' => 'ID, data e hora são obrigatórios'], 400);
        }

        // Verificar se o novo horário já está ocupado
        $stmt = $pdo->prepare('SELECT id FROM appointments WHERE appointment_date = ? AND appointment_time = ? AND id != ? AND status != "cancelado"');
        $stmt->execute([$new_date, $new_time, $appointment_id]);
        if ($stmt->fetch()) {
            sendJson(['error' => 'Este horário já está ocupado'], 400);
        }

        // Atualizar o agendamento e mudar status para 'pendente' para renotificação
        $stmt = $pdo->prepare('UPDATE appointments SET appointment_date = ?, appointment_time = ?, status = "confirmado", notification_status = "pendente" WHERE id = ?');
        if ($stmt->execute([$new_date, $new_time, $appointment_id])) {
            sendJson(['success' => true, 'message' => 'Agendamento atualizado com sucesso']);
        } else {
            sendJson(['error' => 'Erro ao atualizar agendamento'], 500);
        }
    }
    // AÇÃO: Deleta um agendamento
    elseif ($action === 'delete_appointment') {
        $appointment_id = $_POST['appointment_id'] ?? '';

        if (empty($appointment_id)) {
            sendJson(['error' => 'ID do agendamento é obrigatório'], 400);
        }

        $stmt = $pdo->prepare('DELETE FROM appointments WHERE id = ?');
        if ($stmt->execute([$appointment_id])) {
            sendJson(['success' => true, 'message' => 'Agendamento deletado com sucesso']);
        } else {
            sendJson(['error' => 'Erro ao deletar agendamento'], 500);
        }
    }
}

sendJson(['error' => 'Ação inválida'], 400);
