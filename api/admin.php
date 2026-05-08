<?php
/**
 * API Administrativa - Exclusiva para o barbeiro gerenciar o negócio.
 */
require_once '../config/database.php';

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
            SELECT a.id, u.name as client_name, s.name as service_name, a.appointment_date, a.appointment_time, a.created_at
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

        $stmt = $pdo->prepare('
            SELECT COUNT(a.id) as total_appointments, COALESCE(SUM(s.price), 0) as total_revenue
            FROM appointments a
            JOIN services s ON a.service_id = s.id
            WHERE MONTH(a.appointment_date) = ? AND YEAR(a.appointment_date) = ? AND a.status != "cancelado"
        ');
        $stmt->execute([$month, $year]);
        $stats = $stmt->fetch();

        sendJson(['stats' => [
            'total_appointments' => (int) $stats['total_appointments'],
            'total_revenue' => (float) $stats['total_revenue']
        ]]);
    } 
    // AÇÃO: Lista todos os agendamentos cadastrados (Histórico Global)
    elseif ($action === 'all_appointments') {
        $stmt = $pdo->query('
            SELECT a.id, u.name as client_name, s.name as service_name, s.price, a.appointment_date, a.appointment_time, a.status 
            FROM appointments a 
            JOIN users u ON a.user_id = u.id
            JOIN services s ON a.service_id = s.id 
            ORDER BY a.appointment_date DESC, a.appointment_time DESC
        ');
        sendJson(['appointments' => $stmt->fetchAll()]);
    }
}

sendJson(['error' => 'Ação inválida'], 400);
