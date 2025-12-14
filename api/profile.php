<?php
require_once "../includes/config.php";
require_once "../includes/auth.php";
require_login();

header("Content-Type: application/json; charset=utf-8");

function json_out($arr, $code = 200) {
  http_response_code($code);
  echo json_encode($arr);
  exit;
}

function get_uid() {
  if (isset($_SESSION["user_id"])) return (int)$_SESSION["user_id"];
  if (isset($_SESSION["uid"])) return (int)$_SESSION["uid"];
  if (isset($_SESSION["user"]["id"])) return (int)$_SESSION["user"]["id"];
  if (function_exists("current_user_id")) return (int)current_user_id();
  return 0;
}

$uid = get_uid();
if (!$uid) json_out(["ok" => false, "error" => "Not authenticated."], 401);

$method = $_SERVER["REQUEST_METHOD"] ?? "GET";

try {
  // GET: return user profile
  if ($method === "GET") {
    $st = $pdo->prepare("SELECT id, username, email, theme FROM users WHERE id = ?");
    $st->execute([$uid]);
    $u = $st->fetch(PDO::FETCH_ASSOC);
    if (!$u) json_out(["ok" => false, "error" => "User not found."], 404);

    $u["theme"] = $u["theme"] ?: "teal";
    json_out(["ok" => true, "user" => $u]);
  }

  // PUT: update username / password / theme (email is NOT editable)
  if ($method === "PUT") {
    $raw = file_get_contents("php://input");
    $data = json_decode($raw, true);
    if (!is_array($data)) $data = [];

    // Ignore email even if sent
    unset($data["email"]);

    $updates = [];
    $params = [];

    // Username update
    if (isset($data["username"])) {
      $username = trim((string)$data["username"]);
      if (strlen($username) < 3) json_out(["ok" => false, "error" => "Username must be at least 3 characters."], 400);

      // unique check (exclude current user)
      $chk = $pdo->prepare("SELECT id FROM users WHERE username = ? AND id <> ? LIMIT 1");
      $chk->execute([$username, $uid]);
      if ($chk->fetch()) json_out(["ok" => false, "error" => "Username already taken."], 400);

      $updates[] = "username = ?";
      $params[] = $username;
    }

    // Password update (optional) - requires current password
    if (isset($data["new_password"]) && trim((string)$data["new_password"]) !== "") {
      $current = (string)($data["current_password"] ?? "");
      $pass = (string)$data["new_password"];

      if (trim($current) === "") {
        json_out(["ok" => false, "error" => "Current password is required to change password."], 400);
      }

      // Load current hash
      $st = $pdo->prepare("SELECT password_hash FROM users WHERE id = ?");
      $st->execute([$uid]);
      $row = $st->fetch(PDO::FETCH_ASSOC);
      if (!$row) json_out(["ok" => false, "error" => "User not found."], 404);

      if (!password_verify($current, $row["password_hash"])) {
        json_out(["ok" => false, "error" => "Current password is incorrect."], 400);
      }

      $pwPattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/';
      if (!preg_match($pwPattern, $pass)) {
        json_out(["ok" => false, "error" => "Password must be 8+ characters and include uppercase, lowercase, number, and special character."], 400);
      }

      $updates[] = "password_hash = ?";
      $params[] = password_hash($pass, PASSWORD_DEFAULT);
    }


    // Theme update
    if (isset($data["theme"])) {
      $theme = trim((string)$data["theme"]);
      $allowed = ["teal","driftwood","sage","charcoal","fern","blush","lavender","wine"];
      if (!in_array($theme, $allowed, true)) {
        json_out(["ok" => false, "error" => "Invalid theme."], 400);
      }

      $updates[] = "theme = ?";
      $params[] = $theme;
    }

    if (!$updates) {
      // nothing to change, but still return current user
      $st = $pdo->prepare("SELECT id, username, email, theme FROM users WHERE id = ?");
      $st->execute([$uid]);
      $u = $st->fetch(PDO::FETCH_ASSOC);
      $u["theme"] = $u["theme"] ?: "teal";
      json_out(["ok" => true, "user" => $u]);
    }

    $params[] = $uid;
    $sql = "UPDATE users SET " . implode(", ", $updates) . " WHERE id = ?";
    $pdo->prepare($sql)->execute($params);

    $st = $pdo->prepare("SELECT id, username, email, theme FROM users WHERE id = ?");
    $st->execute([$uid]);
    $u = $st->fetch(PDO::FETCH_ASSOC);
    $u["theme"] = $u["theme"] ?: "teal";

    // ✅ Keep session in sync so Home shows the new username immediately
    $_SESSION["username"] = $u["username"];
    if (isset($_SESSION["user"]) && is_array($_SESSION["user"])) {
      $_SESSION["user"]["username"] = $u["username"];
      $_SESSION["user"]["theme"] = $u["theme"];
    }
    if (isset($_SESSION["theme"])) {
      $_SESSION["theme"] = $u["theme"];
    }

    json_out(["ok" => true, "user" => $u]);
  }

  // DELETE: delete account + user data
  if ($method === "DELETE") {
    $pdo->beginTransaction();

    // These table names match your app endpoints (notes.php / todos.php)
    // If your FK tables differ, update them accordingly.
    try {
      $pdo->prepare("DELETE FROM notes WHERE user_id = ?")->execute([$uid]);
    } catch (Throwable $e) { /* ignore if table/column differs */ }

    try {
      $pdo->prepare("DELETE FROM todos WHERE user_id = ?")->execute([$uid]);
    } catch (Throwable $e) { /* ignore if table/column differs */ }

    $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$uid]);

    $pdo->commit();

    // end session
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
      $p = session_get_cookie_params();
      setcookie(session_name(), "", time() - 42000, $p["path"], $p["domain"], $p["secure"], $p["httponly"]);
    }
    session_destroy();

    json_out(["ok" => true]);
  }

  json_out(["ok" => false, "error" => "Method not allowed."], 405);

} catch (Throwable $e) {
  if ($pdo->inTransaction()) $pdo->rollBack();
  json_out(["ok" => false, "error" => "Server error."], 500);
}
