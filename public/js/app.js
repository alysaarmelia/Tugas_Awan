/**
 * IaaS Portal — Frontend Application
 * Vanilla JS + Tailwind CSS, ES Modules
 * API Base: /api/v1
 */

// ================================================================
// API Client
// Wraps fetch with the standard CI4-API response envelope:
// { status, message, data, errors, meta }
// ================================================================
const API = {
    baseUrl: '/api/v1',

    async request(method, path, body = null) {
        const headers = { 'Content-Type': 'application/json' };
        const token = localStorage.getItem('access_token');
        if (token) headers['Authorization'] = `Bearer ${token}`;

        const res = await fetch(this.baseUrl + path, {
            method,
            headers,
            body: body ? JSON.stringify(body) : null,
        });

        // Parse response — handle both JSON and non-JSON
        let data;
        const contentType = res.headers.get('content-type') || '';
        if (contentType.includes('application/json')) {
            data = await res.json().catch(() => null);
        } else {
            data = null;
        }

        if (!res.ok) {
            // Try to extract message from CI4-API envelope
            const msg = data?.message || data?.detail || `HTTP ${res.status}`;
            throw new Error(msg);
        }

        // Return full envelope so caller can access meta if needed
        return data;
    },

    get(path)    { return this.request('GET',    path); },
    post(path, body) { return this.request('POST',   path, body); },
};

// ================================================================
// Auth — manages JWT tokens and user session
// ================================================================
const Auth = {
    getToken:       () => localStorage.getItem('access_token'),
    getRefreshToken: () => localStorage.getItem('refresh_token'),
    getUser:        () => JSON.parse(localStorage.getItem('user') || 'null'),

    setTokens(accessToken, refreshToken) {
        localStorage.setItem('access_token', accessToken);
        localStorage.setItem('refresh_token', refreshToken);
    },

    setUser(user) {
        localStorage.setItem('user', JSON.stringify(user));
    },

    clear() {
        localStorage.removeItem('access_token');
        localStorage.removeItem('refresh_token');
        localStorage.removeItem('user');
    },

    isAuthenticated() {
        return !!this.getToken();
    },

    async login(email, password) {
        const res = await API.post('/auth/login', { email, password });
        // data = { access_token, refresh_token, token_type, expires_in }
        this.setTokens(res.data.access_token, res.data.refresh_token);
        await this.fetchAndStoreUser();
        return res;
    },

    async register(username, email, password, confirm_password) {
        return API.post('/auth/register', { username, email, password, confirm_password });
    },

    async logout() {
        try {
            await API.post('/auth/logout', {});
        } catch (_) { /* ignore */ }
        this.clear();
        Router.navigate('/');
    },

    async fetchAndStoreUser() {
        try {
            const res = await API.get('/user/me');
            this.setUser(res.data);
        } catch (_) { /* ignore */ }
    },

    async refresh() {
        const refreshToken = this.getRefreshToken();
        if (!refreshToken) return false;
        try {
            const res = await API.post('/auth/refresh', { refresh_token: refreshToken });
            this.setTokens(res.data.access_token, res.data.refresh_token);
            return true;
        } catch (_) {
            this.clear();
            return false;
        }
    },
};

// ================================================================
// Toast Notifications
// ================================================================
const Toast = {
    show(message, type = 'info') {
        const toast    = document.getElementById('toast');
        const content  = document.getElementById('toastContent');
        const colors   = {
            info:    'bg-slate-800',
            success: 'bg-green-600',
            error:   'bg-red-600',
            warning: 'bg-amber-500',
        };
        const icons = {
            success: 'check-circle',
            error:   'exclamation-circle',
            warning: 'exclamation-triangle',
            info:    'info-circle',
        };
        const cls = colors[type] || colors.info;
        const ico = icons[type] || icons.info;
        content.className = `${cls} text-white px-5 py-3 rounded-lg shadow-lg flex items-center gap-3 text-sm fade-in`;
        content.innerHTML = `<i class="fas fa-${ico}"></i>${message}`;
        toast.classList.remove('hidden');
        setTimeout(() => toast.classList.add('hidden'), 4000);
    },
};

// ================================================================
// Router — SPA hash-based routing
// ================================================================
const Router = {
    routes: {
        '/':            'dashboard',
        '/dashboard':   'dashboard',
        '/storage':     'storage',
        '/credentials': 'credentials',
        '/subscription': 'subscription',
        '/logs':        'logs',
    },

    navigate(path) {
        history.pushState(null, '', window.location.origin + path);
        this.handleRoute(path);
    },

    handleRoute(path = window.location.hash.slice(1) || '/') {
        if (!Auth.isAuthenticated() && path !== '/') {
            this.navigate('/');
            return;
        }
        const page = this.routes[path] || 'dashboard';
        this.loadPage(page);
    },

    async loadPage(pageName) {
        try {
            const res  = await fetch(`/js/pages/${pageName}.js`);
            if (!res.ok) throw new Error('Page not found');
            const code = await res.text();
            eval(code); // page script sets up DOM and loads data
        } catch (e) {
            const el = document.getElementById('pageContent');
            if (el) {
                el.innerHTML = `<div class="text-center py-20 text-slate-400">
                    <i class="fas fa-exclamation-triangle text-3xl mb-3"></i>
                    <p>${e.message}</p>
                </div>`;
            }
        }

        // Highlight active sidebar link
        document.querySelectorAll('.sidebar-link').forEach(link => {
            link.classList.remove('active');
            if (link.dataset.page === pageName) link.classList.add('active');
        });
    },
};

