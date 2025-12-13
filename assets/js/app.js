/* =========================
   NoteCore - app.js (CLEAN + UPDATED)
   Notes: shows "Last edited"
   Todos: supports due_time
   ========================= */

async function api(url, options = {}) {
  const res = await fetch(url, {
    headers: { "Content-Type": "application/json" },
    ...options,
  });

  const text = await res.text();
  try {
    return JSON.parse(text);
  } catch {
    return { ok: false, error: "Invalid server response." };
  }
}

function qs(id) {
  return document.getElementById(id);
}

function escapeHtml(s = "") {
  return String(s).replace(/[&<>"']/g, (c) => ({
    "&": "&amp;",
    "<": "&lt;",
    ">": "&gt;",
    '"': "&quot;",
    "'": "&#39;",
  }[c]));
}

function fmtDateTime(dt) {
  if (!dt) return "";
  const s = String(dt).replace("T", " ");
  return s.length >= 16 ? s.slice(0, 16) : s;
}

function timeHHMM(t) {
  if (!t) return "";
  // accepts "HH:MM:SS" or "HH:MM"
  const s = String(t);
  return s.length >= 5 ? s.slice(0, 5) : s;
}

function dueText(date, time) {
  if (!date) return "No due date";
  const tt = timeHHMM(time);
  return tt ? `${date} • ${tt}` : date;
}

/* =========================
   Global Action Dialog
   ========================= */

function ncDialogShow({ title = "Working...", sub = "Please wait...", state = "loading", duration = 1100 } = {}) {
  const root = qs("ncDialog");
  if (!root) return Promise.resolve(true);

  root.style.display = "block";

  const iconWrap = qs("ncDialogIcon");
  const titleEl = qs("ncDialogTitle");
  const subEl = qs("ncDialogSub");
  const fill = qs("ncDialogBar");

  if (titleEl) titleEl.textContent = title;
  if (subEl) subEl.textContent = sub;

  if (iconWrap) {
    iconWrap.innerHTML = "";
    if (state === "loading") {
      const sp = document.createElement("div");
      sp.className = "nc-spinner";
      iconWrap.appendChild(sp);
    } else if (state === "success") {
      const c = document.createElement("div");
      c.className = "nc-check";
      c.textContent = "✓";
      iconWrap.appendChild(c);
    } else {
      const x = document.createElement("div");
      x.className = "nc-x";
      x.textContent = "✕";
      iconWrap.appendChild(x);
    }
  }

  if (fill) {
    fill.style.transition = "none";
    fill.style.transform = "scaleX(0)";
    requestAnimationFrame(() => {
      fill.style.transition = `transform ${duration}ms linear`;
      fill.style.transform = "scaleX(1)";
    });
  }

  return new Promise((resolve) => {
    setTimeout(() => {
      const card = root.querySelector(".nc-dialog-card");
      if (card) card.classList.add("nc-hide");

      setTimeout(() => {
        root.style.display = "none";
        if (card) card.classList.remove("nc-hide");
        resolve(true);
      }, 180);
    }, duration);
  });
}

function ncSetBusy(el, busy) {
  if (!el) return;
  el.disabled = !!busy;
}

async function ncActionFlow(promiseFn, msgs, opts = {}) {
  const buttons = opts.buttons || [];
  buttons.forEach((b) => ncSetBusy(b, true));

  ncDialogShow({
    title: msgs.pendingTitle || "Working...",
    sub: msgs.pendingSub || "Please wait...",
    state: "loading",
    duration: 700,
  });

  try {
    const result = await promiseFn();

    await ncDialogShow({
      title: msgs.successTitle || "Done!",
      sub: msgs.successSub || "Success.",
      state: "success",
      duration: 950,
    });

    return result;
  } catch (e) {
    await ncDialogShow({
      title: msgs.errorTitle || "Something went wrong",
      sub: msgs.errorSub || "Please try again.",
      state: "error",
      duration: 1200,
    });
    throw e;
  } finally {
    buttons.forEach((b) => ncSetBusy(b, false));
  }
}

/* =========================
   Confirm Modal
   ========================= */

