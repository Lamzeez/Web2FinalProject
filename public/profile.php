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
    <div class="logo" style="width:96px;height:96px;border-radius:999px;font-size:44px;display:flex;align-items:center;justify-content:center;">👤</div>
    <div>
      <h2 style="margin:0; font-family:Georgia,serif; text-shadow:0 2px 0 rgba(0,0,0,.2);">Profile Settings</h2>
      <div class="tagline" style="text-align:left; margin:4px 0 0;">Manage your account & theme</div>
    </div>
  </div>

  <div class="card" style="margin-top:14px;">
    <div style="display:grid; grid-template-columns: 160px 1fr 60px; gap:10px; align-items:center;">
      <b>Username</b>
      <input class="input" id="pfUsername" disabled />
      <button type="button" class="iconbtn" id="pfEditUsername" title="Edit username">✏️</button>

      <b>Current Password</b>
      <div class="field pw-field" style="width:100%;">
        <input class="input" id="pfCurrentPassword" type="password" placeholder="Current password" disabled autocomplete="current-password" />
        <button type="button" class="pw-toggle" data-target="pfCurrentPassword" aria-label="Show password">👁</button>
      </div>
      <button type="button" class="iconbtn" id="pfEditPassword" title="Change password">✏️</button>

      <b>New Password</b>
      <div class="field pw-field" style="width:100%;">
        <input class="input" id="pfNewPassword" type="password" placeholder="New password" disabled autocomplete="new-password"
              pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$"
              title="At least 8 characters, with uppercase, lowercase, number, and special character." />
        <button type="button" class="pw-toggle" data-target="pfNewPassword" aria-label="Show password">👁</button>
      </div>
      <span></span>

      <b>Confirm New Password</b>
      <div class="field pw-field" style="width:100%;">
        <input class="input" id="pfConfirmPassword" type="password" placeholder="Confirm new password" disabled autocomplete="new-password" />
        <button type="button" class="pw-toggle" data-target="pfConfirmPassword" aria-label="Show password">👁</button>
      </div>
      <span></span>



      <b>Email</b>
      <input class="input" id="pfEmail" disabled />
      <span></span>
    </div>


    <div style="margin-top:12px; display:flex; gap:10px;">
      <button type="button" class="btn" id="pfSave" style="flex:1;">Save Changes</button>
      <button type="button" class="btn secondary" id="pfCancel" style="flex:1;">Cancel</button>
    </div>
  </div>

  <div class="card" style="margin-top:14px; background: rgba(225,63,64,.12); border: 1px solid rgba(225,63,64,.35);">
    <h3 style="margin:0 0 10px; font-family:Georgia,serif;">Delete Account?</h3>
    <button type="button" class="btn danger" id="pfDelete" style="width:100%;">Delete</button>
  </div>
</div>

<?php require_once "../includes/footer.php"; ?>
