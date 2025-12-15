<?php require_once "../includes/guest_guard.php"; ?>
<?php
require_once "../includes/config.php";

$page_title = "Login • NoteCore";
$show_nav = false;

$error = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $username = trim($_POST["username"] ?? "");
  $password = $_POST["password"] ?? "";

  $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
  $stmt->execute([$username]);
  $user = $stmt->fetch();

  $success_redirect = "";

  if ($user && password_verify($password, $user["password_hash"])) {
    $_SESSION["user_id"] = (int)$user["id"];
    $_SESSION["username"] = $user["username"];
    $_SESSION["theme"] = $user["theme"];

    // ✅ show success modal first, then redirect via JS
    $success_redirect = "home.php";
  } else {
    $error = "Invalid username or password.";
  }

  // $error = "Invalid username or password.";
}

require_once "../includes/header.php";
?>
<div class="brand">
  <a class="side-logo">
    <img src="../assets/img/logo.png" alt="NoteCore logo">
  </a>
</div>

<br>

<h1 class="title" style="font-size:44px;">NoteCore</h1>
<p class="tagline">You only have to think once, NoteCore remembers for you ✨</p>

<div class="panel" style="max-width:560px;">
  <h2 style="text-align:center; margin:6px 0 14px; text-shadow:0 2px 0 rgba(0,0,0,.2); font-family:Georgia,serif;">Login</h2>

  <?php if ($error): ?>
    <div class="card" style="background: rgba(225,63,64,.25);"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="post" style="display:flex; flex-direction:column; gap:10px;">
    <input class="input" name="username" placeholder="Username" required />
    <div class="field pw-field">
      <input class="input" type="password" name="password" id="login_password" placeholder="Password" required>
      <button type="button" class="pw-toggle" data-target="login_password" aria-label="Show password">👁</button>
    </div>



    <button class="btn" type="submit">Login</button>
  </form>

  <p style="text-align:center; margin-top:12px; color:var(--muted);">
    Don’t have an account yet? <a href="register.php" style="color:#1a58ff;">Register here</a>
  </p>
</div>

<?php if (!empty($success_redirect)): ?>
  <script>
    document.addEventListener("DOMContentLoaded", async () => {
      if (typeof ncDialogShow === "function") {
        await ncDialogShow({
          title: "Welcome back!",
          sub: "Login successful. Redirecting…",
          state: "success",
          duration: 900,
        });
      }
      window.location.href = "<?= $success_redirect ?>";
    });
  </script>
<?php endif; ?>

<?php require_once "../includes/footer.php"; ?>