function ncConfirm({
  title = "Confirm",
  message = "Are you sure?",
  confirmText = "Delete",
  cancelText = "Cancel",
  danger = true,
} = {}) {
  return new Promise((resolve) => {
    const root = qs("ncConfirm");
    if (!root) return resolve(false);

    const t = qs("ncConfirmTitle");
    const m = qs("ncConfirmMsg");
    const okBtn = qs("ncConfirmOk");
    const cancelBtn = qs("ncConfirmCancel");

    if (t) t.textContent = title;
    if (m) m.textContent = message;

    if (okBtn) okBtn.textContent = confirmText;
    if (cancelBtn) cancelBtn.textContent = cancelText;

    if (okBtn) okBtn.classList.toggle("danger", !!danger);

    const cleanup = (val) => {
      root.style.display = "none";
      if (okBtn) okBtn.onclick = null;
      if (cancelBtn) cancelBtn.onclick = null;
      root.onclick = null;
      resolve(val);
    };

    root.style.display = "block";

    if (okBtn) okBtn.onclick = () => cleanup(true);
    if (cancelBtn) cancelBtn.onclick = () => cleanup(false);

    root.onclick = (e) => {
      if (e.target && e.target.classList && e.target.classList.contains("nc-confirm-backdrop")) cleanup(false);
    };
  });
}

/* =========================
   NOTES (notes.php + calendar.php)
   ========================= */

async function loadNotes() {
  const list = qs("notesList");
  if (!list) return;

  const q = (qs("noteSearch")?.value || "").trim();
  const data = await api(`../api/notes.php?q=${encodeURIComponent(q)}`);
  list.innerHTML = "";

  (data.notes || []).forEach((n) => {
    const row = document.createElement("div");
    row.className = "item";
    row.innerHTML = `
      <div style="min-width:0;">
        <b title="${escapeHtml(n.title)}" style="display:block; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
          ${escapeHtml(n.title)}
        </b>
        <div style="font-size:12px; opacity:.75;">
          ${escapeHtml(n.note_date || "")}
          ${n.updated_at ? ` • Last edited: ${escapeHtml(fmtDateTime(n.updated_at))}` : ""}
        </div>
      </div>
      <div style="display:flex; gap:8px;">
        <button class="iconbtn" data-edit-note="${n.id}">✏️</button>
        <button class="iconbtn danger" data-del-note="${n.id}">🗑️</button>
      </div>
    `;
    list.appendChild(row);
  });

  list.querySelectorAll("[data-edit-note]").forEach((btn) => {
    btn.addEventListener("click", () => {
      const id = Number(btn.dataset.editNote);
      const note = (data.notes || []).find((x) => Number(x.id) === id);
      openNote(note);
    });
  });

  list.querySelectorAll("[data-del-note]").forEach((btn) => {
    btn.addEventListener("click", async () => {
      const id = Number(btn.dataset.delNote);

      const yes = await ncConfirm({
        title: "Delete note?",
        message: "This will permanently remove the note.",
        confirmText: "Delete",
        cancelText: "Cancel",
        danger: true,
      });
      if (!yes) return;

      await ncActionFlow(
        () => api("../api/notes.php", { method: "DELETE", body: JSON.stringify({ id }) }),
        {
          pendingTitle: "Deleting note...",
          pendingSub: "Removing it safely",
          successTitle: "Deleted!",
          successSub: "Your note was deleted.",
          errorTitle: "Delete failed",
          errorSub: "Please try again.",
        },
        { buttons: [btn] }
      );

      await refreshCalendarIfPresent();
      loadNotes();
    });
  });
}

function openNote(note = null) {
  if (!qs("noteModal")) return;

  qs("noteModal").style.display = "block";
  qs("noteId").value = note?.id || "";
  qs("noteTitle").value = note?.title || "";
  qs("noteContent").value = note?.content || "";
  qs("noteDate").value = note?.note_date || new Date().toISOString().slice(0, 10);
}

function closeNote() {
  if (qs("noteModal")) qs("noteModal").style.display = "none";
}

