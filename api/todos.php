<?php
// api/todos.php
require_once "../includes/config.php";
require_once "../includes/auth.php";
require_login();

header("Content-Type: application/json; charset=utf-8");

$user_id = (int)$_SESSION["user_id"];
$method = $_SERVER["REQUEST_METHOD"];

try {
  if ($method === "GET") {
    $q = trim($_GET["q"] ?? "");
    $date = $_GET["date"] ?? null;
    $start = $_GET["start"] ?? null;
    $end = $_GET["end"] ?? null;

    $sql = "SELECT * FROM todos WHERE user_id=?";
    $params = [$user_id];

    if ($q !== "") {
      $sql .= " AND title LIKE ?";
      $params[] = "%$q%";
    }

    if ($date) {
      $sql .= " AND due_date = ?";
      $params[] = $date;
    } elseif ($start && $end) {
      $sql .= " AND (due_date BETWEEN ? AND ? OR due_date IS NULL)";
      $params[] = $start;
      $params[] = $end;
    }

    $sql .= " ORDER BY is_done ASC, updated_at DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    echo json_encode(["ok" => true, "todos" => $stmt->fetchAll()]);
    exit;
  }

  $data = json_decode(file_get_contents("php://input"), true) ?? [];

  if ($method === "POST") {
    $title = trim($data["title"] ?? "");
    $due_date = $data["due_date"] ?? null;

    $stmt = $pdo->prepare("INSERT INTO todos(user_id,title,due_date) VALUES (?,?,?)");
    $stmt->execute([$user_id, $title, $due_date ?: null]);

    echo json_encode(["ok" => true, "id" => (int)$pdo->lastInsertId()]);
    exit;
  }

  if ($method === "PUT") {
    $id = (int)($data["id"] ?? 0);
    $title = isset($data["title"]) ? trim($data["title"]) : null;
    $due_date = array_key_exists("due_date", $data) ? ($data["due_date"] ?: null) : null;
    $is_done = array_key_exists("is_done", $data) ? (int)!!$data["is_done"] : null;

    // Update only sent fields
    $sets = [];
    $params = [];

    if ($title !== null) { $sets[] = "title=?"; $params[] = $title; }
    if (array_key_exists("due_date", $data)) { $sets[] = "due_date=?"; $params[] = $due_date; }
    if ($is_done !== null) { $sets[] = "is_done=?"; $params[] = $is_done; }

    if (!$sets) {
      echo json_encode(["ok" => true]);
      exit;
    }

    $params[] = $id;
    $params[] = $user_id;

    $sql = "UPDATE todos SET " . implode(",", $sets) . " WHERE id=? AND user_id=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    echo json_encode(["ok" => true]);
    exit;
  }

  if ($method === "DELETE") {
    $id = (int)($data["id"] ?? 0);

    $stmt = $pdo->prepare("DELETE FROM todos WHERE id=? AND user_id=?");
    $stmt->execute([$id, $user_id]);

    echo json_encode(["ok" => true]);
    exit;
  }

  http_response_code(405);
  echo json_encode(["ok" => false, "error" => "Method not allowed"]);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(["ok" => false, "error" => "Server error"]);
}
