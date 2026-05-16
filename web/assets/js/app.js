/**
 * Scripts Globais - Funções utilitárias compartilhadas entre todas as páginas.
 */

// Base da URL para chamadas de API
const API_BASE = '/teste1/web/api'; //barberia/api

/**
 * Inicializa o navbar e suas interações
 */
document.addEventListener('DOMContentLoaded', async () => {
    initializeNavbar();
    await updateNavbarAuthStatus();
    setupMobileMenuListeners();
    setupBackToTopButton();
});

/**
 * Inicializa a animação e funcionalidades do navbar
 */
function initializeNavbar() {
    const navItems = document.getElementById('nav-items');
    if (!navItems) return;

    const links = navItems.querySelectorAll('a:not(.btn-nav)');
    const pill = document.getElementById('nav-pill');

    if (!pill) return;

    // Define qual link é ativo baseado na página atual
    setActiveNavLink(links);

    // Atualiza a posição do pill ao clicar
    links.forEach(link => {
        link.addEventListener('click', function () {
            links.forEach(l => l.classList.remove('active'));
            this.classList.add('active');
            updatePillPosition(this, pill);
        });
    });

    // Atualiza posição do pill na primeira carga
    const activeLink = navItems.querySelector('a.active');
    if (activeLink) {
        updatePillPosition(activeLink, pill);
    }

    // Reajusta a posição do pill ao redimensionar
    window.addEventListener('resize', () => {
        if (activeLink) {
            updatePillPosition(activeLink, pill);
        }
    });
}

/**
 * Define qual link deve estar ativo baseado na página atual
 */
function setActiveNavLink(links) {
    const currentPage = window.location.pathname.split('/').pop() || 'index.html';

    links.forEach(link => {
        link.classList.remove('active');
        const href = link.getAttribute('href');

        if (href === currentPage || (href === '#contato' && currentPage === 'index.html' && !document.querySelector('.active'))) {
            if (href === currentPage) {
                link.classList.add('active');
            }
        }
    });

    // Se nenhum link estiver ativo, ativa o primeiro
    if (!document.querySelector('.menu-nav a.active')) {
        links[0].classList.add('active');
    }
}

/**
 * Atualiza a posição do pill (elemento de fundo ativo)
 */
function updatePillPosition(activeLink, pill) {
    const left = activeLink.offsetLeft - 5;
    const width = activeLink.offsetWidth + 10;

    pill.style.left = left + 'px';
    pill.style.width = width + 'px';
}

/**
 * Atualiza o navbar de acordo com o status de autenticação
 */
