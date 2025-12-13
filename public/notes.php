<?php
require_once "../includes/config.php";
require_once "../includes/auth.php";
require_login();

$page_title = "Effortless Notes • NoteCore";
$show_nav = true;
$active = "notes";

require_once "../includes/header.php";
?>

<style>
  .note-item{
    display:flex;
    align-items:flex-start;
    justify-content:space-between;
    gap: 12px;
    padding: 12px 12px;
    border-radius: 18px;
    background: rgba(255,255,255,.16);
    border: 1px solid rgba(255,255,255,.18);
    box-shadow: 0 10px 20px rgba(0,0,0,.08);
    margin-top: 10px;
  }
  .note-main{ min-width:0; }
  .note-title{
    font-weight: 950;
    color: rgba(0,0,0,.82);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  .note-meta{
    font-size: 12px;
    opacity: .75;
    font-weight: 800;
    margin-top: 4px;
  }
  .note-preview{
    margin-top: 8px;
    font-size: 13px;
    opacity: .85;
    line-height: 1.45;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }
  .note-right{ display:flex; align-items:center; gap: 10px; flex:0 0 auto; }
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

  .modal-card{ max-width:640px; margin: 8vh auto; position:relative; }
  .help{ font-size: 12px; opacity:.75; font-weight:700; margin-top:4px; }
</style>

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
  <div class="panel modal-card">
    <h3 style="margin:0 0 10px;">Note</h3>
    <input class="input" id="noteId" type="hidden" />
    <input class="input" id="noteTitle" placeholder="Title" />
    <textarea class="input" id="noteContent" placeholder="Write your note..." style="min-height:140px; margin-top:10px;"></textarea>

    <div style="margin-top:10px;">
      <input class="input" id="noteDate" type="date" />
      <div class="help">Date (used for Calendar Peace)</div>
    </div>

    <div class="row" style="margin-top:12px;">
      <button class="btn" id="saveNote">Save</button>
      <button class="btn secondary" id="closeNote">Close</button>
    </div>
  </div>
</div>

<?php require_once "../includes/footer.php"; ?>
