-- 1. Criação do Banco de Dados
CREATE DATABASE IF NOT EXISTS barberia;
USE barberia;

-- 2. Tabela de Usuários (Deve vir antes de appointments)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('cliente', 'admin') DEFAULT 'cliente',
    phone VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. Tabela de Serviços (Deve vir antes de appointments)
CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 4. Tabela de Agendamentos (Já com a coluna de notificação inclusa)
CREATE TABLE IF NOT EXISTS appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    service_id INT NOT NULL,
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    status ENUM('pendente', 'confirmado', 'cancelado') DEFAULT 'confirmado',
    -- A coluna abaixo resolve o erro 1054 do seu script Python
    notification_status ENUM('pendente', 'enviado') DEFAULT 'pendente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE
);

-- ==========================================================
-- DADOS INICIAIS PARA TESTE (Opcional)
-- ==========================================================

-- Inserir serviços básicos
INSERT INTO services (name, price) VALUES ('Corte de Cabelo', 35.00);
INSERT INTO services (name, price) VALUES ('Barba', 25.00);
INSERT INTO services (name, price) VALUES ('Cabelo e Barba', 55.00);

-- Criar o usuário admin padrão (Senha: admin123)
INSERT INTO users (name, email, password, role, phone) 
VALUES ('Administrador Barbeiro', 'admin@barbearia.com', '$2y$10$Lh310nxiwSsbo6fP1bOJyuF7HBWfyZekWP67LbrP.10OmoiQoPv26', 'admin', '5511999999999');