async function saveNote() {
  const btn = qs("saveNote");

  const id = qs("noteId")?.value || "";
  const payload = {
    id: id ? Number(id) : undefined,
    title: (qs("noteTitle")?.value || "").trim(),
    content: (qs("noteContent")?.value || "").trim(),
    note_date: (qs("noteDate")?.value || new Date().toISOString().slice(0, 10)),
  };

  if (!payload.title) return alert("Title required.");
  if (!payload.content) return alert("Content required.");

  if (id) {
    await ncActionFlow(
      () => api("../api/notes.php", { method: "PUT", body: JSON.stringify(payload) }),
      {
        pendingTitle: "Updating note...",
        pendingSub: "Saving your changes",
        successTitle: "Updated!",
        successSub: "Your note was updated successfully.",
        errorTitle: "Update failed",
        errorSub: "Please try again.",
      },
      { buttons: [btn] }
    );
  } else {
    await ncActionFlow(
      () => api("../api/notes.php", { method: "POST", body: JSON.stringify(payload) }),
      {
        pendingTitle: "Creating note...",
        pendingSub: "Adding it to your notes",
        successTitle: "Created!",
        successSub: "Your note was created successfully.",
        errorTitle: "Create failed",
        errorSub: "Please try again.",
      },
      { buttons: [btn] }
    );
  }

  closeNote();
  loadNotes();
  await refreshCalendarIfPresent();
}

/* =========================
   TODOS (todos.php + calendar.php)
   ========================= */

async function loadTodos() {
  const list = qs("todosList");
  if (!list) return;

  const q = (qs("todoSearch")?.value || "").trim();
  const data = await api(`../api/todos.php?q=${encodeURIComponent(q)}`);
  list.innerHTML = "";

  (data.todos || []).forEach((t) => {
    const done = Number(t.is_done) === 1;
    const row = document.createElement("div");
    row.className = "item";
    row.innerHTML = `
      <div style="min-width:0;">
        <div style="display:flex; align-items:center; gap:10px;">
          <input type="checkbox" data-done-todo="${t.id}" ${done ? "checked" : ""} />
          <b style="text-decoration:${done ? "line-through" : "none"}; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
            ${escapeHtml(t.title)}
          </b>
        </div>
        <div style="font-size:12px; opacity:.75; margin-left:32px;">
          Due: ${escapeHtml(dueText(t.due_date, t.due_time))}
        </div>
      </div>
      <div style="display:flex; gap:8px;">
        <button class="iconbtn" data-edit-todo="${t.id}">✏️</button>
        <button class="iconbtn danger" data-del-todo="${t.id}">🗑️</button>
      </div>
    `;
    list.appendChild(row);
  });

  // toggle done
  list.querySelectorAll("[data-done-todo]").forEach((cb) => {
    cb.addEventListener("change", async () => {
      const id = Number(cb.dataset.doneTodo);

      await ncActionFlow(
        () => api("../api/todos.php", { method: "PUT", body: JSON.stringify({ id, is_done: cb.checked }) }),
        {
          pendingTitle: cb.checked ? "Completing..." : "Reopening...",
          pendingSub: "Updating status",
          successTitle: "Done!",
          successSub: cb.checked ? "Task marked as complete." : "Task marked as active.",
        },
        { buttons: [cb] }
      );

      loadTodos();
      await refreshCalendarIfPresent();
    });
  });

  // edit
  list.querySelectorAll("[data-edit-todo]").forEach((btn) => {
    btn.addEventListener("click", () => {
      const id = Number(btn.dataset.editTodo);
      const todo = (data.todos || []).find((x) => Number(x.id) === id);
      openTodo(todo);
    });
  });

  // delete
  list.querySelectorAll("[data-del-todo]").forEach((btn) => {
    btn.addEventListener("click", async () => {
      const id = Number(btn.dataset.delTodo);

      const yes = await ncConfirm({
        title: "Delete task?",
        message: "This will permanently remove the task.",
        confirmText: "Delete",
        cancelText: "Cancel",
        danger: true,
      });
      if (!yes) return;

      await ncActionFlow(
        () => api("../api/todos.php", { method: "DELETE", body: JSON.stringify({ id }) }),
        {
          pendingTitle: "Deleting task...",
          pendingSub: "Removing it safely",
          successTitle: "Deleted!",
          successSub: "Task deleted successfully.",
        },
        { buttons: [btn] }
      );

      loadTodos();
      await refreshCalendarIfPresent();
    });
  });
}

