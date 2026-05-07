const API_BASE = '/barberia/api';

async function checkAuth() {
    try {
        const res = await fetch(`${API_BASE}/auth.php?action=me`);
        const data = await res.json();
        return data;
    } catch (e) {
        return { logged_in: false };
    }
}

async function logout() {
    await fetch(`${API_BASE}/auth.php?action=logout`, { method: 'POST' });
    window.location.href = 'index.html';
}

function showToast(message, type = 'success') {
    // Implementação simples de alerta (poderia ser Toast do Bootstrap)
    alert(message);
}
