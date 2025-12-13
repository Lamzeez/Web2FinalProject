<?php
require_once "../includes/config.php";
require_once "../includes/auth.php";
require_login();

$page_title = "Calendar Peace • NoteCore";
$show_nav = true;
$active = "calendar";

require_once "../includes/header.php";
?>

<h2 style="font-family:Georgia,serif; text-shadow:0 2px 0 rgba(0,0,0,.2); margin:10px 0 2px;">Calendar Peace</h2>
<div class="tagline" style="text-align:left; margin:0 0 14px;">Secure plans in one peaceful place</div>

<div class="panel">
  <div class="row" style="align-items:center;">
    <button class="btn secondary" id="calPrev">‹</button>
    <div class="card" style="text-align:center; margin:0; padding:12px; flex:2;">
      <b id="calLabel">Month YYYY</b>
    </div>
    <button class="btn secondary" id="calNext">›</button>
  </div>

  <div id="calendarGrid" class="card" style="margin-top:12px; padding:14px;"></div>

  <div style="margin-top:14px; text-align:center; color:var(--muted);">
    Notes/tasks on this day:
    <b id="calSelectedLabel"></b>
  </div>

  <div class="list" id="calItems" style="margin-top:10px;"></div>

  <div style="display:flex; gap:10px; margin-top:12px;">
    <button class="btn" id="calAddNote" style="flex:1;">＋ Add Note</button>
    <button class="btn" id="calAddTask" style="flex:1;">＋ Add Task</button>
  </div>
</div>

<!-- Reuse Note modal -->
<div id="noteModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.45);">
  <div class="panel" style="max-width:640px; margin: 8vh auto; position:relative;">
    <h3 style="margin:0 0 10px;">Note</h3>
    <input class="input" id="noteId" type="hidden" />
    <input class="input" id="noteTitle" placeholder="Title" />
    <textarea class="input" id="noteContent" placeholder="Write your note..." style="min-height:140px; margin-top:10px;"></textarea>
    <div class="row" style="margin-top:10px;">
      <input class="input" id="noteDate" type="date" />
      <button class="btn" id="saveNote">Save</button>
      <button class="btn secondary" id="closeNote">Close</button>
    </div>
  </div>
</div>

<!-- Reuse Todo modal -->
<div id="todoModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.45);">
  <div class="panel" style="max-width:640px; margin: 8vh auto; position:relative;">
    <h3 style="margin:0 0 10px;">Task</h3>
    <input class="input" id="todoId" type="hidden" />
    <input class="input" id="todoTitle" placeholder="Task title" />
    <div class="row" style="margin-top:10px;">
      <input class="input" id="todoDueDate" type="date" />
      <button class="btn" id="saveTodo">Save</button>
      <button class="btn secondary" id="closeTodo">Close</button>
    </div>
  </div>
</div>

<?php require_once "../includes/footer.php"; ?>
