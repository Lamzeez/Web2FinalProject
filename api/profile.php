<?php
// api/profile.php
require_once "../includes/config.php";
require_once "../includes/auth.php";
require_login();

header("Content-Type: application/json; charset=utf-8");

$user_id = (int)$_SESSION["user_id"];
$method = $_SERVER["REQUEST_METHOD"];

function ok($arr){ echo json_encode(array_merge(["ok"=>true], $arr)); exit; }
function bad($code, $msg){ http_response_code($code); echo json_encode(["ok"=>false,"error"=>$msg]); exit; }

try {
  if ($method === "GET") {
    $stmt = $pdo->prepare("SELECT id, username, email, theme FROM users WHERE id=? LIMIT 1");
    $stmt->execute([$user_id]);
    $u = $stmt->fetch();
    ok(["user"=>$u]);
  }

  $data = json_decode(file_get_contents("php://input"), true) ?? [];

  if ($method === "PUT") {
    $username = isset($data["username"]) ? trim($data["username"]) : null;
    $email = isset($data["email"]) ? trim($data["email"]) : null;
    $password = $data["password"] ?? null;
    $theme = isset($data["theme"]) ? trim($data["theme"]) : null;

    // Validate uniqueness if changing
    if ($username !== null && $username !== "") {
      $stmt = $pdo->prepare("SELECT id FROM users WHERE username=? AND id<>? LIMIT 1");
      $stmt->execute([$username, $user_id]);
      if ($stmt->fetch()) bad(400, "Username already taken.");
    }

    if ($email !== null && $email !== "") {
      $stmt = $pdo->prepare("SELECT id FROM users WHERE email=? AND id<>? LIMIT 1");
      $stmt->execute([$email, $user_id]);
      if ($stmt->fetch()) bad(400, "Email already taken.");
    }

    $sets = [];
    $params = [];

    if ($username !== null && $username !== "") { $sets[]="username=?"; $params[]=$username; }
    if ($email !== null && $email !== "") { $sets[]="email=?"; $params[]=$email; }
    if ($theme !== null && $theme !== "") { $sets[]="theme=?"; $params[]=$theme; }

    if ($password !== null && trim($password) !== "") {
      if (strlen($password) < 6) bad(400, "Password must be at least 6 characters.");
      $sets[]="password_hash=?";
      $params[] = password_hash($password, PASSWORD_DEFAULT);
    }

    if ($sets) {
      $params[] = $user_id;
      $sql = "UPDATE users SET " . implode(",", $sets) . " WHERE id=?";
      $stmt = $pdo->prepare($sql);
      $stmt->execute($params);
    }

    // Refresh session values
    $stmt = $pdo->prepare("SELECT username, theme FROM users WHERE id=? LIMIT 1");
    $stmt->execute([$user_id]);
    $u = $stmt->fetch();
    $_SESSION["username"] = $u["username"];
    $_SESSION["theme"] = $u["theme"];

    ok(["user"=>$u]);
  }

  if ($method === "DELETE") {
    $pdo->prepare("DELETE FROM users WHERE id=?")->execute([$user_id]);
    session_destroy();
    ok(["deleted"=>true]);
  }

  bad(405, "Method not allowed.");
} catch (Throwable $e) {
  bad(500, "Server error.");
}
