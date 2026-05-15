/**
 * Scripts Globais - Funções utilitárias compartilhadas entre todas as páginas.
 */

// Base da URL para chamadas de API
const API_BASE = '/barberia/web/api'; //barberia/api

/**
 * Inicializa o navbar e suas interações
 */
document.addEventListener('DOMContentLoaded', async () => {
    initializeNavbar();
    await updateNavbarAuthStatus();
    setupMobileMenuListeners();
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
        link.addEventListener('click', function() {
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
            sidebarAuthItems.innerHTML = `
                <a href="#" onclick="logout(); return false;" class="sidebar-link">SAIR <i class="fas fa-chevron-right"></i></a>
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
    // Por simplicidade, usamos o alert padrão, mas o código está pronto para expansão
    alert(message);
}

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
        sidebar.addEventListener('click', function(e) {
            if (e.target === sidebar) {
                closeMobileMenu();
            }
        });
    }
}
