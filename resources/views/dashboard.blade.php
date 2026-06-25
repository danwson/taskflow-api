<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TaskFlow</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #0f172a;
            color: #e2e8f0;
            min-height: 100vh;
        }

        /* AUTH */
        #auth-screen {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 1rem;
        }

        .auth-card {
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 12px;
            padding: 2.5rem;
            width: 100%;
            max-width: 400px;
        }

        .auth-card h1 {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: .25rem;
            color: #f8fafc;
        }

        .auth-card p { color: #94a3b8; margin-bottom: 2rem; font-size: .9rem; }

        .tabs { display: flex; gap: .5rem; margin-bottom: 1.5rem; }

        .tab {
            flex: 1; padding: .5rem; border: 1px solid #334155;
            background: transparent; color: #94a3b8; border-radius: 6px;
            cursor: pointer; font-size: .875rem; transition: all .2s;
        }

        .tab.active { background: #6366f1; border-color: #6366f1; color: #fff; }

        .form-group { margin-bottom: 1rem; }

        .form-group label { display: block; font-size: .8rem; color: #94a3b8; margin-bottom: .4rem; }

        .form-group input {
            width: 100%; padding: .65rem .9rem;
            background: #0f172a; border: 1px solid #334155;
            border-radius: 6px; color: #f8fafc; font-size: .875rem;
            outline: none; transition: border-color .2s;
        }

        .form-group input:focus { border-color: #6366f1; }

        .btn {
            width: 100%; padding: .7rem;
            background: #6366f1; border: none; border-radius: 6px;
            color: #fff; font-size: .9rem; font-weight: 600;
            cursor: pointer; transition: background .2s;
        }

        .btn:hover { background: #4f46e5; }
        .btn:disabled { background: #334155; cursor: not-allowed; }

        .error-msg {
            background: #450a0a; border: 1px solid #991b1b;
            color: #fca5a5; border-radius: 6px; padding: .75rem;
            font-size: .8rem; margin-bottom: 1rem; display: none;
        }

        /* APP */
        #app-screen { display: none; }

        .topbar {
            background: #1e293b; border-bottom: 1px solid #334155;
            padding: .9rem 1.5rem; display: flex;
            align-items: center; justify-content: space-between;
        }

        .topbar h1 { font-size: 1.1rem; font-weight: 700; color: #f8fafc; }
        .topbar span { font-size: .8rem; color: #94a3b8; }

        .logout-btn {
            background: transparent; border: 1px solid #334155;
            color: #94a3b8; padding: .35rem .8rem; border-radius: 6px;
            cursor: pointer; font-size: .8rem; transition: all .2s;
        }

        .logout-btn:hover { border-color: #ef4444; color: #ef4444; }

        .layout { display: grid; grid-template-columns: 260px 1fr; height: calc(100vh - 57px); }

        /* SIDEBAR */
        .sidebar {
            background: #1e293b; border-right: 1px solid #334155;
            padding: 1rem; overflow-y: auto;
        }

        .sidebar-title {
            font-size: .7rem; font-weight: 600; color: #64748b;
            text-transform: uppercase; letter-spacing: .05em;
            margin-bottom: .75rem; padding: 0 .5rem;
        }

        .workspace-item {
            padding: .6rem .75rem; border-radius: 6px;
            cursor: pointer; font-size: .875rem; color: #cbd5e1;
            transition: all .2s; margin-bottom: .25rem;
            display: flex; align-items: center; gap: .5rem;
        }

        .workspace-item:hover { background: #0f172a; color: #f8fafc; }
        .workspace-item.active { background: #312e81; color: #a5b4fc; }

        .workspace-icon {
            width: 24px; height: 24px; border-radius: 6px;
            background: #6366f1; display: flex; align-items: center;
            justify-content: center; font-size: .7rem; font-weight: 700;
            flex-shrink: 0;
        }

        .new-workspace-btn {
            width: 100%; padding: .55rem; margin-top: .5rem;
            background: transparent; border: 1px dashed #334155;
            color: #64748b; border-radius: 6px; cursor: pointer;
            font-size: .8rem; transition: all .2s;
        }

        .new-workspace-btn:hover { border-color: #6366f1; color: #6366f1; }

        /* MAIN */
        .main { overflow-y: auto; padding: 1.5rem; }

        .main-empty {
            display: flex; flex-direction: column; align-items: center;
            justify-content: center; height: 100%; color: #475569;
            gap: .5rem;
        }

        .main-empty svg { opacity: .3; }

        .section-header {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 1.25rem;
        }

        .section-header h2 { font-size: 1.1rem; font-weight: 600; color: #f8fafc; }

        .add-btn {
            padding: .45rem .9rem; background: #6366f1;
            border: none; border-radius: 6px; color: #fff;
            font-size: .8rem; cursor: pointer; transition: background .2s;
        }

        .add-btn:hover { background: #4f46e5; }

        /* PROJECTS */
        .projects-grid {
            display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 1rem; margin-bottom: 2rem;
        }

        .project-card {
            background: #1e293b; border: 1px solid #334155;
            border-radius: 10px; padding: 1rem; cursor: pointer;
            transition: all .2s;
        }

        .project-card:hover { border-color: #6366f1; transform: translateY(-1px); }
        .project-card.active { border-color: #6366f1; background: #1e1b4b; }

        .project-card h3 { font-size: .9rem; font-weight: 600; margin-bottom: .25rem; }
        .project-card p { font-size: .75rem; color: #64748b; }
        .project-card .task-count {
            margin-top: .75rem; font-size: .75rem; color: #6366f1;
            font-weight: 600;
        }

        /* TASKS */
        .tasks-section { margin-top: .5rem; }

        .kanban { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; }

        .kanban-col {
            background: #1e293b; border: 1px solid #334155;
            border-radius: 10px; padding: 1rem;
        }

        .kanban-col-title {
            font-size: .75rem; font-weight: 600; text-transform: uppercase;
            letter-spacing: .05em; margin-bottom: 1rem; display: flex;
            align-items: center; gap: .5rem;
        }

        .col-dot {
            width: 8px; height: 8px; border-radius: 50%;
        }

        .col-todo .col-dot { background: #64748b; }
        .col-in_progress .col-dot { background: #f59e0b; }
        .col-done .col-dot { background: #22c55e; }

        .task-card {
            background: #0f172a; border: 1px solid #1e293b;
            border-radius: 8px; padding: .75rem; margin-bottom: .5rem;
            cursor: pointer; transition: border-color .2s;
        }

        .task-card:hover { border-color: #6366f1; }

        .task-card h4 { font-size: .8rem; font-weight: 500; margin-bottom: .4rem; }

        .status-buttons { display: flex; gap: .4rem; margin-top: .75rem; flex-wrap: wrap; }

        .status-btn {
            flex: 1; padding: .35rem .5rem; border-radius: 5px; font-size: .7rem;
            font-weight: 600; cursor: pointer; border: 1px solid transparent;
            transition: all .2s; text-align: center;
        }

        .status-btn.todo { background: #1e293b; color: #94a3b8; border-color: #334155; }
        .status-btn.in_progress { background: #3b2f1a; color: #fbbf24; border-color: #92400e; }
        .status-btn.done { background: #1e3a2f; color: #4ade80; border-color: #166534; }
        .status-btn.active-status { opacity: 1; }
        .status-btn:not(.active-status) { opacity: .4; }
        .status-btn:hover { opacity: 1; }

        .task-meta { display: flex; gap: .4rem; flex-wrap: wrap; }

        .badge {
            font-size: .65rem; padding: .2rem .45rem;
            border-radius: 4px; font-weight: 600;
        }

        .badge-low { background: #1e3a2f; color: #4ade80; }
        .badge-medium { background: #3b2f1a; color: #fbbf24; }
        .badge-high { background: #3b1a1a; color: #f87171; }
        .badge-date { background: #1e293b; color: #94a3b8; border: 1px solid #334155; }

        .add-task-btn {
            width: 100%; padding: .5rem; background: transparent;
            border: 1px dashed #334155; color: #475569; border-radius: 6px;
            cursor: pointer; font-size: .75rem; margin-top: .5rem;
            transition: all .2s;
        }

        .add-task-btn:hover { border-color: #6366f1; color: #6366f1; }

        /* MODAL */
        .modal-overlay {
            position: fixed; inset: 0; background: rgba(0,0,0,.7);
            display: flex; align-items: center; justify-content: center;
            z-index: 100; padding: 1rem;
        }

        .modal {
            background: #1e293b; border: 1px solid #334155;
            border-radius: 12px; padding: 1.75rem; width: 100%;
            max-width: 440px;
        }

        .modal h3 { font-size: 1rem; font-weight: 600; margin-bottom: 1.25rem; }

        .modal .form-group input,
        .modal .form-group select,
        .modal .form-group textarea {
            width: 100%; padding: .65rem .9rem;
            background: #0f172a; border: 1px solid #334155;
            border-radius: 6px; color: #f8fafc; font-size: .875rem;
            outline: none; transition: border-color .2s;
        }

        .modal .form-group select option { background: #1e293b; }
        .modal .form-group textarea { resize: vertical; min-height: 80px; }
        .modal .form-group input:focus,
        .modal .form-group select:focus,
        .modal .form-group textarea:focus { border-color: #6366f1; }

        .modal-footer { display: flex; gap: .75rem; margin-top: 1.25rem; }

        .modal-footer .btn { flex: 1; }

        .cancel-btn {
            flex: 1; padding: .7rem; background: transparent;
            border: 1px solid #334155; border-radius: 6px; color: #94a3b8;
            font-size: .9rem; cursor: pointer; transition: all .2s;
        }

        .cancel-btn:hover { border-color: #ef4444; color: #ef4444; }

        .loading { color: #64748b; font-size: .85rem; padding: 1rem 0; }

        .ws-edit-btn, .ws-del-btn {
            opacity: 0; font-size: .75rem; color: #64748b; padding: .1rem .3rem;
            border-radius: 4px; transition: all .2s; line-height: 1;
        }
        .workspace-item:hover .ws-edit-btn,
        .workspace-item:hover .ws-del-btn { opacity: 1; }
        .ws-edit-btn:hover { background: #334155; color: #a5b4fc; }
        .ws-del-btn:hover { background: #3b1a1a; color: #f87171; }

        .project-edit-btn, .project-del-btn {
            opacity: 0; font-size: .8rem; color: #64748b; padding: .1rem .35rem;
            border-radius: 4px; transition: all .2s; flex-shrink: 0;
        }
        .project-card:hover .project-edit-btn,
        .project-card:hover .project-del-btn { opacity: 1; }
        .project-edit-btn:hover { background: #334155; color: #a5b4fc; }
        .project-del-btn:hover { background: #3b1a1a; color: #f87171; }

        .task-del-btn {
            margin-left: auto; font-size: .7rem; color: #475569;
            padding: .15rem .4rem; border-radius: 4px; transition: all .2s;
            opacity: 0;
        }
        .task-card:hover .task-del-btn { opacity: 1; }
        .task-del-btn:hover { background: #3b1a1a; color: #f87171; }

        .confirm-modal { text-align: center; }
        .confirm-modal p { color: #94a3b8; font-size: .875rem; margin-bottom: 1.5rem; }
        .btn-danger {
            flex: 1; padding: .7rem; background: #ef4444; border: none;
            border-radius: 6px; color: #fff; font-size: .9rem; font-weight: 600;
            cursor: pointer; transition: background .2s;
        }
        .btn-danger:hover { background: #dc2626; }

        .user-menu-trigger {
            display: flex; align-items: center; gap: .4rem;
            cursor: pointer; padding: .35rem .7rem;
            border: 1px solid #334155; border-radius: 6px;
            font-size: .85rem; transition: all .2s;
        }
        .user-menu-trigger:hover { border-color: #6366f1; color: #a5b4fc; }

        .user-menu {
            position: absolute; top: calc(100% + .5rem); right: 0;
            background: #1e293b; border: 1px solid #334155;
            border-radius: 8px; min-width: 200px; z-index: 50;
            box-shadow: 0 8px 24px rgba(0,0,0,.4);
        }

        .user-menu-item {
            padding: .65rem 1rem; font-size: .85rem; cursor: pointer;
            transition: background .15s; color: #cbd5e1;
        }
        .user-menu-item:hover { background: #0f172a; }
        .user-menu-item.danger { color: #f87171; }
        .user-menu-item.danger:hover { background: #3b1a1a; }
        .user-menu-divider { height: 1px; background: #334155; }

        .comment-item {
            background: #0f172a; border: 1px solid #1e293b;
            border-radius: 6px; padding: .65rem .75rem; margin-bottom: .5rem;
        }
        .comment-meta {
            display: flex; align-items: center; gap: .5rem;
            font-size: .7rem; color: #64748b; margin-bottom: .35rem;
        }
        .comment-author { font-weight: 600; color: #94a3b8; }
        .comment-body { font-size: .8rem; color: #cbd5e1; line-height: 1.5; }
        .comment-del {
            margin-left: auto; cursor: pointer; color: #475569;
            transition: color .2s;
        }
        .comment-del:hover { color: #f87171; }
    </style>
</head>
<body>

<!-- AUTH -->
<div id="auth-screen">
    <div class="auth-card">
        <h1>TaskFlow</h1>
        <p>Gestão de tarefas para times</p>
        <div class="tabs">
            <button class="tab active" onclick="switchTab('login')">Entrar</button>
            <button class="tab" onclick="switchTab('register')">Criar conta</button>
        </div>
        <div id="auth-error" class="error-msg"></div>
        <div id="login-form">
            <div class="form-group">
                <label>E-mail</label>
                <input type="email" id="login-email" placeholder="seu@email.com">
            </div>
            <div class="form-group">
                <label>Senha</label>
                <input type="password" id="login-password" placeholder="••••••••">
            </div>
            <button class="btn" onclick="login()">Entrar</button>
        </div>
        <div id="register-form" style="display:none">
            <div class="form-group">
                <label>Nome</label>
                <input type="text" id="reg-name" placeholder="Seu nome">
            </div>
            <div class="form-group">
                <label>E-mail</label>
                <input type="email" id="reg-email" placeholder="seu@email.com">
            </div>
            <div class="form-group">
                <label>Senha</label>
                <input type="password" id="reg-password" placeholder="Mínimo 8 caracteres">
            </div>
            <button class="btn" onclick="register()">Criar conta</button>
        </div>
        <div style="margin-top:1.5rem;padding-top:1.25rem;border-top:1px solid #1e293b;text-align:center">
            <a href="/api/documentation" target="_blank" style="font-size:.8rem;color:#6366f1;text-decoration:none;display:inline-flex;align-items:center;gap:.35rem;transition:color .2s">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Ver documentação da API
            </a>
        </div>
    </div>
</div>

<!-- APP -->
<div id="app-screen">
    <div class="topbar">
        <h1>⚡ TaskFlow</h1>
        <div style="display:flex;align-items:center;gap:.75rem;position:relative">
            <div class="user-menu-trigger" onclick="toggleUserMenu()">
                <span id="user-name"></span>
                <span style="color:#64748b;font-size:.7rem">▾</span>
            </div>
            <div id="user-menu" class="user-menu" style="display:none">
                <div class="user-menu-item" onclick="closeUserMenu();openChangePassword()">🔑 Alterar senha</div>
                <div class="user-menu-divider"></div>
                <div class="user-menu-item danger" onclick="closeUserMenu();openDeleteAccount()">🗑 Excluir minha conta</div>
                <div class="user-menu-divider"></div>
                <div class="user-menu-item" onclick="closeUserMenu();logout()">→ Sair</div>
            </div>
        </div>
    </div>
    <div class="layout">
        <div class="sidebar">
            <div class="sidebar-title">Workspaces</div>
            <div id="workspace-list"></div>
            <button class="new-workspace-btn" onclick="openModal('workspace')">+ Novo workspace</button>
        </div>
        <div class="main" id="main-content">
            <div class="main-empty">
                <svg width="48" height="48" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7h18M3 12h18M3 17h18"/>
                </svg>
                <p>Selecione um workspace</p>
            </div>
        </div>
    </div>
</div>

<!-- MODALS -->
<div id="modal-container"></div>

<script>
const API = '/api';
let token = localStorage.getItem('tf_token');
let user = JSON.parse(localStorage.getItem('tf_user') || 'null');
let currentWorkspace = null;
let currentProject = null;

// ─── BOOT ───────────────────────────────────────────────────────────────────
if (token && user) showApp();

async function api(method, path, body) {
    const opts = {
        method,
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
    };
    if (token) opts.headers['Authorization'] = `Bearer ${token}`;
    if (body) opts.body = JSON.stringify(body);
    const res = await fetch(API + path, opts);
    const data = await res.json().catch(() => ({}));
    if (!res.ok) throw data;
    return data;
}

// ─── AUTH ────────────────────────────────────────────────────────────────────
function switchTab(tab) {
    document.querySelectorAll('.tab').forEach((t, i) => t.classList.toggle('active', (i === 0) === (tab === 'login')));
    document.getElementById('login-form').style.display = tab === 'login' ? '' : 'none';
    document.getElementById('register-form').style.display = tab === 'register' ? '' : 'none';
    hideError();
}

function showError(msg) {
    const el = document.getElementById('auth-error');
    el.textContent = msg;
    el.style.display = 'block';
}

function hideError() { document.getElementById('auth-error').style.display = 'none'; }

async function login() {
    hideError();
    try {
        const data = await api('POST', '/auth/login', {
            email: document.getElementById('login-email').value,
            password: document.getElementById('login-password').value,
        });
        saveAuth(data);
        showApp();
    } catch (e) {
        showError(e.message || 'Credenciais inválidas');
    }
}

async function register() {
    hideError();
    const password = document.getElementById('reg-password').value;
    try {
        const data = await api('POST', '/auth/register', {
            name: document.getElementById('reg-name').value,
            email: document.getElementById('reg-email').value,
            password,
            password_confirmation: password,
        });
        saveAuth(data);
        showApp();
    } catch (e) {
        const msgs = e.errors ? Object.values(e.errors).flat().join(' ') : e.message;
        showError(msgs || 'Erro ao criar conta');
    }
}

async function logout() {
    try { await api('POST', '/auth/logout'); } catch {}
    localStorage.removeItem('tf_token');
    localStorage.removeItem('tf_user');
    token = null; user = null;
    document.getElementById('app-screen').style.display = 'none';
    document.getElementById('auth-screen').style.display = 'flex';
}

function saveAuth(data) {
    token = data.token;
    user = data.user;
    localStorage.setItem('tf_token', token);
    localStorage.setItem('tf_user', JSON.stringify(user));
}

// ─── APP ─────────────────────────────────────────────────────────────────────
function showApp() {
    document.getElementById('auth-screen').style.display = 'none';
    document.getElementById('app-screen').style.display = 'block';
    document.getElementById('user-name').textContent = user.name;
    loadWorkspaces();
}

async function loadWorkspaces() {
    const list = document.getElementById('workspace-list');
    list.innerHTML = '<div class="loading">Carregando...</div>';
    try {
        const workspaces = await api('GET', '/workspaces');
        list.innerHTML = '';
        if (!workspaces.length) {
            list.innerHTML = '<div style="font-size:.8rem;color:#475569;padding:.5rem">Nenhum workspace</div>';
            return;
        }
        workspaces.forEach(ws => {
            const el = document.createElement('div');
            el.className = 'workspace-item' + (currentWorkspace?.id === ws.id ? ' active' : '');
            el.innerHTML = `
                <div class="workspace-icon">${ws.name[0].toUpperCase()}</div>
                <span style="flex:1">${ws.name}</span>
                <span class="ws-edit-btn" onclick="event.stopPropagation();openEditWorkspace(${ws.id},'${ws.name.replace(/'/g,"\\'")}')">✎</span>
                <span class="ws-del-btn" onclick="event.stopPropagation();confirmDelete('workspace',${ws.id},'${ws.name.replace(/'/g,"\\'")}')">✕</span>
            `;
            el.onclick = () => selectWorkspace(ws);
            list.appendChild(el);
        });
    } catch { list.innerHTML = '<div class="loading">Erro ao carregar</div>'; }
}

async function selectWorkspace(ws) {
    currentWorkspace = ws;
    currentProject = null;
    document.querySelectorAll('.workspace-item').forEach(el =>
        el.classList.toggle('active', el.textContent.trim().startsWith(ws.name[0]) && el.textContent.includes(ws.name))
    );
    renderMain();
}

async function renderMain() {
    const main = document.getElementById('main-content');
    main.innerHTML = '<div class="loading">Carregando projetos...</div>';
    try {
        const projects = await api('GET', `/workspaces/${currentWorkspace.id}/projects`);
        let tasks = [];
        if (currentProject) {
            tasks = await api('GET', `/projects/${currentProject.id}/tasks`);
        }
        main.innerHTML = renderMainHTML(projects, tasks);
    } catch { main.innerHTML = '<div class="loading">Erro ao carregar</div>'; }
}

function renderMainHTML(projects, tasks) {
    const cols = { todo: [], in_progress: [], done: [] };
    tasks.forEach(t => (cols[t.status] || cols.todo).push(t));

    return `
        <div class="section-header">
            <h2>${currentWorkspace.name}</h2>
            <button class="add-btn" onclick="openModal('project')">+ Projeto</button>
        </div>
        <div class="projects-grid">
            ${projects.map(p => `
                <div class="project-card ${currentProject?.id === p.id ? 'active' : ''}" onclick="selectProject(${p.id}, '${escHtml(p.name)}', '${escHtml(p.description || '')}')">
                    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:.5rem">
                        <h3>${escHtml(p.name)}</h3>
                        <div style="display:flex;gap:.25rem" onclick="event.stopPropagation()">
                            <span class="project-edit-btn" onclick="openEditProject(${p.id},'${escHtml(p.name)}','${escHtml(p.description || '')}')">✎</span>
                            <span class="project-del-btn" onclick="confirmDelete('project',${p.id},'${escHtml(p.name)}')">✕</span>
                        </div>
                    </div>
                    <p>${escHtml(p.description || 'Sem descrição')}</p>
                </div>
            `).join('')}
        </div>
        ${currentProject ? `
        <div class="tasks-section">
            <div class="section-header">
                <h2>📋 ${escHtml(currentProject.name)}</h2>
                <button class="add-btn" onclick="openModal('task')">+ Task</button>
            </div>
            <div class="kanban">
                ${['todo','in_progress','done'].map(status => `
                    <div class="kanban-col col-${status}">
                        <div class="kanban-col-title">
                            <span class="col-dot"></span>
                            ${{ todo: 'A fazer', in_progress: 'Em progresso', done: 'Concluído' }[status]}
                            <span style="color:#475569">(${cols[status].length})</span>
                        </div>
                        ${cols[status].map(t => `
                            <div class="task-card" onclick="openEditTask(${t.id}, '${escHtml(t.title)}', '${t.status}', '${t.priority}', '${t.due_date || ''}', ${t.assigned_to || null})">
                                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:.5rem">
                                    <h4>${escHtml(t.title)}</h4>
                                    <span class="task-del-btn" onclick="event.stopPropagation();confirmDelete('task',${t.id},'${escHtml(t.title)}')">✕</span>
                                </div>
                                <div class="task-meta">
                                    <span class="badge badge-${t.priority}">${t.priority}</span>
                                    ${t.due_date ? `<span class="badge badge-date">${t.due_date.split('T')[0]}</span>` : ''}
                                    ${t.assignee ? `<span class="badge badge-date">👤 ${escHtml(t.assignee.name)}</span>` : ''}
                                    ${t.comments_count > 0 ? `<span class="badge badge-date">💬 ${t.comments_count}</span>` : ''}
                                </div>
                                <div class="status-buttons" onclick="event.stopPropagation()">
                                    <button class="status-btn todo ${t.status === 'todo' ? 'active-status' : ''}" onclick="updateTaskStatus(${t.id}, 'todo', '${escHtml(t.title)}', '${t.priority}')">A fazer</button>
                                    <button class="status-btn in_progress ${t.status === 'in_progress' ? 'active-status' : ''}" onclick="updateTaskStatus(${t.id}, 'in_progress', '${escHtml(t.title)}', '${t.priority}')">Em progresso</button>
                                    <button class="status-btn done ${t.status === 'done' ? 'active-status' : ''}" onclick="updateTaskStatus(${t.id}, 'done', '${escHtml(t.title)}', '${t.priority}')">Concluída</button>
                                </div>
                            </div>
                        `).join('')}
                        <button class="add-task-btn" onclick="openModal('task')">+ adicionar task</button>
                    </div>
                `).join('')}
            </div>
        </div>` : ''}
    `;
}

async function selectProject(id, name, description) {
    currentProject = { id, name, description };
    renderMain();
}

function openEditWorkspace(id, name) {
    const container = document.getElementById('modal-container');
    container.innerHTML = `
        <div class="modal-overlay" onclick="if(event.target===this)closeModal()">
            <div class="modal">
                <h3>Editar Workspace</h3>
                <div class="form-group"><label>Nome</label><input type="text" id="m-name" value="${name}"></div>
                <div class="modal-footer">
                    <button class="cancel-btn" onclick="closeModal()">Cancelar</button>
                    <button class="btn" onclick="saveEditWorkspace(${id})">Salvar</button>
                </div>
            </div>
        </div>`;
}

async function saveEditWorkspace(id) {
    const name = document.getElementById('m-name').value.trim();
    if (!name) return;
    try {
        await api('PUT', `/workspaces/${id}`, { name });
        if (currentWorkspace?.id === id) currentWorkspace.name = name;
        closeModal();
        loadWorkspaces();
        if (currentWorkspace?.id === id) renderMain();
    } catch {}
}

function openEditProject(id, name, description) {
    const container = document.getElementById('modal-container');
    container.innerHTML = `
        <div class="modal-overlay" onclick="if(event.target===this)closeModal()">
            <div class="modal">
                <h3>Editar Projeto</h3>
                <div class="form-group"><label>Nome</label><input type="text" id="m-name" value="${name}"></div>
                <div class="form-group"><label>Descrição</label><textarea id="m-desc">${description}</textarea></div>
                <div class="modal-footer">
                    <button class="cancel-btn" onclick="closeModal()">Cancelar</button>
                    <button class="btn" onclick="saveEditProject(${id})">Salvar</button>
                </div>
            </div>
        </div>`;
}

async function saveEditProject(id) {
    const name = document.getElementById('m-name').value.trim();
    const description = document.getElementById('m-desc').value.trim();
    if (!name) return;
    try {
        await api('PUT', `/projects/${id}`, { name, description });
        if (currentProject?.id === id) currentProject = { id, name, description };
        closeModal();
        renderMain();
    } catch {}
}

function escHtml(str) {
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// ─── MODALS ──────────────────────────────────────────────────────────────────
function openModal(type) {
    const container = document.getElementById('modal-container');
    const modals = {
        workspace: `
            <div class="modal">
                <h3>Novo Workspace</h3>
                <div class="form-group"><label>Nome</label><input type="text" id="m-name" placeholder="Nome do workspace"></div>
                <div class="modal-footer">
                    <button class="cancel-btn" onclick="closeModal()">Cancelar</button>
                    <button class="btn" onclick="createWorkspace()">Criar</button>
                </div>
            </div>`,
        project: `
            <div class="modal">
                <h3>Novo Projeto</h3>
                <div class="form-group"><label>Nome</label><input type="text" id="m-name" placeholder="Nome do projeto"></div>
                <div class="form-group"><label>Descrição</label><textarea id="m-desc" placeholder="Descrição (opcional)"></textarea></div>
                <div class="modal-footer">
                    <button class="cancel-btn" onclick="closeModal()">Cancelar</button>
                    <button class="btn" onclick="createProject()">Criar</button>
                </div>
            </div>`,
        task: `
            <div class="modal">
                <h3>Nova Task</h3>
                <div class="form-group"><label>Título</label><input type="text" id="m-title" placeholder="Título da task"></div>
                <div class="form-group"><label>Prioridade</label>
                    <select id="m-priority">
                        <option value="low">Baixa</option>
                        <option value="medium" selected>Média</option>
                        <option value="high">Alta</option>
                    </select>
                </div>
                <div class="form-group"><label>Responsável</label>
                    <select id="m-assignee"><option value="">Carregando membros...</option></select>
                </div>
                <div class="form-group"><label>Prazo</label><input type="date" id="m-due"></div>
                <div class="modal-footer">
                    <button class="cancel-btn" onclick="closeModal()">Cancelar</button>
                    <button class="btn" onclick="createTask()">Criar</button>
                </div>
            </div>`,
    };
    container.innerHTML = `<div class="modal-overlay" onclick="if(event.target===this)closeModal()">${modals[type]}</div>`;
    if (type === 'task') loadMembersIntoSelect('m-assignee', null);
}

function closeModal() { document.getElementById('modal-container').innerHTML = ''; }

async function createWorkspace() {
    const name = document.getElementById('m-name').value.trim();
    if (!name) return;
    try {
        await api('POST', '/workspaces', { name });
        closeModal();
        loadWorkspaces();
    } catch {}
}

async function createProject() {
    const name = document.getElementById('m-name').value.trim();
    const description = document.getElementById('m-desc').value.trim();
    if (!name) return;
    try {
        await api('POST', `/workspaces/${currentWorkspace.id}/projects`, { name, description });
        closeModal();
        renderMain();
    } catch {}
}

async function createTask() {
    const title = document.getElementById('m-title').value.trim();
    const priority = document.getElementById('m-priority').value;
    const due_date = document.getElementById('m-due').value || null;
    const assigned_to = document.getElementById('m-assignee')?.value || null;
    if (!title) return;
    try {
        await api('POST', `/projects/${currentProject.id}/tasks`, { title, priority, due_date, assigned_to: assigned_to || null });
        closeModal();
        renderMain();
    } catch {}
}

async function loadMembersIntoSelect(selectId, selectedId) {
    try {
        const ws = await api('GET', `/workspaces/${currentWorkspace.id}`);
        const members = ws.members || [];
        const sel = document.getElementById(selectId);
        if (!sel) return;
        sel.innerHTML = '<option value="">Sem responsável</option>' +
            members.map(m => `<option value="${m.id}" ${m.id == selectedId ? 'selected' : ''}>${m.name}</option>`).join('');
    } catch {}
}

function openEditTask(id, title, status, priority, due_date, assignedTo) {
    const container = document.getElementById('modal-container');
    container.innerHTML = `
        <div class="modal-overlay" onclick="if(event.target===this)closeModal()">
            <div class="modal" style="max-width:520px">
                <h3>Editar Task</h3>
                <div class="form-group"><label>Título</label><input type="text" id="m-title" value="${title}"></div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem">
                    <div class="form-group"><label>Status</label>
                        <select id="m-status">
                            <option value="todo" ${status === 'todo' ? 'selected' : ''}>A fazer</option>
                            <option value="in_progress" ${status === 'in_progress' ? 'selected' : ''}>Em progresso</option>
                            <option value="done" ${status === 'done' ? 'selected' : ''}>Concluída</option>
                        </select>
                    </div>
                    <div class="form-group"><label>Prioridade</label>
                        <select id="m-priority">
                            <option value="low" ${priority === 'low' ? 'selected' : ''}>Baixa</option>
                            <option value="medium" ${priority === 'medium' ? 'selected' : ''}>Média</option>
                            <option value="high" ${priority === 'high' ? 'selected' : ''}>Alta</option>
                        </select>
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem">
                    <div class="form-group"><label>Responsável</label>
                        <select id="m-assignee"><option value="">Carregando...</option></select>
                    </div>
                    <div class="form-group"><label>Prazo</label>
                        <input type="date" id="m-due" value="${due_date ? due_date.split('T')[0] : ''}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="cancel-btn" onclick="closeModal()">Cancelar</button>
                    <button class="btn" onclick="saveEditTask(${id})">Salvar</button>
                </div>
                <div style="margin-top:1.25rem;border-top:1px solid #334155;padding-top:1.25rem">
                    <div style="font-size:.8rem;font-weight:600;color:#94a3b8;margin-bottom:.75rem">💬 Comentários</div>
                    <div id="comments-list"><div class="loading">Carregando...</div></div>
                    <div style="display:flex;gap:.5rem;margin-top:.75rem">
                        <input type="text" id="m-comment" placeholder="Adicionar comentário..." style="flex:1;padding:.55rem .75rem;background:#0f172a;border:1px solid #334155;border-radius:6px;color:#f8fafc;font-size:.8rem;outline:none">
                        <button class="add-btn" onclick="addComment(${id})">Enviar</button>
                    </div>
                </div>
            </div>
        </div>`;
    loadMembersIntoSelect('m-assignee', assignedTo);
    loadComments(id);
}

async function saveEditTask(id) {
    const title = document.getElementById('m-title').value.trim();
    const status = document.getElementById('m-status').value;
    const priority = document.getElementById('m-priority').value;
    const due_date = document.getElementById('m-due').value || null;
    const assigned_to = document.getElementById('m-assignee')?.value || null;
    if (!title) return;
    try {
        await api('PUT', `/tasks/${id}`, { title, status, priority, due_date, assigned_to: assigned_to || null });
        closeModal();
        renderMain();
    } catch {}
}

async function loadComments(taskId) {
    const list = document.getElementById('comments-list');
    if (!list) return;
    try {
        const comments = await api('GET', `/tasks/${taskId}/comments`);
        if (!comments.length) {
            list.innerHTML = '<div style="font-size:.75rem;color:#475569">Nenhum comentário ainda.</div>';
            return;
        }
        list.innerHTML = comments.map(c => `
            <div class="comment-item">
                <div class="comment-meta">
                    <span class="comment-author">${escHtml(c.author?.name || 'Usuário')}</span>
                    <span>${new Date(c.created_at).toLocaleString('pt-BR')}</span>
                    ${c.user_id === user.id ? `<span class="comment-del" onclick="deleteComment(${c.id}, ${taskId})">✕</span>` : ''}
                </div>
                <div class="comment-body">${escHtml(c.body)}</div>
            </div>
        `).join('');
    } catch {
        list.innerHTML = '<div style="font-size:.75rem;color:#475569">Erro ao carregar comentários.</div>';
    }
}

async function addComment(taskId) {
    const input = document.getElementById('m-comment');
    const body = input.value.trim();
    if (!body) return;
    try {
        await api('POST', `/tasks/${taskId}/comments`, { body });
        input.value = '';
        loadComments(taskId);
    } catch {}
}

async function deleteComment(commentId, taskId) {
    try {
        await api('DELETE', `/comments/${commentId}`);
        loadComments(taskId);
    } catch {}
}

// ─── USER MENU ───────────────────────────────────────────────────────────────
function toggleUserMenu() {
    const menu = document.getElementById('user-menu');
    menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
}

function closeUserMenu() {
    document.getElementById('user-menu').style.display = 'none';
}

document.addEventListener('click', e => {
    if (!e.target.closest('.user-menu-trigger') && !e.target.closest('.user-menu')) {
        closeUserMenu();
    }
});

function openChangePassword() {
    document.getElementById('modal-container').innerHTML = `
        <div class="modal-overlay" onclick="if(event.target===this)closeModal()">
            <div class="modal">
                <h3>Alterar senha</h3>
                <div id="pwd-error" class="error-msg"></div>
                <div class="form-group"><label>Senha atual</label><input type="password" id="m-current-pwd" placeholder="••••••••"></div>
                <div class="form-group"><label>Nova senha</label><input type="password" id="m-new-pwd" placeholder="Mínimo 8 caracteres"></div>
                <div class="form-group"><label>Confirmar nova senha</label><input type="password" id="m-confirm-pwd" placeholder="••••••••"></div>
                <div class="modal-footer">
                    <button class="cancel-btn" onclick="closeModal()">Cancelar</button>
                    <button class="btn" onclick="saveChangePassword()">Salvar</button>
                </div>
            </div>
        </div>`;
}

async function saveChangePassword() {
    const current = document.getElementById('m-current-pwd').value;
    const password = document.getElementById('m-new-pwd').value;
    const confirmation = document.getElementById('m-confirm-pwd').value;
    const errEl = document.getElementById('pwd-error');
    errEl.style.display = 'none';

    if (password !== confirmation) {
        errEl.textContent = 'As senhas não coincidem.';
        errEl.style.display = 'block';
        return;
    }

    try {
        await api('PUT', '/auth/password', {
            current_password: current,
            password,
            password_confirmation: confirmation,
        });
        closeModal();
    } catch (e) {
        errEl.textContent = e.message || 'Erro ao alterar senha.';
        errEl.style.display = 'block';
    }
}

function openDeleteAccount() {
    document.getElementById('modal-container').innerHTML = `
        <div class="modal-overlay" onclick="if(event.target===this)closeModal()">
            <div class="modal confirm-modal">
                <h3>Excluir minha conta</h3>
                <p>Esta ação é <strong style="color:#f87171">irreversível</strong>. Todos os seus workspaces, projetos e tarefas serão excluídos.<br><br>Digite sua senha para confirmar:</p>
                <div id="del-error" class="error-msg"></div>
                <div class="form-group" style="text-align:left"><label>Senha</label><input type="password" id="m-del-pwd" placeholder="••••••••"></div>
                <div class="modal-footer">
                    <button class="cancel-btn" onclick="closeModal()">Cancelar</button>
                    <button class="btn-danger" onclick="confirmDeleteAccount()">Excluir minha conta</button>
                </div>
            </div>
        </div>`;
}

async function confirmDeleteAccount() {
    const password = document.getElementById('m-del-pwd').value;
    const errEl = document.getElementById('del-error');
    errEl.style.display = 'none';

    try {
        await api('DELETE', '/auth/account', { password });
        localStorage.removeItem('tf_token');
        localStorage.removeItem('tf_user');
        token = null; user = null;
        closeModal();
        document.getElementById('app-screen').style.display = 'none';
        document.getElementById('auth-screen').style.display = 'flex';
    } catch (e) {
        errEl.textContent = e.message || 'Senha incorreta.';
        errEl.style.display = 'block';
    }
}

function confirmDelete(type, id, name) {
    const labels = { workspace: 'workspace', project: 'projeto', task: 'task' };
    const container = document.getElementById('modal-container');
    container.innerHTML = `
        <div class="modal-overlay" onclick="if(event.target===this)closeModal()">
            <div class="modal confirm-modal">
                <h3>Excluir ${labels[type]}</h3>
                <p>Tem certeza que deseja excluir <strong style="color:#f8fafc">${name}</strong>?<br>Esta ação não pode ser desfeita.</p>
                <div class="modal-footer">
                    <button class="cancel-btn" onclick="closeModal()">Cancelar</button>
                    <button class="btn-danger" onclick="deleteItem('${type}', ${id})">Excluir</button>
                </div>
            </div>
        </div>`;
}

async function deleteItem(type, id) {
    try {
        if (type === 'workspace') {
            await api('DELETE', `/workspaces/${id}`);
            if (currentWorkspace?.id === id) { currentWorkspace = null; currentProject = null; }
            closeModal();
            loadWorkspaces();
            if (!currentWorkspace) {
                document.getElementById('main-content').innerHTML = `
                    <div class="main-empty">
                        <svg width="48" height="48" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7h18M3 12h18M3 17h18"/>
                        </svg>
                        <p>Selecione um workspace</p>
                    </div>`;
            }
        } else if (type === 'project') {
            await api('DELETE', `/projects/${id}`);
            if (currentProject?.id === id) currentProject = null;
            closeModal();
            renderMain();
        } else if (type === 'task') {
            await api('DELETE', `/tasks/${id}`);
            closeModal();
            renderMain();
        }
    } catch {}
}

async function updateTaskStatus(id, status, title, priority) {
    try {
        await api('PUT', `/tasks/${id}`, { title, status, priority });
        renderMain();
    } catch {}
}
</script>
</body>
</html>