function openTodo(todo = null) {
  if (!qs("todoModal")) return;

  qs("todoModal").style.display = "block";
  qs("todoId").value = todo?.id || "";
  qs("todoTitle").value = todo?.title || "";

  // Due date (optional). If creating from Calendar, it passes due_date.
  const dateEl = qs("todoDueDate");
  if (dateEl) dateEl.value = todo?.due_date || "";

  // Due time (optional)
  const timeEl = qs("todoDueTime");
  if (timeEl) timeEl.value = todo?.due_time ? timeHHMM(todo.due_time) : "";
}

function closeTodo() {
  if (qs("todoModal")) qs("todoModal").style.display = "none";
}

async function saveTodo() {
  const btn = qs("saveTodo");

  const id = qs("todoId")?.value || "";
  const payload = {
    id: id ? Number(id) : undefined,
    title: (qs("todoTitle")?.value || "").trim(),
    due_date: (qs("todoDueDate")?.value || null),
    due_time: (qs("todoDueTime")?.value || null), // "HH:MM"
  };

  if (!payload.title) return alert("Task title required.");

  if (!payload.due_date) payload.due_date = null;
  if (!payload.due_time) payload.due_time = null;

  if (id) {
    await ncActionFlow(
      () => api("../api/todos.php", { method: "PUT", body: JSON.stringify(payload) }),
      {
        pendingTitle: "Updating task...",
        pendingSub: "Saving your changes",
        successTitle: "Updated!",
        successSub: "Task updated successfully.",
      },
      { buttons: [btn] }
    );
  } else {
    await ncActionFlow(
      () => api("../api/todos.php", { method: "POST", body: JSON.stringify(payload) }),
      {
        pendingTitle: "Creating task...",
        pendingSub: "Adding it to your list",
        successTitle: "Added!",
        successSub: "Task added successfully.",
      },
      { buttons: [btn] }
    );
  }

  closeTodo();
  loadTodos();
  await refreshCalendarIfPresent();
}

/* =========================
   CALENDAR (calendar.php)
   ========================= */

let calYear = null;
let calMonth = null; // 0-11
let calSelectedDate = null; // "YYYY-MM-DD"
let calCounts = {}; // { "YYYY-MM-DD": { notes:n, todos:t } }

function pad2(n) {
  return String(n).padStart(2, "0");
}
function ymd(d) {
  return `${d.getFullYear()}-${pad2(d.getMonth() + 1)}-${pad2(d.getDate())}`;
}
function monthRange(year, month0) {
  const start = new Date(year, month0, 1);
  const end = new Date(year, month0 + 1, 0);
  return { start: ymd(start), end: ymd(end) };
}

async function loadCalendarMonthCounts() {
  if (!qs("calendarGrid")) return;

  const r = monthRange(calYear, calMonth);

  const [notesRes, todosRes] = await Promise.all([
    api(`../api/notes.php?start=${encodeURIComponent(r.start)}&end=${encodeURIComponent(r.end)}`),
    api(`../api/todos.php?start=${encodeURIComponent(r.start)}&end=${encodeURIComponent(r.end)}`),
  ]);

  const map = {};

  (notesRes.notes || []).forEach((n) => {
    const d = n.note_date;
    map[d] ??= { notes: 0, todos: 0 };
    map[d].notes += 1;
  });

  (todosRes.todos || []).forEach((t) => {
    const d = t.due_date || null;
    if (!d) return;
    map[d] ??= { notes: 0, todos: 0 };
    map[d].todos += 1;
  });

  calCounts = map;
}

