<?php
require_once "../includes/config.php";
require_once "../includes/auth.php";
require_login();

$page_title = "Gentle To-Dos • NoteCore";
$show_nav = true;
$active = "todos";

require_once "../includes/header.php";
?>

<style>
  .todo-item{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap: 12px;
    padding: 12px 12px;
    border-radius: 18px;
    background: rgba(255,255,255,.16);
    border: 1px solid rgba(255,255,255,.18);
    box-shadow: 0 10px 20px rgba(0,0,0,.08);
    margin-top: 10px;
  }
  .todo-left{ display:flex; align-items:center; gap: 10px; min-width:0; }
  .todo-check{
    width: 22px; height: 22px;
    border-radius: 7px;
    border: 2px solid rgba(0,0,0,.25);
    background: rgba(255,255,255,.28);
    cursor:pointer;
    display:grid; place-items:center;
    flex: 0 0 auto;
    user-select:none;
  }
  .todo-check.done{
    background: rgba(78, 187, 120, .55);
    border-color: rgba(0,0,0,.15);
  }
  .todo-main{ min-width:0; }
  .todo-title{
    font-weight: 950;
    color: rgba(0,0,0,.82);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  .todo-meta{
    font-size: 12px;
    opacity: .75;
    font-weight: 800;
    margin-top: 3px;
  }
  .todo-right{ display:flex; align-items:center; gap: 10px; flex:0 0 auto; }
  .icon-btn{
    border: 0;
    background: rgba(0,0,0,.12);
    color: rgba(255,255,255,.95);
    border: 1px solid rgba(255,255,255,.14);
    border-radius: 14px;
    padding: 8px 10px;
    cursor:pointer;
  }
  .icon-btn:hover{ background: rgba(0,0,0,.18); }
  .icon-btn.danger{ background: rgba(220, 53, 69, .85); }
  .icon-btn.danger:hover{ background: rgba(220, 53, 69, 1); }

  /* Modal improvements */
  .modal-card{ max-width: 640px; margin: 8vh auto; position:relative; }
  .modal-grid{ display:grid; grid-template-columns: 1fr; gap:10px; margin-top:10px; }
  @media (min-width: 720px){
    .modal-grid{ grid-template-columns: 1fr 1fr; }
  }
  .help{ font-size: 12px; opacity:.75; font-weight:700; margin-top:4px; }
</style>

<h2 style="font-family:Georgia,serif; text-shadow:0 2px 0 rgba(0,0,0,.2); margin:10px 0 2px;">Gentle To-Dos</h2>
<div class="tagline" style="text-align:left; margin:0 0 14px;">Tasks without the mental load</div>

<div class="panel">
  <div class="row">
    <button class="btn" id="btnNewTodo">＋ Add Task</button>
    <input class="input" id="todoSearch" placeholder="Search tasks..." />
  </div>

  <div class="list scroll-8" id="todosList"></div>
</div>

<!-- Todo modal -->
<div id="todoModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.45);">
  <div class="panel modal-card">
    <h3 style="margin:0 0 10px;">Task</h3>

    <input class="input" id="todoId" type="hidden" />
    <input class="input" id="todoTitle" placeholder="Task title" />

    <!-- NEW: due date + due time -->
    <div class="modal-grid">
      <div>
        <input class="input" id="todoDueDate" type="date" />
        <div class="help">Due date (optional)</div>
      </div>
      <div>
        <input class="input" id="todoDueTime" type="time" />
        <div class="help">Due time (optional)</div>
      </div>
    </div>

    <div class="row" style="margin-top:12px;">
      <button class="btn" id="saveTodo">Save</button>
      <button class="btn secondary" id="closeTodo">Close</button>
    </div>
  </div>
</div>

<?php require_once "../includes/footer.php"; ?>