// ================================================================
// UI Helpers
// ================================================================
const UI = {
    updateUserInfo() {
        const user    = Auth.getUser();
        if (!user) return;
        const initial = (user.username?.[0] || 'U').toUpperCase();
        document.querySelectorAll('#sidebarAvatar, #headerAvatar')
            .forEach(el => { if (el) el.textContent = initial; });
        const ue = document.getElementById('sidebarUsername');
        const ee = document.getElementById('sidebarEmail');
        if (ue) ue.textContent = user.username;
        if (ee) ee.textContent = user.email;
    },

    getTierBadge(tier) {
        return { free: 'bg-slate-200 text-slate-700', pro: 'bg-blue-100 text-blue-700', enterprise: 'bg-purple-100 text-purple-700' }[tier] || '';
    },

    getProgressColor(percent) {
        if (percent >= 90) return 'progress-red';
        if (percent >= 70) return 'progress-yellow';
        return 'progress-green';
    },

    formatDate(dateStr) {
        if (!dateStr) return '--';
        return new Date(dateStr).toLocaleDateString('en-US', {
            year: 'numeric', month: 'short', day: 'numeric',
            hour: '2-digit', minute: '2-digit',
        });
    },
};

// ================================================================
// Auth Page Init (login / register tabs)
// ================================================================
async function initAuthPage() {
    const loginForm    = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    const tabLogin     = document.getElementById('tabLogin');
    const tabRegister  = document.getElementById('tabRegister');
    const alert        = document.getElementById('authAlert');

    function showAlert(msg, type = 'error') {
        const bg = type === 'success'
            ? 'bg-green-50 text-green-700 border border-green-200'
            : 'bg-red-50 text-red-700 border border-red-200';
        alert.className = `mb-4 p-3 rounded-lg text-sm flex items-center gap-2 ${bg}`;
        alert.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>${msg}`;
        alert.classList.remove('hidden');
    }

    function switchTab(tab) {
        const showLogin = tab === 'login';
        loginForm.classList.toggle('hidden',    !showLogin);
        registerForm.classList.toggle('hidden',  showLogin);
        tabLogin.classList.toggle('bg-white',    showLogin);
        tabLogin.classList.toggle('shadow-sm',   showLogin);
        tabLogin.classList.toggle('text-slate-800', showLogin);
        tabLogin.classList.toggle('text-slate-500', !showLogin);
        tabRegister.classList.toggle('bg-white',    !showLogin);
        tabRegister.classList.toggle('shadow-sm',    !showLogin);
        tabRegister.classList.toggle('text-slate-800', !showLogin);
        tabRegister.classList.toggle('text-slate-500',  showLogin);
        alert.classList.add('hidden');
    }

    tabLogin?.addEventListener('click',    () => switchTab('login'));
    tabRegister?.addEventListener('click', () => switchTab('register'));

    // Login
    loginForm?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const btn   = document.getElementById('loginBtn');
        const email = loginForm.email.value.trim();
        const pass  = loginForm.password.value;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Signing in...';
        try {
            await Auth.login(email, pass);
            showAlert('Login successful!', 'success');
            setTimeout(() => Router.navigate('/dashboard'), 800);
        } catch (err) {
            showAlert(err.message);
            btn.disabled = false;
            btn.innerHTML = 'Sign In <i class="fas fa-arrow-right text-xs"></i>';
        }
    });

    // Register
    registerForm?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const btn = document.getElementById('registerBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating account...';
        try {
            await Auth.register(
                registerForm.username.value.trim(),
                registerForm.email.value.trim(),
                registerForm.password.value,
                registerForm.confirm_password.value,
            );
            showAlert('Account created! You can now sign in.', 'success');
            setTimeout(() => switchTab('login'), 1500);
        } catch (err) {
            showAlert(err.message);
            btn.disabled = false;
            btn.innerHTML = 'Create Account <i class="fas fa-user-plus text-xs"></i>';
        }
    });
}

// ================================================================
// App Init
// ================================================================
async function init() {
    if (document.getElementById('loginForm')) {
        initAuthPage();
        return;
    }

    if (!Auth.isAuthenticated()) {
        window.location.href = '/';
        return;
    }

    UI.updateUserInfo();
    document.getElementById('logoutBtn')?.addEventListener('click', () => Auth.logout());

    window.addEventListener('hashchange', () => Router.handleRoute());
    Router.handleRoute();
}

init();
