/**
 * Scripts Globais - Funções utilitárias compartilhadas entre todas as páginas.
 */

// Base da URL para chamadas de API
const API_BASE = '/teste1/web/api'; //barberia/api
/**
 * Verifica o status de autenticação do usuário.
 * Faz uma chamada para o backend para saber se há uma sessão ativa.
 * @returns {Promise<Object>} Dados do usuário ou status logado/deslogado
 */
async function checkAuth() {
    try {
        const res = await fetch(`${API_BASE}/auth.php?action=me`, {
            credentials: 'include'
        });
        const data = await res.json();
        return data;
    } catch (e) {
        return { logged_in: false };
    }
}

/**
 * Encerra a sessão do usuário e redireciona para a home.
 */
async function logout() {
    await fetch(`${API_BASE}/auth.php?action=logout`, { method: 'POST', credentials: 'include' });
    window.location.href = 'index.html';
}

/**
 * Exibe um alerta amigável para o usuário.
 * @param {string} message Mensagem a ser exibida
 * @param {string} type Tipo do alerta (success ou danger)
 */
function showToast(message, type = 'success') {
    // Por simplicidade, usamos o alert padrão, mas o código está pronto para expansão
    alert(message);
}
