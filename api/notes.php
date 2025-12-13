<?php
// api/notes.php
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

    $sql = "SELECT * FROM notes WHERE user_id=?";
    $params = [$user_id];

    if ($q !== "") {
      $sql .= " AND (title LIKE ? OR content LIKE ?)";
      $like = "%$q%";
      $params[] = $like;
      $params[] = $like;
    }

    if ($date) {
      $sql .= " AND note_date = ?";
      $params[] = $date;
    } elseif ($start && $end) {
      $sql .= " AND note_date BETWEEN ? AND ?";
      $params[] = $start;
      $params[] = $end;
    }

    $sql .= " ORDER BY updated_at DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    echo json_encode(["ok" => true, "notes" => $stmt->fetchAll()]);
    exit;
  }

  $data = json_decode(file_get_contents("php://input"), true) ?? [];

  if ($method === "POST") {
    $title = trim($data["title"] ?? "");
    $content = trim($data["content"] ?? "");
    $note_date = $data["note_date"] ?? date("Y-m-d");

    $stmt = $pdo->prepare("INSERT INTO notes(user_id,title,content,note_date) VALUES (?,?,?,?)");
    $stmt->execute([$user_id, $title, $content, $note_date]);

    echo json_encode(["ok" => true, "id" => (int)$pdo->lastInsertId()]);
    exit;
  }

  if ($method === "PUT") {
    $id = (int)($data["id"] ?? 0);
    $title = trim($data["title"] ?? "");
    $content = trim($data["content"] ?? "");
    $note_date = $data["note_date"] ?? date("Y-m-d");

    $stmt = $pdo->prepare("UPDATE notes SET title=?, content=?, note_date=? WHERE id=? AND user_id=?");
    $stmt->execute([$title, $content, $note_date, $id, $user_id]);

    echo json_encode(["ok" => true]);
    exit;
  }

  if ($method === "DELETE") {
    $id = (int)($data["id"] ?? 0);

    $stmt = $pdo->prepare("DELETE FROM notes WHERE id=? AND user_id=?");
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
