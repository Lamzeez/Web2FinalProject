<?php
// includes/header.php
if (session_status() === PHP_SESSION_NONE) session_start();

$page_title = $page_title ?? "NoteCore";
$show_nav   = $show_nav ?? false;     // show sidenav only on authenticated pages
$body_class = $body_class ?? "";

// active page auto-detect (so you don't have to manually set $active everywhere)
$curr = basename($_SERVER["PHP_SELF"]);
$active = match ($curr) {
  "home.php" => "home",
  "notes.php" => "notes",
  "todos.php" => "todos",
  "calendar.php" => "calendar",
  "profile.php" => "profile",
  default => "",
};

if ($show_nav) $body_class = trim($body_class . " has-sidenav");

// logo controls (your existing welcome hero uses this; sidebar always uses small logo)
$show_logo = $show_logo ?? false;
$logo_variant = $logo_variant ?? "normal";
$logo_sticker = $logo_sticker ?? false;
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title><?= htmlspecialchars($page_title) ?></title>
  <link rel="stylesheet" href="../assets/css/style.css" />
</head>

<body class="<?= htmlspecialchars($body_class) ?>">

<?php if ($show_nav): ?>
  <!-- Side Navbar -->
  <nav class="sidenav" aria-label="Primary">
    <!-- Logo (click -> Home) -->
    <a class="side-logo" href="home.php" title="Home">
      <img src="../assets/img/logo.png" alt="NoteCore logo">
    </a>

    <!-- 2 empty spaces -->
    <div class="side-gap"></div>
    <div class="side-gap"></div>

    <!-- Nav links (order you requested) -->
    <a class="side-link <?= $active === "notes" ? "active" : "" ?>" href="notes.php">
      <span class="side-ico">✍️</span>
      <span class="side-text">Effortless Notes</span>
    </a>

    <a class="side-link <?= $active === "todos" ? "active" : "" ?>" href="todos.php">
      <span class="side-ico">✅</span>
      <span class="side-text">Gentle To-Dos</span>
    </a>

    <a class="side-link <?= $active === "calendar" ? "active" : "" ?>" href="calendar.php">
      <span class="side-ico">🗓️</span>
      <span class="side-text">Calendar Peace</span>
    </a>

    <a class="side-link <?= $active === "profile" ? "active" : "" ?>" href="profile.php">
      <span class="side-ico">👤</span>
      <span class="side-text">Profile</span>
    </a>

    <a class="side-link <?= $active === "home" ? "active" : "" ?>" href="home.php">
      <span class="side-ico">🏠</span>
      <span class="side-text">Home</span>
    </a>

    <!-- Push logout to bottom -->
    <div class="side-push"></div>

    <a class="side-link side-logout" href="logout.php">
      <span class="side-ico">↩️</span>
      <span class="side-text">Logout</span>
    </a>
  </nav>
<?php endif; ?>

<main class="wrap">
  <?php
  // OPTIONAL: keep your existing welcome logo block if you still use it on index/login/register pages
  if ($show_logo):
  ?>
    <div class="brand">
      <div class="logo <?= $logo_variant === "hero" ? "logo-hero" : "" ?> <?= $logo_sticker ? "logo-sticker" : "" ?>">
        <img src="../assets/img/logo.png" alt="NoteCore logo">
      </div>
    </div>
  <?php endif; ?>
