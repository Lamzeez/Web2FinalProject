<?php
// api/notes.php
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

try {
  if ($method === "GET") {
    $q     = trim($_GET["q"] ?? "");
    $date  = $_GET["date"] ?? null;
    $start = $_GET["start"] ?? null;
    $end   = $_GET["end"] ?? null;

    $sql = "SELECT id, user_id, title, content, note_date, created_at, updated_at
            FROM notes
            WHERE user_id = ?";
    $params = [$user_id];

    if ($q !== "") {
      $sql .= " AND (title LIKE ? OR content LIKE ?)";
      $like = "%{$q}%";
      $params[] = $like;
      $params[] = $like;
    }

    if (!empty($date)) {
      $sql .= " AND note_date = ?";
      $params[] = $date;
    } elseif (!empty($start) && !empty($end)) {
      $sql .= " AND note_date BETWEEN ? AND ?";
      $params[] = $start;
      $params[] = $end;
    }

    $sql .= " ORDER BY updated_at DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    respond(200, ["ok" => true, "notes" => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
  }

  $data = read_json();

  if ($method === "POST") {
    $title = trim($data["title"] ?? "");
    $content = trim($data["content"] ?? "");
    $note_date = $data["note_date"] ?? date("Y-m-d");

    if ($title === "" || $content === "") {
      respond(400, ["ok" => false, "error" => "Title and content are required."]);
    }

    $stmt = $pdo->prepare(
      "INSERT INTO notes (user_id, title, content, note_date)
       VALUES (?, ?, ?, ?)"
    );
    $stmt->execute([$user_id, $title, $content, $note_date]);

    respond(201, ["ok" => true, "id" => (int)$pdo->lastInsertId()]);
  }

  if ($method === "PUT") {
    $id = (int)($data["id"] ?? 0);
    if ($id <= 0) respond(400, ["ok" => false, "error" => "Missing note id."]);

    $title = trim($data["title"] ?? "");
    $content = trim($data["content"] ?? "");
    $note_date = $data["note_date"] ?? null;

    // If note_date not provided, keep existing value (don’t change calendar day)
    if (empty($note_date)) {
      $keep = $pdo->prepare("SELECT note_date FROM notes WHERE id = ? AND user_id = ?");
      $keep->execute([$id, $user_id]);
      $note_date = $keep->fetchColumn() ?: date("Y-m-d");
    }

    if ($title === "" || $content === "") {
      respond(400, ["ok" => false, "error" => "Title and content are required."]);
    }

    $stmt = $pdo->prepare(
      "UPDATE notes
       SET title = ?, content = ?, note_date = ?
       WHERE id = ? AND user_id = ?"
    );
    $stmt->execute([$title, $content, $note_date, $id, $user_id]);

    respond(200, ["ok" => true]);
  }

  if ($method === "DELETE") {
    $id = (int)($data["id"] ?? 0);
    if ($id <= 0) respond(400, ["ok" => false, "error" => "Missing note id."]);

    $stmt = $pdo->prepare("DELETE FROM notes WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $user_id]);

    respond(200, ["ok" => true]);
  }

  respond(405, ["ok" => false, "error" => "Method not allowed"]);
} catch (Throwable $e) {
  respond(500, ["ok" => false, "error" => "Server error"]);
}
