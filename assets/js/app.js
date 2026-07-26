/**
 * SGRC - Main Application JavaScript
 * الجافاسكريبت الرئيسي للتطبيق
 */

// ===== Theme Management =====
const ThemeManager = {
    init() {
        const savedTheme = localStorage.getItem('sgrc_theme') || 'light';
        this.setTheme(savedTheme);

        document.getElementById('themeToggle')?.addEventListener('click', () => {
            const current = document.documentElement.getAttribute('data-theme');
            const next = current === 'dark' ? 'light' : 'dark';
            this.setTheme(next);
        });
    },

    setTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        localStorage.setItem('sgrc_theme', theme);

        const icon = document.querySelector('#themeToggle i');
        if (icon) {
            icon.className = theme === 'dark' ? 'bi bi-sun-fill' : 'bi bi-moon-fill';
        }
    }
};

// ===== Sidebar Toggle =====
const SidebarManager = {
    init() {
        const toggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');

        toggle?.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            localStorage.setItem('sgrc_sidebar', sidebar.classList.contains('collapsed') ? 'collapsed' : 'expanded');
        });

        // Restore state
        if (localStorage.getItem('sgrc_sidebar') === 'collapsed') {
            sidebar?.classList.add('collapsed');
        }

        // Mobile toggle
        document.getElementById('mobileToggle')?.addEventListener('click', () => {
            sidebar?.classList.toggle('show');
        });
    }
};

// ===== Language Switcher =====
const LanguageManager = {
    init() {
        document.querySelectorAll('.lang-switch').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const lang = btn.dataset.lang;
                this.switchLanguage(lang);
            });
        });
    },

    async switchLanguage(lang) {
        try {
            const response = await fetch(`/modules/auth/switch_lang.php?lang=${lang}`);
            const data = await response.json();
            if (data.success) {
                window.location.reload();
            }
        } catch (error) {
            console.error('Language switch error:', error);
        }
    }
};

// ===== Flash Messages =====
const FlashManager = {
    init() {
        document.querySelectorAll('.flash-message').forEach(msg => {
            setTimeout(() => {
                msg.style.opacity = '0';
                msg.style.transform = 'translateX(100%)';
                setTimeout(() => msg.remove(), 300);
            }, 5000);
        });
    },

    show(type, message) {
        const container = document.querySelector('.flash-container') || this.createContainer();
        const msg = document.createElement('div');
        msg.className = `flash-message ${type}`;
        msg.innerHTML = `
            <i class="bi bi-${this.getIcon(type)}"></i>
            <span>${message}</span>
        `;
        container.appendChild(msg);

        setTimeout(() => {
            msg.style.opacity = '0';
            setTimeout(() => msg.remove(), 300);
        }, 5000);
    },

    createContainer() {
        const container = document.createElement('div');
        container.className = 'flash-container';
        document.body.appendChild(container);
        return container;
    },

    getIcon(type) {
        const icons = {
            success: 'check-circle-fill',
            error: 'x-circle-fill',
            warning: 'exclamation-triangle-fill',
            info: 'info-circle-fill'
        };
        return icons[type] || 'info-circle-fill';
    }
};

// ===== Loading Overlay =====
const LoadingManager = {
    show() {
        let overlay = document.querySelector('.loading-overlay');
        if (!overlay) {
            overlay = document.createElement('div');
            overlay.className = 'loading-overlay';
            overlay.innerHTML = '<div class="spinner-custom"></div>';
            document.body.appendChild(overlay);
        }
        overlay.classList.add('show');
    },

    hide() {
        document.querySelector('.loading-overlay')?.classList.remove('show');
    }
};

// ===== Confirm Delete =====
function confirmDelete(message = 'Are you sure?') {
    return confirm(message);
}

// ===== DataTables Initialization =====
function initDataTable(selector, options = {}) {
    if (typeof $.fn.DataTable === 'undefined') return;

    const defaults = {
        language: {
            url: getLang() === 'ar' 
                ? '//cdn.datatables.net/plug-ins/1.13.6/i18n/ar.json'
                : '//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json'
        },
        pageLength: 25,
        responsive: true,
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
             '<"row"<"col-sm-12"tr>>' +
             '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        ...options
    };

    return $(selector).DataTable(defaults);
}

// ===== Helper Functions =====
function getLang() {
    return document.documentElement.lang || 'ar';
}

function formatDate(date, format = 'YYYY/MM/DD') {
    const d = new Date(date);
    const pad = (n) => n.toString().padStart(2, '0');

    return format
        .replace('YYYY', d.getFullYear())
        .replace('MM', pad(d.getMonth() + 1))
        .replace('DD', pad(d.getDate()));
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// ===== Initialize Everything =====
document.addEventListener('DOMContentLoaded', () => {
    ThemeManager.init();
    SidebarManager.init();
    LanguageManager.init();
    FlashManager.init();
});

// ===== AJAX Helper =====
async function ajax(url, options = {}) {
    LoadingManager.show();
    try {
        const response = await fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                ...options.headers
            },
            ...options
        });

        if (!response.ok) throw new Error('Network error');
        return await response.json();
    } catch (error) {
        FlashManager.show('error', error.message);
        throw error;
    } finally {
        LoadingManager.hide();
    }
}