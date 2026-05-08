# 💈 Barbearia VIP - Sistema de Agendamento

![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-00000F?style=for-the-badge&logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-563D7C?style=for-the-badge&logo=bootstrap&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)

Um sistema completo de gestão de agendamentos para barbearias, desenvolvido com **PHP Puro** e **MySQL**. O projeto oferece uma interface elegante, responsiva e funcionalidades específicas tanto para clientes quanto para o proprietário (barbeiro).

---

## ✨ Funcionalidades

### 👤 Área do Cliente
- **Cadastro e Login:** Sistema seguro de autenticação.
- **Agendamento Dinâmico:** Escolha de serviços, datas e horários disponíveis.
- **Regras de Negócio:** Agendamentos permitidos apenas de **terça a sábado**, com intervalos de **15 minutos**.
- **Histórico:** Visualização de todos os agendamentos realizados e seus respectivos status.

### ✂️ Painel do Barbeiro (Admin)
- **Estatísticas Mensais:** Contador de agendamentos totais e faturamento total do mês atual.
- **Notificações:** Alertas visuais de novos agendamentos realizados recentemente.
- **Gestão Global:** Tabela com o histórico completo de todos os clientes e serviços prestados.

---

## 🚀 Tecnologias Utilizadas

- **Backend:** PHP 7.4+ (Estrutura funcional pura, sem frameworks pesados).
- **Banco de Dados:** MySQL (Relacional).
- **Frontend:** HTML5, CSS3 customizado e Bootstrap 5 (via CDN).
- **Comunicação:** AJAX / Fetch API para uma experiência sem recarregamento de página (SPA-like).
- **Segurança:** Criptografia de senhas com `password_hash` (BCRYPT).

---

## 🛠️ Como Instalar e Rodar

### 1. Pré-requisitos
- Ter o **XAMPP**, **WAMP** ou **Laragon** instalado no computador.
- Git instalado (opcional).

### 2. Clonar o projeto
Mova a pasta do projeto para dentro do diretório de servidor local (geralmente `htdocs` no XAMPP ou `www` no WAMP).

### 3. Configurar o Banco de Dados
1. Abra o **phpMyAdmin** (`http://localhost/phpmyadmin`).
2. Crie um novo banco de dados chamado `barberia`.
3. Importe o arquivo `database.sql` que está na raiz do projeto.

### 4. Rodar a aplicação
Acesse no seu navegador:
```text
http://localhost/barberia
```

---

## 🔑 Credenciais de Teste (Admin)

Para acessar o painel administrativo e visualizar o faturamento, utilize os dados abaixo:
- **E-mail:** `admin@barbearia.com`
- **Senha:** `admin123`

---

## 📂 Estrutura de Pastas

```text
├── api/                # Endpoints PHP para comunicação com o frontend
├── assets/             # Arquivos estáticos (CSS, JS, Imagens)
├── config/             # Configuração de conexão com o banco de dados
├── admin.html          # Painel do Barbeiro
├── dashboard.html      # Painel do Cliente
├── database.sql        # Script de criação do banco de dados
└── index.html          # Landing Page principal
```

---

## 📝 Licença
Este projeto foi desenvolvido para fins de demonstração e estudo. Sinta-se à vontade para clonar e adaptar para suas necessidades!

---
*Desenvolvido com ❤️ para a Barbearia VIP.*
