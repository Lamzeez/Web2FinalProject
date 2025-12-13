<?php
require_once "../includes/config.php";
require_once "../includes/auth.php";
require_login();

$page_title = "Profile • NoteCore";
$show_nav = true;
$active = "profile";

require_once "../includes/header.php";
?>

<div class="panel" style="max-width:860px; margin: 10px auto;">
  <div style="display:flex; align-items:center; gap:14px;">
    <div class="logo" style="width:64px;height:64px;border-radius:999px;">👤</div>
    <div>
      <h2 style="margin:0; font-family:Georgia,serif; text-shadow:0 2px 0 rgba(0,0,0,.2);">Profile Settings</h2>
      <div class="tagline" style="text-align:left; margin:4px 0 0;">Manage your account & theme</div>
    </div>
  </div>

  <div class="card" style="margin-top:14px;">
    <div style="display:grid; grid-template-columns: 160px 1fr 60px; gap:10px; align-items:center;">
      <b>Username</b>
      <input class="input" id="pfUsername" disabled />
      <button class="iconbtn" id="pfEditUsername">✏️</button>

      <b>Password</b>
      <input class="input" id="pfPassword" type="password" placeholder="••••••••" />
      <button class="iconbtn" title="Leave empty to keep current">ℹ️</button>

      <b>Email</b>
      <input class="input" id="pfEmail" disabled />
      <button class="iconbtn" id="pfEditEmail">✏️</button>
    </div>

    <div style="margin-top:12px; display:flex; gap:10px;">
      <button class="btn" id="pfSave" style="flex:1;">Save Changes</button>
      <button class="btn secondary" id="pfCancel" style="flex:1;">Cancel</button>
    </div>
  </div>

  <div class="card" style="margin-top:14px;">
    <b>Theme</b>
    <div style="display:flex; flex-wrap:wrap; gap:10px; margin-top:10px;" id="themePickers">
      <button class="iconbtn" data-theme="teal" title="Teal (Default)" style="background:#1fbab3;">✓</button>
      <button class="iconbtn" data-theme="driftwood" title="Driftwood" style="background:#9a8b81;"> </button>
      <button class="iconbtn" data-theme="sage" title="Sage Green" style="background:#9fb48b;"> </button>
      <button class="iconbtn" data-theme="charcoal" title="Deep Charcoal" style="background:#2f3337;"> </button>
      <button class="iconbtn" data-theme="fern" title="Fern" style="background:#567a5a;"> </button>
      <button class="iconbtn" data-theme="blush" title="Blush Pink" style="background:#f1b8c4;"> </button>
      <button class="iconbtn" data-theme="lavender" title="Lavender Haze" style="background:#b9a7e8;"> </button>
      <button class="iconbtn" data-theme="wine" title="Mulled Wine" style="background:#6b2a2a;"> </button>
    </div>
    <div style="margin-top:10px; color:var(--muted); font-size:13px;">
      Theme changes update your whole site instantly.
    </div>
  </div>

  <div class="card" style="margin-top:14px; background: rgba(225,63,64,.12); border: 1px solid rgba(225,63,64,.35);">
    <h3 style="margin:0 0 10px; font-family:Georgia,serif;">Delete Account?</h3>
    <button class="btn danger" id="pfDelete" style="width:100%;">Delete</button>
  </div>
</div>

<?php require_once "../includes/footer.php"; ?>
