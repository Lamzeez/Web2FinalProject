<?php
// api/todos.php
require_once "../includes/config.php";
require_once "../includes/auth.php";
require_login();

header("Content-Type: application/json; charset=utf-8");

$user_id = (int)($_SESSION["user_id"] ?? 0);
$method = $_SERVER["REQUEST_METHOD"] ?? "GET";

function respond(int $code, array $payload): void {
  http_response_code($code);
  echo json_encode($payload);
  exit;
}

function read_json(): array {
  $raw = file_get_contents("php://input");
  if (!$raw) return [];
  $data = json_decode($raw, true);
  return is_array($data) ? $data : [];
}

function norm_date($v) {
  if ($v === null) return null;
  $v = trim((string)$v);
  return $v === "" ? null : $v;
}

function norm_time($v) {
  if ($v === null) return null;
  $v = trim((string)$v);
  if ($v === "") return null;

  // Allow "HH:MM" or "HH:MM:SS"
  if (preg_match('/^\d{2}:\d{2}$/', $v)) return $v . ":00";
  if (preg_match('/^\d{2}:\d{2}:\d{2}$/', $v)) return $v;

  return "__INVALID__";
}

try {
  if ($method === "GET") {
    $q     = trim($_GET["q"] ?? "");
    $date  = $_GET["date"] ?? null;
    $start = $_GET["start"] ?? null;
    $end   = $_GET["end"] ?? null;

    $sql = "SELECT id, user_id, title, is_done, due_date, due_time, created_at, updated_at
            FROM todos
            WHERE user_id = ?";
    $params = [$user_id];

    if ($q !== "") {
      $sql .= " AND title LIKE ?";
      $params[] = "%{$q}%";
    }

    if (!empty($date)) {
      $sql .= " AND due_date = ?";
      $params[] = $date;
    } elseif (!empty($start) && !empty($end)) {
      // include NULL due_date tasks too (like your current behavior)
      $sql .= " AND (due_date BETWEEN ? AND ? OR due_date IS NULL)";
      $params[] = $start;
      $params[] = $end;
    }

    // Done last, null dates last, then by due date/time
    $sql .= " ORDER BY
                is_done ASC,
                CASE WHEN due_date IS NULL THEN 1 ELSE 0 END ASC,
                due_date ASC,
                CASE WHEN due_time IS NULL THEN 1 ELSE 0 END ASC,
                due_time ASC,
                updated_at DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    respond(200, ["ok" => true, "todos" => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
  }

  $data = read_json();

  if ($method === "POST") {
    $title = trim($data["title"] ?? "");
    if ($title === "") {
      respond(400, ["ok" => false, "error" => "Title is required."]);
    }

    $due_date = array_key_exists("due_date", $data) ? norm_date($data["due_date"]) : null;
    $due_time = array_key_exists("due_time", $data) ? norm_time($data["due_time"]) : null;

    if ($due_time === "__INVALID__") {
      respond(400, ["ok" => false, "error" => "Invalid due_time. Use HH:MM or HH:MM:SS."]);
    }

    $stmt = $pdo->prepare(
      "INSERT INTO todos (user_id, title, due_date, due_time, is_done)
       VALUES (?, ?, ?, ?, 0)"
    );
    $stmt->execute([$user_id, $title, $due_date, $due_time]);

    respond(201, ["ok" => true, "id" => (int)$pdo->lastInsertId()]);
  }

  if ($method === "PUT") {
    $id = (int)($data["id"] ?? 0);
    if ($id <= 0) respond(400, ["ok" => false, "error" => "Missing todo id."]);

    // Allow partial updates:
    // title, due_date (nullable), due_time (nullable), is_done
    $sets = [];
    $params = [];

    if (array_key_exists("title", $data)) {
      $title = trim((string)$data["title"]);
      if ($title === "") respond(400, ["ok" => false, "error" => "Title cannot be empty."]);
      $sets[] = "title = ?";
      $params[] = $title;
    }

    if (array_key_exists("due_date", $data)) {
      $due_date = norm_date($data["due_date"]);
      $sets[] = "due_date = ?";
      $params[] = $due_date;
    }

    if (array_key_exists("due_time", $data)) {
      $due_time = norm_time($data["due_time"]);
      if ($due_time === "__INVALID__") {
        respond(400, ["ok" => false, "error" => "Invalid due_time. Use HH:MM or HH:MM:SS."]);
      }
      $sets[] = "due_time = ?";
      $params[] = $due_time;
    }

    if (array_key_exists("is_done", $data)) {
      $is_done = (int)!!$data["is_done"];
      $sets[] = "is_done = ?";
      $params[] = $is_done;
    }

    if (!$sets) {
      respond(200, ["ok" => true]); // nothing to update
    }

    $params[] = $id;
    $params[] = $user_id;

    $sql = "UPDATE todos SET " . implode(", ", $sets) . " WHERE id = ? AND user_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    respond(200, ["ok" => true]);
  }

  if ($method === "DELETE") {
    $id = (int)($data["id"] ?? 0);
    if ($id <= 0) respond(400, ["ok" => false, "error" => "Missing todo id."]);

    $stmt = $pdo->prepare("DELETE FROM todos WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $user_id]);

    respond(200, ["ok" => true]);
  }

  respond(405, ["ok" => false, "error" => "Method not allowed"]);
} catch (Throwable $e) {
  respond(500, ["ok" => false, "error" => "Server error"]);
}
