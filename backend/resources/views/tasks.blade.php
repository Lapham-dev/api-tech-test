<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Agenda Digital (DEMO)</title>

  <!-- Bootstrap 5 (CDN) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body { background: #0f1115; color: #e9ecef; }
    .card, .modal-content { background: #151922; border: 1px solid rgba(255,255,255,.08); }
    .muted { color: rgba(233,236,239,.65); }

    .kanban { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; }
    @media (max-width: 992px) { .kanban { grid-template-columns: 1fr; } }

    .colHead { display:flex; align-items:center; justify-content:space-between; gap:10px; }
    .badge-soft { border: 1px solid rgba(255,255,255,.12); background: rgba(255,255,255,.06); color: #e9ecef; }

    .dropzone {
      min-height: 280px;
      border: 1px dashed rgba(255,255,255,.18);
      border-radius: 12px;
      padding: 10px;
      background: rgba(255,255,255,.02);
      transition: .12s ease;
    }
    .dropzone.dragover {
      border-color: rgba(13,110,253,.8);
      background: rgba(13,110,253,.10);
      transform: translateY(-1px);
    }

    .task {
      border: 1px solid rgba(255,255,255,.10);
      background: rgba(255,255,255,.04);
      border-radius: 12px;
      padding: 10px;
      margin-bottom: 10px;
      cursor: grab;
      user-select: none;
    }
    .task:active { cursor: grabbing; }
    .taskTitle { font-weight: 600; }
    .taskMeta { font-size: 12px; color: rgba(233,236,239,.65); }

    .btn-xs { padding: .25rem .5rem; font-size: .8rem; }
    .lineThrough { text-decoration: line-through; opacity: .65; }

    .topbar { display:flex; align-items:center; justify-content:space-between; gap: 12px; }
    .statusPill { font-size: 12px; }
  </style>
</head>

<body class="py-4">
  <div class="container">

    <div class="topbar mb-3">
      <div>
        <h1 class="h3 m-0">Agenda Digital (DEMO)</h1>
        <div class="muted small"> Prueba tecnica para Fabian Lopez la misma incluye un sistema de agenda con 3 columnas + drag & drop + persistencia de status</div>
      </div>
      <div class="d-flex align-items-center gap-2">
        <span class="badge statusPill badge-soft" id="apiStatus">Conectando...</span>
        <button class="btn btn-outline-light btn-sm" onclick="loadAll()">Refrescar</button>
      </div>
    </div>

    <div class="card mb-3">
      <div class="card-body">
        <form id="createForm" class="row g-2">
          <div class="col-md-9">
            <input type="text" id="title" class="form-control"
                   placeholder="Nueva tarea..." required maxlength="255">
          </div>
          <div class="col-md-3 d-grid">
            <button class="btn btn-primary" type="submit">Agregar</button>
          </div>
        </form>

        <div class="form-text mt-2">
          API base: <code id="apiBase"></code>
        </div>
      </div>
    </div>

    <div class="kanban">

      <!-- BACKLOG -->
      <div class="card">
        <div class="card-body">
          <div class="colHead mb-2">
            <div class="d-flex align-items-center gap-2">
              <strong>Tareas Anuales</strong>
              <span class="badge badge-soft" id="count_backlog">0</span>
            </div>
           
          </div>
          <div class="dropzone" id="zone_backlog"
               ondragover="onDragOver(event)"
               ondragleave="onDragLeave(event)"
               ondrop="onDrop(event, 'backlog')">
            
          </div>
        </div>
      </div>

      <!-- IN PROGRESS -->
      <div class="card">
        <div class="card-body">
          <div class="colHead mb-2">
            <div class="d-flex align-items-center gap-2">
              <strong>Por hacer</strong>
              <span class="badge badge-soft" id="count_in_progress">0</span>
            </div>
            
          </div>
          <div class="dropzone" id="zone_in_progress"
               ondragover="onDragOver(event)"
               ondragleave="onDragLeave(event)"
               ondrop="onDrop(event, 'in_progress')">
            
          </div>
        </div>
      </div>

      <!-- DONE -->
      <div class="card">
        <div class="card-body">
          <div class="colHead mb-2">
            <div class="d-flex align-items-center gap-2">
              <strong>Terminadas</strong>
              <span class="badge badge-soft" id="count_done">0</span>
            </div>
            
          </div>
          <div class="dropzone" id="zone_done"
               ondragover="onDragOver(event)"
               ondragleave="onDragLeave(event)"
               ondrop="onDrop(event, 'done')">
            
          </div>
        </div>
      </div>

    </div>

    <div class="mt-3 muted small">
      Tips: creá una tarea (entra en Tareas Anuales), las tareas se pueden agarrar con el mouse y llevarlas a (Por Hacer) en caso que sean tareas prioritarias,
      una vez terminadas las tareas arrastralas a (Terminadas).
    </div>

  </div>

<script>
  // Base relativa (mismo host/puerto)
  const API = '/api';
  document.getElementById('apiBase').textContent = location.origin + API;

  const apiStatusEl = document.getElementById('apiStatus');

  const zones = {
    backlog: document.getElementById('zone_backlog'),
    in_progress: document.getElementById('zone_in_progress'),
    done: document.getElementById('zone_done'),
  };

  const counts = {
    backlog: document.getElementById('count_backlog'),
    in_progress: document.getElementById('count_in_progress'),
    done: document.getElementById('count_done'),
  };

  function setApiStatus(ok, text) {
    apiStatusEl.textContent = text;
    apiStatusEl.style.borderColor = ok ? 'rgba(25,135,84,.6)' : 'rgba(220,53,69,.7)';
    apiStatusEl.style.background = ok ? 'rgba(25,135,84,.12)' : 'rgba(220,53,69,.12)';
  }

  async function apiFetch(path, options = {}) {
    const res = await fetch(API + path, {
      headers: { 'Content-Type': 'application/json', ...(options.headers || {}) },
      ...options,
    });

    if (!res.ok) {
      const txt = await res.text();
      throw new Error(`HTTP ${res.status}: ${txt.slice(0, 300)}`);
    }

    const contentType = res.headers.get('content-type') || '';
    if (contentType.includes('application/json')) return await res.json();
    return null;
  }

  function normalizeTasks(payload) {
    // Soporta [] o {data: []}
    if (Array.isArray(payload)) return payload;
    if (payload && Array.isArray(payload.data)) return payload.data;
    return [];
  }

  function clearZones() {
    for (const k of Object.keys(zones)) {
      zones[k].innerHTML = '';
    }
  }

  function render(tasks) {
    clearZones();

    const byStatus = {
      backlog: [],
      in_progress: [],
      done: [],
    };

    for (const t of tasks) {
      const st = (t.status || 'backlog');
      if (byStatus[st]) byStatus[st].push(t);
      else byStatus.backlog.push(t); // cualquier cosa rara va a backlog
    }

    counts.backlog.textContent = byStatus.backlog.length;
    counts.in_progress.textContent = byStatus.in_progress.length;
    counts.done.textContent = byStatus.done.length;

    // si una columna queda vacía, meto un hint
    for (const st of Object.keys(byStatus)) {
      if (byStatus[st].length === 0) {
        zones[st].innerHTML = `<div class="muted small">No hay tareas.</div>`;
      } else {
        zones[st].innerHTML = byStatus[st].map(taskCard).join('');
      }
    }
  }

  function taskCard(t) {
    const doneChecked = t.done ? 'checked' : '';
    const titleClass = t.done ? 'lineThrough' : '';
    const safeTitle = escapeHtml(t.title ?? '');

    return `
      <div class="task" draggable="true"
           ondragstart="onDragStart(event, ${t.id})"
           data-id="${t.id}">
        <div class="d-flex align-items-start justify-content-between gap-2">
          <div class="flex-grow-1">
            <div class="d-flex align-items-center gap-2 mb-1">
              <div class="taskTitle ${titleClass}">${safeTitle}</div>
            </div>
            <div class="taskMeta">Tarea numero : ${t.id} • <-- (Numero de tareas anotadas) </div>
          </div>

          <div class="d-flex gap-2">
            <button class="btn btn-outline-light btn-xs"
                    onclick="renameTask(${t.id}, '${escapeAttr(t.title ?? '')}')">Renombrar</button>
            <button class="btn btn-outline-danger btn-xs"
                    onclick="deleteTask(${t.id})">Borrar</button>
          </div>
        </div>
      </div>
    `;
  }

  // ====== Drag & Drop ======

  function onDragStart(ev, taskId) {
    ev.dataTransfer.setData('text/plain', String(taskId));
    ev.dataTransfer.effectAllowed = 'move';
  }

  function onDragOver(ev) {
    ev.preventDefault();
    ev.currentTarget.classList.add('dragover');
  }

  function onDragLeave(ev) {
    ev.currentTarget.classList.remove('dragover');
  }

  async function onDrop(ev, newStatus) {
    ev.preventDefault();
    ev.currentTarget.classList.remove('dragover');

    const taskId = ev.dataTransfer.getData('text/plain');
    if (!taskId) return;

    try {
      await apiFetch(`/tasks/${taskId}`, {
        method: 'PATCH',
        body: JSON.stringify({ status: newStatus }),
      });
      await loadAll();
    } catch (e) {
      alert(e.message);
    }
  }

  // ====== CRUD ======

  async function loadAll() {
    try {
      // ping
      await apiFetch('/ping', { method: 'GET' });
      setApiStatus(true, 'API OK');

      const payload = await apiFetch('/tasks', { method: 'GET' });
      const tasks = normalizeTasks(payload);
      render(tasks);
    } catch (e) {
      setApiStatus(false, 'API ERROR');
      clearZones();
      for (const st of Object.keys(zones)) {
        zones[st].innerHTML = `<div class="text-danger small">${escapeHtml(e.message)}</div>`;
      }
    }
  }

  // Crear (default backlog)
  document.getElementById('createForm').addEventListener('submit', async (ev) => {
    ev.preventDefault();
    const titleEl = document.getElementById('title');
    const title = titleEl.value.trim();
    if (!title) return;

    try {
      await apiFetch('/tasks', {
        method: 'POST',
        body: JSON.stringify({ title, status: 'backlog' }),
      });
      titleEl.value = '';
      await loadAll();
    } catch (e) {
      alert(e.message);
    }
  });

  async function toggleDone(id, done) {
    try {
      await apiFetch(`/tasks/${id}`, {
        method: 'PATCH',
        body: JSON.stringify({ done }),
      });
      await loadAll();
    } catch (e) {
      alert(e.message);
      await loadAll();
    }
  }

  async function deleteTask(id) {
    if (!confirm('¿Borrar tarea?')) return;
    try {
      await apiFetch(`/tasks/${id}`, { method: 'DELETE' });
      await loadAll();
    } catch (e) {
      alert(e.message);
    }
  }

  async function renameTask(id, oldTitle) {
    const title = prompt('Nuevo título:', oldTitle);
    if (title === null) return;
    const clean = title.trim();
    if (!clean) return;

    try {
      await apiFetch(`/tasks/${id}`, {
        method: 'PATCH',
        body: JSON.stringify({ title: clean }),
      });
      await loadAll();
    } catch (e) {
      alert(e.message);
    }
  }

  // ====== helpers de escape ======
  function escapeHtml(str) {
    return String(str).replace(/[&<>"']/g, s => ({
      '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
    }[s]));
  }
  function escapeAttr(str) {
    return String(str).replace(/'/g, "\\'");
  }

  // init
  loadAll();
</script>

</body>
</html>
