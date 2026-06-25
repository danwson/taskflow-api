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
        }

        .task-card h4 { font-size: .8rem; font-weight: 500; margin-bottom: .4rem; }

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
    </div>
</div>

<!-- APP -->
<div id="app-screen">
    <div class="topbar">
        <h1>⚡ TaskFlow</h1>
        <div style="display:flex;align-items:center;gap:1rem">
            <span id="user-name"></span>
            <button class="logout-btn" onclick="logout()">Sair</button>
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
            el.innerHTML = `<div class="workspace-icon">${ws.name[0].toUpperCase()}</div>${ws.name}`;
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
                <div class="project-card ${currentProject?.id === p.id ? 'active' : ''}" onclick="selectProject(${p.id}, '${escHtml(p.name)}')">
                    <h3>${escHtml(p.name)}</h3>
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
                            <div class="task-card">
                                <h4>${escHtml(t.title)}</h4>
                                <div class="task-meta">
                                    <span class="badge badge-${t.priority}">${t.priority}</span>
                                    ${t.due_date ? `<span class="badge badge-date">${t.due_date.split('T')[0]}</span>` : ''}
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

async function selectProject(id, name) {
    currentProject = { id, name };
    renderMain();
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
                <div class="form-group"><label>Prazo</label><input type="date" id="m-due"></div>
                <div class="modal-footer">
                    <button class="cancel-btn" onclick="closeModal()">Cancelar</button>
                    <button class="btn" onclick="createTask()">Criar</button>
                </div>
            </div>`,
    };
    container.innerHTML = `<div class="modal-overlay" onclick="if(event.target===this)closeModal()">${modals[type]}</div>`;
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
    if (!title) return;
    try {
        await api('POST', `/projects/${currentProject.id}/tasks`, { title, priority, due_date });
        closeModal();
        renderMain();
    } catch {}
}
</script>
</body>
</html>
