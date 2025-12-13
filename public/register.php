<?php require_once "../includes/guest_guard.php"; ?>
<?php
require_once "../includes/config.php";

$page_title = "Register • NoteCore";
$show_nav = false;

$error = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $username = trim($_POST["username"] ?? "");
  $email = trim($_POST["email"] ?? "");
  $pass = $_POST["password"] ?? "";
  $confirm = $_POST["confirm_password"] ?? "";

  if ($pass !== $confirm) {
    $error = "Passwords do not match.";
  }
  $password = $_POST["password"] ?? "";

  if (strlen($username) < 3 || strlen($password) < 6) {
    $error = "Username (3+) and password (6+) required.";
  } else {
    try {
      $stmt = $pdo->prepare("INSERT INTO users(username,email,password_hash) VALUES (?,?,?)");
      $stmt->execute([$username, $email, password_hash($password, PASSWORD_DEFAULT)]);
      header("Location: login.php");
      exit;
    } catch (Throwable $e) {
      $error = "Username or email already exists.";
    }
  }
}

require_once "../includes/header.php";
?>
<div class="brand"><div class="logo">📄✅</div></div>
<h1 class="title" style="font-size:44px;">NoteCore</h1>
<p class="tagline">You only have to think once, NoteCore remembers for you ✨</p>

<div class="panel" style="max-width:560px;">
  <h2 style="text-align:center; margin:6px 0 14px; font-family:Georgia,serif;">Register</h2>

  <?php if ($error): ?>
    <div class="card" style="background: rgba(225,63,64,.25);"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="post" style="display:flex; flex-direction:column; gap:10px;">
    <input class="input" name="username" placeholder="Username" required />

    <div class="field pw-field">
      <input class="input" type="password" name="password" id="register_password" placeholder="Password" required>
      <button type="button" class="pw-toggle" data-target="register_password" aria-label="Show password">👁</button>
    </div>

    <div class="field pw-field">
      <input class="input" type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required>
      <button type="button" class="pw-toggle" data-target="confirm_password" aria-label="Show password">👁</button>
    </div>

    <input class="input" name="email" placeholder="Email" type="email" required />
    <button class="btn" type="submit">Register</button>
  </form>

  <p style="text-align:center; margin-top:12px; color:var(--muted);">
    Already have an account? <a href="login.php" style="color:#1a58ff;">Login here</a>
  </p>
</div>

<?php require_once "../includes/footer.php"; ?>