function buildCalendar() {
  const grid = qs("calendarGrid");
  if (!grid) return;

  const now = new Date();
  if (calYear === null || calMonth === null) {
    calYear = now.getFullYear();
    calMonth = now.getMonth();
  }

  const first = new Date(calYear, calMonth, 1);
  const last = new Date(calYear, calMonth + 1, 0);

  const label = qs("calLabel");
  if (label) {
    label.textContent = first.toLocaleString(undefined, { month: "long", year: "numeric" });
  }

  const dow = ["SUN", "MON", "TUE", "WED", "THU", "FRI", "SAT"];

  let html = `
    <div style="display:grid; grid-template-columns:repeat(7,1fr); gap:8px; text-align:center; font-weight:900; color:rgba(0,0,0,.55);">
      ${dow.map((x) => `<div>${x}</div>`).join("")}
    </div>
    <div style="height:10px;"></div>
    <div style="display:grid; grid-template-columns:repeat(7,1fr); gap:8px;">
  `;

  for (let i = 0; i < first.getDay(); i++) html += `<div></div>`;

  for (let day = 1; day <= last.getDate(); day++) {
    const d = new Date(calYear, calMonth, day);
    const key = ymd(d);
    const isSelected = calSelectedDate === key;

    const counts = calCounts[key] || { notes: 0, todos: 0 };
    const showNote = counts.notes > 0;
    const showTodo = counts.todos > 0;

    html += `
      <button class="cal-day" data-calday="${key}" style="
        border:0; cursor:pointer;
        padding:10px 0 8px;
        border-radius:14px;
        background:${isSelected ? "rgba(0,0,0,.22)" : "rgba(255,255,255,.22)"};
        color:${isSelected ? "#fff" : "rgba(0,0,0,.7)"};
        font-weight:900;
      ">
        <div>${day}</div>
        <div class="cal-markers">
          ${showNote ? `<span class="cal-badge note" title="${counts.notes} note(s)">${counts.notes}</span>` : ``}
          ${showTodo ? `<span class="cal-badge todo" title="${counts.todos} task(s)">${counts.todos}</span>` : ``}
        </div>
      </button>
    `;
  }

  html += `</div>`;
  grid.innerHTML = html;

  grid.querySelectorAll("[data-calday]").forEach((btn) => {
    btn.addEventListener("click", () => {
      calSelectedDate = btn.dataset.calday;
      buildCalendar();
      loadCalendarDay();
    });
  });

  if (!calSelectedDate) {
    calSelectedDate = ymd(now);
    const sel = qs("calSelectedLabel");
    if (sel) sel.textContent = calSelectedDate;
  }
}

