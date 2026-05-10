# 💈 Barbearia VIP - Sistema de Agendamento

![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-00000F?style=for-the-badge&logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-563D7C?style=for-the-badge&logo=bootstrap&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)
![Python](https://img.shields.io/badge/Python-3776AB?style=for-the-badge&logo=python&logoColor=white)
![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)


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

## � Mensagem automática via WhatsApp
O sistema já inclui um bot Python que envia mensagens automáticas para clientes com agendamentos pendentes.

### Como usar
1. Entre na pasta do bot:
```bash
cd bot
```
2. Ative o ambiente virtual (Linux):
```bash
source venv/bin/activate
```
3. Instale as dependências necessárias:
```bash
pip install requests python-dotenv mysql-connector-python
```
4. Verifique o arquivo `.env` e ajuste as variáveis, se necessário:
```text
AUTHENTICATION_API_KEY=
DB_HOST=
DB_USER=
DB_PASS=
DB_NAME=
```
5. Execute o bot:
```bash
python app.py
```

O bot consulta a tabela `appointments` e envia mensagens para os registros com `notification_status = 'pendente'`. Após o envio, ele atualiza o status para `enviado`.

---

## �🔑 Credenciais de Teste (Admin)

Para acessar o painel administrativo e visualizar o faturamento, utilize os dados abaixo:
- **E-mail:** `admin@barbearia.com`
- **Senha:** `admin123`

---

## 📂 Estrutura de Pastas

```text
├── .gitignore              # Define arquivos/pastas que o Git deve ignorar
├── bot/                    # Bot Python para envio automático de mensagens via WhatsApp
│   ├── app.py              # Script principal que consulta agendamentos e envia notificações
│   ├── .env                # Variáveis de ambiente do bot (API key e dados do banco)
│   └── venv/               # Ambiente virtual Python usado pelo bot
├── database.sql            # Script SQL para criar o banco, tabelas e dados iniciais
├── docker-compose.yml      # Configuração de orquestração de containers (opcional)
├── README.md               # Documentação do projeto
└── web/                    # Aplicação web PHP/HTML/JS principal
    ├── admin.html          # Painel administrativo para o barbeiro
    ├── dashboard.html      # Área do cliente com histórico de agendamentos
    ├── index.html          # Página inicial / landing page
    ├── login.html          # Tela de login de clientes e admin
    ├── register.html       # Formulário de cadastro de novos clientes
    ├── api/                # Endpoints PHP que atendem o frontend
    │   ├── auth.php        # Autenticação, registro e sessão de usuários
    │   └── appointments.php# Busca de horários, criação de agendamentos e histórico
    ├── assets/             # Arquivos estáticos do frontend
    │   ├── css/style.css   # Estilos customizados da aplicação
    │   └── js/app.js       # Lógica JavaScript compartilhada no frontend
    └── config/             # Configuração do banco de dados para a web
        └── database.php    # Conexão MySQL usada pela aplicação web
```

---

## 📝 Licença
Este projeto foi desenvolvido para fins de demonstração e estudo. Sinta-se à vontade para clonar e adaptar para suas necessidades!

---
*Desenvolvido com ❤️*
