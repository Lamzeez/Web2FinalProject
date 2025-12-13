<?php
require_once "../includes/config.php";
require_once "../includes/auth.php";
require_login();

$page_title = "Effortless Notes • NoteCore";
$show_nav = true;
$active = "notes";

require_once "../includes/header.php";
?>
<h2 style="font-family:Georgia,serif; text-shadow:0 2px 0 rgba(0,0,0,.2); margin:10px 0 2px;">Effortless Notes</h2>
<div class="tagline" style="text-align:left; margin:0 0 14px;">Capture thoughts, clear your mind</div>

<div class="panel">
  <div class="row">
    <button class="btn" id="btnNewNote">＋ New Note</button>
    <input class="input" id="noteSearch" placeholder="Search notes..." />
  </div>

  <div class="list" id="notesList"></div>
</div>

<!-- Simple modal -->
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

<?php require_once "../includes/footer.php"; ?>