async function updateNavbarAuthStatus() {
    const auth = await checkAuth();
    const navItems = document.getElementById('nav-items');
    const sidebarAuthItems = document.getElementById('sidebar-auth-items');

    if (auth.logged_in) {
        // Usuário logado
        if (navItems) {
            const btnEntrar = navItems.querySelector('#btn-entrar');
            if (btnEntrar) {
                btnEntrar.textContent = 'SAIR';
                btnEntrar.classList.add('btn-logout');
                btnEntrar.onclick = logout;
            }
        }

        if (sidebarAuthItems) {
            const firstName = auth.user.name ? auth.user.name.split(" ")[0] : "";
            let adminLink = "";
            if (auth.user.role === 'admin') {
                adminLink = `<a href="admin.html" class="sidebar-link" style="font-weight: bold; color: #d4af37;">PAINEL DO BARBEIRO <i class="fas fa-chevron-right"></i></a>`;
            }
            sidebarAuthItems.innerHTML = `
                ${adminLink}
                <div class="sidebar-greeting" style="color: rgba(255,255,255,0.8); font-size: 1.1rem; font-weight: 600; text-align: center; margin-top: 30px; margin-bottom: 15px;">Olá, ${firstName}</div>
                <a href="#" onclick="logout(); return false;" style="display: block; text-align: center; color: #d32f2f; border: 1px solid #d32f2f; border-radius: 50px; padding: 12px 0; font-weight: 600; text-decoration: none; font-size: 1rem; background: transparent; transition: all 0.3s ease;">SAIR</a>
            `;
        }
    } else {
        // Usuário não logado
        if (navItems) {
            const btnEntrar = navItems.querySelector('#btn-entrar');
            if (btnEntrar) {
                btnEntrar.textContent = 'ENTRAR';
                btnEntrar.classList.remove('btn-logout');
                btnEntrar.onclick = () => showLogin();
            }
        }

        if (sidebarAuthItems) {
            sidebarAuthItems.innerHTML = `
                <a onclick="showLogin(); closeMobileMenu()" class="sidebar-link">ENTRAR <i class="fas fa-chevron-right"></i></a>
            `;
        }
    }
}

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
    let toastContainer = document.getElementById('toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toast-container';
        document.body.appendChild(toastContainer);
    }

    const toast = document.createElement('div');
    
    // Inferir tipo pela mensagem se vier como genérico (ex: do window.alert)
    if (message.toLowerCase().includes('erro') || message.toLowerCase().includes('inválido') || message.toLowerCase().includes('por favor') || message.toLowerCase().includes('falha')) {
        type = 'danger';
    } else if (message.toLowerCase().includes('sucesso') || message.toLowerCase().includes('realizado')) {
        type = 'success';
    } else if (type !== 'success' && type !== 'danger') {
        type = 'warning';
    }

    toast.className = `custom-toast toast-${type}`;
    
    let iconClass = 'fas fa-info-circle';
    if (type === 'success') iconClass = 'fas fa-check-circle';
    else if (type === 'danger' || type === 'error') iconClass = 'fas fa-exclamation-circle';
    else if (type === 'warning') iconClass = 'fas fa-exclamation-triangle';

    toast.innerHTML = `
        <div class="toast-content">
            <i class="${iconClass} toast-icon"></i>
            <span class="toast-message">${message}</span>
        </div>
        <button class="toast-close" onclick="this.parentElement.classList.remove('show'); setTimeout(() => this.parentElement.remove(), 300)"><i class="fas fa-times"></i></button>
    `;

    toastContainer.appendChild(toast);

    // Animacao de entrada
    setTimeout(() => toast.classList.add('show'), 10);

    // Remover automaticamente
    setTimeout(() => {
        if (toast.parentElement) {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }
    }, 4500);
}

// Substitui o alert nativo pelo novo toast
window.alert = function(msg) {
    showToast(msg, 'warning');
};

/**
 * Funções do Menu Lateral Mobile
 */
function openMobileMenu() {
    const sidebar = document.getElementById('mobileSidebar');
    if (sidebar) {
        sidebar.classList.add('active');
        document.body.style.overflow = 'hidden'; // Evita scroll ao fundo
    }
}

function closeMobileMenu() {
    const sidebar = document.getElementById('mobileSidebar');
    if (sidebar) {
        sidebar.classList.remove('active');
        document.body.style.overflow = 'auto'; // Restaura scroll
    }
}

function setupMobileMenuListeners() {
    // Fecha o menu ao clicar fora dele (opcional)
    const sidebar = document.getElementById('mobileSidebar');
    if (sidebar) {
        sidebar.addEventListener('click', function (e) {
            if (e.target === sidebar) {
                closeMobileMenu();
            }
        });
    }
}

/**
 * Adiciona um botão de "voltar ao topo" elegante
 */
function setupBackToTopButton() {
    const btn = document.createElement('button');
    btn.id = 'btn-back-to-top';
    btn.innerHTML = '<i class="fas fa-chevron-up"></i>';
    document.body.appendChild(btn);

    window.addEventListener('scroll', () => {
        if (window.scrollY > 300) {
            btn.classList.add('show');
        } else {
            btn.classList.remove('show');
        }
    });

    btn.addEventListener('click', () => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
}