async function loadCalendarDay() {
  const list = qs("calItems");
  if (!list) return;

  const label = qs("calSelectedLabel");
  if (label) label.textContent = calSelectedDate || "";

  const [notesRes, todosRes] = await Promise.all([
    api(`../api/notes.php?date=${encodeURIComponent(calSelectedDate)}`),
    api(`../api/todos.php?date=${encodeURIComponent(calSelectedDate)}`),
  ]);

  const notes = notesRes.notes || [];
  const todos = todosRes.todos || [];

  list.innerHTML = "";

  // notes
  notes.forEach((n) => {
    const row = document.createElement("div");
    row.className = "item";
    row.innerHTML = `
      <div style="min-width:0;">
        <b>📝 ${escapeHtml(n.title)}</b>
        <div style="font-size:12px; opacity:.75;">
          Note${n.updated_at ? ` • Last edited: ${escapeHtml(fmtDateTime(n.updated_at))}` : ""}
        </div>
      </div>
      <div style="display:flex; gap:8px;">
        <button class="iconbtn" data-cal-edit-note="${n.id}">✏️</button>
        <button class="iconbtn danger" data-cal-del-note="${n.id}">🗑️</button>
      </div>
    `;
    list.appendChild(row);
  });

  // todos
  todos.forEach((t) => {
    const done = Number(t.is_done) === 1;
    const row = document.createElement("div");
    row.className = "item";
    row.innerHTML = `
      <div style="min-width:0;">
        <div style="display:flex; align-items:center; gap:10px;">
          <input type="checkbox" data-cal-done="${t.id}" ${done ? "checked" : ""} />
          <b style="text-decoration:${done ? "line-through" : "none"};">
            ✅ ${escapeHtml(t.title)}
          </b>
        </div>
        <div style="font-size:12px; opacity:.75; margin-left:32px;">
          Due: ${escapeHtml(dueText(t.due_date, t.due_time))}
        </div>
      </div>
      <div style="display:flex; gap:8px;">
        <button class="iconbtn" data-cal-edit-todo="${t.id}">✏️</button>
        <button class="iconbtn danger" data-cal-del-todo="${t.id}">🗑️</button>
      </div>
    `;
    list.appendChild(row);
  });

  // note edit
  list.querySelectorAll("[data-cal-edit-note]").forEach((btn) => {
    btn.addEventListener("click", () => {
      const id = Number(btn.dataset.calEditNote);
      const note = notes.find((x) => Number(x.id) === id);
      openNote(note);
    });
  });

  // note delete
  list.querySelectorAll("[data-cal-del-note]").forEach((btn) => {
    btn.addEventListener("click", async () => {
      const id = Number(btn.dataset.calDelNote);

      const yes = await ncConfirm({
        title: "Delete note?",
        message: "This will permanently remove the note.",
        confirmText: "Delete",
        cancelText: "Cancel",
        danger: true,
      });
      if (!yes) return;

      await ncActionFlow(
        () => api("../api/notes.php", { method: "DELETE", body: JSON.stringify({ id }) }),
        {
          pendingTitle: "Deleting note...",
          pendingSub: "Removing it safely",
          successTitle: "Deleted!",
          successSub: "Your note was deleted.",
        },
        { buttons: [btn] }
      );

      await refreshCalendarIfPresent();
      loadCalendarDay();
    });
  });

  // todo done toggle
  list.querySelectorAll("[data-cal-done]").forEach((cb) => {
    cb.addEventListener("change", async () => {
      const id = Number(cb.dataset.calDone);

      await ncActionFlow(
        () => api("../api/todos.php", { method: "PUT", body: JSON.stringify({ id, is_done: cb.checked }) }),
        {
          pendingTitle: cb.checked ? "Completing..." : "Reopening...",
          pendingSub: "Updating status",
          successTitle: "Done!",
          successSub: cb.checked ? "Task marked as complete." : "Task marked as active.",
        },
        { buttons: [cb] }
      );

      await refreshCalendarIfPresent();
      loadCalendarDay();
    });
  });

  // todo edit
  list.querySelectorAll("[data-cal-edit-todo]").forEach((btn) => {
    btn.addEventListener("click", () => {
      const id = Number(btn.dataset.calEditTodo);
      const todo = todos.find((x) => Number(x.id) === id);
      openTodo(todo);
    });
  });

  // todo delete
  list.querySelectorAll("[data-cal-del-todo]").forEach((btn) => {
    btn.addEventListener("click", async () => {
      const id = Number(btn.dataset.calDelTodo);

      const yes = await ncConfirm({
        title: "Delete task?",
        message: "This will permanently remove the task.",
        confirmText: "Delete",
        cancelText: "Cancel",
        danger: true,
      });
      if (!yes) return;

      await ncActionFlow(
        () => api("../api/todos.php", { method: "DELETE", body: JSON.stringify({ id }) }),
        {
          pendingTitle: "Deleting task...",
          pendingSub: "Removing it safely",
          successTitle: "Deleted!",
          successSub: "Task deleted successfully.",
        },
        { buttons: [btn] }
      );

      await refreshCalendarIfPresent();
      loadCalendarDay();
    });
  });
}

async function refreshCalendarIfPresent() {
  if (!qs("calendarGrid")) return;
  await loadCalendarMonthCounts();
  buildCalendar();
  loadCalendarDay();
}

/* =========================
   PROFILE (profile.php)
   (unchanged from your version)
   ========================= */

let selectedTheme = null;

async function loadProfile() {
  if (!qs("pfUsername")) return;

  const res = await api("../api/profile.php");
  if (!res.ok) return;

  const u = res.user || {};
  qs("pfUsername").value = u.username || "";
  qs("pfEmail").value = u.email || "";
  qs("pfPassword").value = "";

  selectedTheme = u.theme || "teal";
  markThemeSelected();
}

