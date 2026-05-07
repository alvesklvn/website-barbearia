<?php
require_once '../config/database.php';

if (!isLoggedIn()) {
    sendJson(['error' => 'Não autorizado'], 401);
}

$action = $_GET['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if ($action === 'services') {
        $stmt = $pdo->query('SELECT * FROM services');
        sendJson(['services' => $stmt->fetchAll()]);
    } elseif ($action === 'available_times') {
        $date = $_GET['date'] ?? '';
        if (empty($date)) {
            sendJson(['error' => 'Data não informada'], 400);
        }

        // Verifica se é terça a sábado
        $dayOfWeek = date('w', strtotime($date));
        if ($dayOfWeek == 0 || $dayOfWeek == 1) { // 0 = Domingo, 1 = Segunda
            sendJson(['times' => []]); // Sem horários
        }

        // Gerar horários (09:00 às 18:00, a cada 15 min)
        $allTimes = [];
        $start = strtotime('09:00');
        $end = strtotime('18:00');

        while ($start < $end) {
            $allTimes[] = date('H:i', $start);
            $start = strtotime('+15 minutes', $start);
        }

        // Buscar horários já agendados para a data
        $stmt = $pdo->prepare('SELECT TIME_FORMAT(appointment_time, "%H:%i") as time FROM appointments WHERE appointment_date = ? AND status != "cancelado"');
        $stmt->execute([$date]);
        $bookedTimes = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // Filtrar
        $availableTimes = array_values(array_diff($allTimes, $bookedTimes));

        sendJson(['times' => $availableTimes]);
    } elseif ($action === 'my_appointments') {
        $stmt = $pdo->prepare('
            SELECT a.id, a.appointment_date, a.appointment_time, a.status, s.name as service_name, s.price 
            FROM appointments a 
            JOIN services s ON a.service_id = s.id 
            WHERE a.user_id = ? 
            ORDER BY a.appointment_date DESC, a.appointment_time DESC
        ');
        $stmt->execute([$_SESSION['user_id']]);
        sendJson(['appointments' => $stmt->fetchAll()]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'book') {
        $service_id = $_POST['service_id'] ?? '';
        $date = $_POST['date'] ?? '';
        $time = $_POST['time'] ?? '';

        if (empty($service_id) || empty($date) || empty($time)) {
            sendJson(['error' => 'Todos os campos são obrigatórios'], 400);
        }

        // Verifica o dia da semana
        $dayOfWeek = date('w', strtotime($date));
        if ($dayOfWeek == 0 || $dayOfWeek == 1) {
            sendJson(['error' => 'A barbearia não abre aos domingos e segundas.'], 400);
        }

        // Verifica se horário já foi pego
        $stmt = $pdo->prepare('SELECT id FROM appointments WHERE appointment_date = ? AND appointment_time = ? AND status != "cancelado"');
        $stmt->execute([$date, $time]);
        if ($stmt->fetch()) {
            sendJson(['error' => 'Este horário já foi agendado.'], 400);
        }

        $stmt = $pdo->prepare('INSERT INTO appointments (user_id, service_id, appointment_date, appointment_time) VALUES (?, ?, ?, ?)');
        if ($stmt->execute([$_SESSION['user_id'], $service_id, $date, $time])) {
            sendJson(['success' => true, 'message' => 'Agendamento realizado com sucesso!']);
        } else {
            sendJson(['error' => 'Erro ao agendar.'], 500);
        }
    }
}

sendJson(['error' => 'Ação inválida'], 400);
