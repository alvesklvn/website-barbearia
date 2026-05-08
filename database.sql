CREATE DATABASE IF NOT EXISTS barberia;
USE barberia;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('cliente', 'admin') DEFAULT 'cliente',
    phone VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    service_id INT NOT NULL,
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    status ENUM('pendente', 'confirmado', 'cancelado') DEFAULT 'confirmado',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE
);

-- Inserir alguns serviços básicos
INSERT INTO services (name, price) VALUES ('Corte de Cabelo', 35.00);
INSERT INTO services (name, price) VALUES ('Barba', 25.00);
INSERT INTO services (name, price) VALUES ('Cabelo e Barba', 55.00);

-- Criar o usuário admin padrão (senha: admin123)
-- Hash da senha 'admin123' usando BCRYPT
INSERT INTO users (name, email, password, role) VALUES ('Administrador Barbeiro', 'admin@barbearia.com', '$2y$10$Lh310nxiwSsbo6fP1bOJyuF7HBWfyZekWP67LbrP.10OmoiQoPv26', 'admin');