function markThemeSelected() {
  const wrap = qs("themePickers");
  if (!wrap) return;

  wrap.querySelectorAll("button[data-theme]").forEach((btn) => {
    const t = btn.dataset.theme;
    btn.textContent = (t === selectedTheme) ? "✓" : " ";
  });
}

function enableInput(id) {
  const el = qs(id);
  if (!el) return;
  el.disabled = false;
  el.focus();
}

async function saveProfile() {
  const btn = qs("pfSave");

  const payload = {
    username: (qs("pfUsername")?.value || "").trim(),
    email: (qs("pfEmail")?.value || "").trim(),
    password: (qs("pfPassword")?.value || "").trim(),
    theme: selectedTheme,
  };

  if (!payload.password) delete payload.password;

  const res = await ncActionFlow(
    () => api("../api/profile.php", { method: "PUT", body: JSON.stringify(payload) }),
    {
      pendingTitle: "Saving profile...",
      pendingSub: "Updating your settings",
      successTitle: "Saved!",
      successSub: "Profile updated successfully.",
      errorTitle: "Save failed",
      errorSub: "Please check your inputs.",
    },
    { buttons: [btn] }
  );

  if (!res || res.ok === false) return;

  document.documentElement.setAttribute("data-theme", res.user?.theme || selectedTheme);

  qs("pfUsername").disabled = true;
  qs("pfEmail").disabled = true;
  qs("pfPassword").value = "";
}

async function deleteAccount() {
  const btn = qs("pfDelete");

  const yes = await ncConfirm({
    title: "Delete account?",
    message: "This will permanently delete your account and all your notes/tasks.",
    confirmText: "Delete",
    cancelText: "Cancel",
    danger: true,
  });
  if (!yes) return;

  await ncActionFlow(
    () => api("../api/profile.php", { method: "DELETE" }),
    {
      pendingTitle: "Deleting account...",
      pendingSub: "This may take a moment",
      successTitle: "Account deleted",
      successSub: "Redirecting...",
    },
    { buttons: [btn] }
  );

  window.location.href = "index.php";
}

/* =========================
   INIT
   ========================= */

document.addEventListener("DOMContentLoaded", () => {
  // NOTES page
  if (qs("notesList")) {
    qs("btnNewNote")?.addEventListener("click", () => openNote(null));
    qs("closeNote")?.addEventListener("click", closeNote);
    qs("saveNote")?.addEventListener("click", saveNote);
    qs("noteSearch")?.addEventListener("input", loadNotes);
    loadNotes();
  }

  // TODOS page
  if (qs("todosList")) {
    qs("btnNewTodo")?.addEventListener("click", () => openTodo(null));
    qs("closeTodo")?.addEventListener("click", closeTodo);
    qs("saveTodo")?.addEventListener("click", saveTodo);
    qs("todoSearch")?.addEventListener("input", loadTodos);
    loadTodos();
  }

  // CALENDAR page
  if (qs("calendarGrid")) {
    const now = new Date();
    calYear = now.getFullYear();
    calMonth = now.getMonth();
    calSelectedDate = ymd(now);

    const refreshMonth = async () => {
      await loadCalendarMonthCounts();
      buildCalendar();
      loadCalendarDay();
    };

    qs("calPrev")?.addEventListener("click", async () => {
      calMonth -= 1;
      if (calMonth < 0) { calMonth = 11; calYear -= 1; }
      await refreshMonth();
    });

    qs("calNext")?.addEventListener("click", async () => {
      calMonth += 1;
      if (calMonth > 11) { calMonth = 0; calYear += 1; }
      await refreshMonth();
    });

    qs("calAddNote")?.addEventListener("click", () => {
      openNote({ note_date: calSelectedDate, title: "", content: "" });
      if (qs("noteDate")) qs("noteDate").value = calSelectedDate;
    });

    qs("calAddTask")?.addEventListener("click", () => {
      openTodo({ due_date: calSelectedDate, due_time: "", title: "" });
      if (qs("todoDueDate")) qs("todoDueDate").value = calSelectedDate;
      if (qs("todoDueTime")) qs("todoDueTime").value = "";
    });

    refreshMonth();
  }

  // PROFILE page
  if (qs("pfUsername")) {
    loadProfile();

    qs("pfEditUsername")?.addEventListener("click", () => enableInput("pfUsername"));
    qs("pfEditEmail")?.addEventListener("click", () => enableInput("pfEmail"));

    qs("pfSave")?.addEventListener("click", saveProfile);
    qs("pfCancel")?.addEventListener("click", () => window.location.reload());

    const wrap = qs("themePickers");
    wrap?.querySelectorAll("button[data-theme]").forEach((btn) => {
      btn.addEventListener("click", async () => {
        selectedTheme = btn.dataset.theme;
        markThemeSelected();

        const res = await api("../api/profile.php", {
          method: "PUT",
          body: JSON.stringify({ theme: selectedTheme }),
        });
        if (res.ok) document.documentElement.setAttribute("data-theme", res.user?.theme || selectedTheme);
      });
    });

    qs("pfDelete")?.addEventListener("click", deleteAccount);
  }
});

