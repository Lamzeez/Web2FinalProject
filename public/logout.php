<?php
// public/logout.php
require_once "../includes/config.php";

$page_title = "Logout • NoteCore";
$show_nav = false;

session_destroy();

require_once "../includes/header.php";
?>

<div class="panel" style="max-width:560px;">
  <h2 style="text-align:center; margin:6px 0 14px; font-family:Georgia,serif;">Logging out…</h2>
  <p style="text-align:center; color:var(--muted);">Please wait.</p>
</div>

<script>
  document.addEventListener("DOMContentLoaded", async () => {
    if (typeof ncDialogShow === "function") {
      await ncDialogShow({
        title: "Logged out",
        sub: "See you again soon ✨",
        state: "success",
        duration: 900,
      });
    }
    window.location.href = "index.php";
  });
</script>

<?php require_once "../includes/footer.php"; ?>
