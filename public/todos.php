<?php
require_once "../includes/config.php";
require_once "../includes/auth.php";
require_login();

$page_title = "Gentle To-Dos • NoteCore";
$show_nav = true;
$active = "todos";

require_once "../includes/header.php";
?>

<h2 style="font-family:Georgia,serif; text-shadow:0 2px 0 rgba(0,0,0,.2); margin:10px 0 2px;">Gentle To-Dos</h2>
<div class="tagline" style="text-align:left; margin:0 0 14px;">Tasks without the mental load</div>

<div class="panel">
  <div class="row">
    <button class="btn" id="btnNewTodo">＋ Add Task</button>
    <input class="input" id="todoSearch" placeholder="Search tasks..." />
  </div>

  <div class="list" id="todosList"></div>
</div>

<!-- Todo modal -->
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