// Password peek toggle
document.addEventListener("click", (e) => {
  const btn = e.target.closest(".pw-toggle");
  if (!btn) return;

  const id = btn.getAttribute("data-target");
  const input = document.getElementById(id);
  if (!input) return;

  const isPw = input.type === "password";
  input.type = isPw ? "text" : "password";
  btn.textContent = isPw ? "🙈" : "👁";
});


/* =========================
   HOME (home.php)
   ========================= */

function ncFmtStamp(s) {
  if (!s) return "";
  return String(s).replace("T", " ").slice(0, 16);
}

function ncFmtTime(s) {
  if (!s) return "";
  return String(s).slice(0, 5);
}

async function loadHomeDashboard() {
  if (!qs("statNotes") || !qs("todayNotes") || !qs("todayTodos")) return;

  const today = new Date().toISOString().slice(0, 10);

  const [notesAll, todosAll, notesToday, todosToday] = await Promise.all([
    api("../api/notes.php"),
    api("../api/todos.php"),
    api(`../api/notes.php?date=${encodeURIComponent(today)}`),
    api(`../api/todos.php?date=${encodeURIComponent(today)}`),
  ]);

  const notes = notesAll.notes || [];
  const todos = todosAll.todos || [];

  const pending = todos.filter((t) => Number(t.is_done) !== 1).length;

  const nToday = (notesToday.notes || []).length;
  const tToday = (todosToday.todos || []).length;

  qs("statNotes").textContent = notes.length;
  qs("statPending").textContent = pending;
  qs("statToday").textContent = nToday + tToday;

  const tn = qs("todayNotes");
  const tt = qs("todayTodos");

  const todayNotes = notesToday.notes || [];
  tn.innerHTML = todayNotes.length
    ? todayNotes
        .map((n) => {
          const edited = ncFmtStamp(n.updated_at);
          const meta = `${escapeHtml(n.note_date || "")}${edited ? " • Last edited: " + escapeHtml(edited) : ""}`;
          return `
            <div class="mini-item">
              <div>
                <b>${escapeHtml(n.title || "Untitled")}</b>
                <div class="meta">${meta}</div>
              </div>
              <div>📝</div>
            </div>
          `;
        })
        .join("")
    : `<div class="mini-item"><div><b>No notes today</b><div class="meta">Create one in Effortless Notes</div></div><div>✨</div></div>`;

  const todayTodos = todosToday.todos || [];
  tt.innerHTML = todayTodos.length
    ? todayTodos
        .map((t) => {
          const time = ncFmtTime(t.due_time);
          const due = `${escapeHtml(t.due_date || "")}${time ? " • " + escapeHtml(time) : ""}`;
          const icon = Number(t.is_done) === 1 ? "✅" : "🕒";
          return `
            <div class="mini-item">
              <div>
                <b>${escapeHtml(t.title || "Untitled task")}</b>
                <div class="meta">Due: ${due}</div>
              </div>
              <div>${icon}</div>
            </div>
          `;
        })
        .join("")
    : `<div class="mini-item"><div><b>No tasks due today</b><div class="meta">Add one in Gentle To-Dos</div></div><div>🌿</div></div>`;
}

document.addEventListener("DOMContentLoaded", loadHomeDashboard);
