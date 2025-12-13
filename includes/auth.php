<?php
// includes/auth.php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// require user login
function require_login(): void {
  if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
  }
}
