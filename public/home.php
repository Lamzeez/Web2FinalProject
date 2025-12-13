<?php
// public/home.php
require_once "../includes/config.php";
require_once "../includes/auth.php";
require_login();

$page_title = "Home • NoteCore";
$show_nav = true;

$username = $_SESSION["username"] ?? "User";

$active = "todos";
require_once "../includes/header.php";
?>

<style>
  .home-shell{ max-width: 1100px; margin: 0 auto; }

  .home-top{
    display:flex; align-items:flex-start; justify-content:space-between;
    gap:14px; flex-wrap:wrap; margin-top:6px;
  }

  .home-title h1{
    margin:0; font-family:Georgia,serif; font-size:34px;
    text-shadow:0 3px 0 rgba(0,0,0,.18);
    color:rgba(255,255,255,.95);
  }
  .home-title p{
    margin:6px 0 0; color:rgba(255,255,255,.82);
    font-weight:700;
  }

  .quick-actions{ display:flex; gap:10px; flex-wrap:wrap; }
  .quick-actions a{ text-decoration:none; display:inline-block; }

  .grid-3{
    display:grid; grid-template-columns:1fr; gap:12px; margin-top:14px;
  }
  @media (min-width: 980px){ .grid-3{ grid-template-columns:repeat(3,1fr); } }

  .feature-link{ text-decoration:none; display:block; }

  .card-lite{
    border-radius: var(--radius);
    background: rgba(255,255,255,.20);
    border: 1px solid rgba(255,255,255,.22);
    padding: 16px;
    backdrop-filter: blur(10px);
    box-shadow: 0 12px 26px rgba(0,0,0,.16);
    color: rgba(0,0,0,.78);
    transition: transform .08s ease, background .12s ease;
    min-height: 120px;
  }
  .card-lite:hover{ background: rgba(255,255,255,.24); }
  .card-lite:active{ transform: translateY(1px); }

  .card-top{
    display:flex; align-items:center; justify-content:space-between; gap:12px;
  }
  .card-top h3{ margin:0; font-size:18px; font-weight:900; color:rgba(0,0,0,.82); }
  .card-top .sub{ font-size:12px; opacity:.75; margin-top:2px; font-weight:800; }

  .badge-ico{
    width:46px; height:46px; border-radius:14px; display:grid; place-items:center;
    background: rgba(0,0,0,.16); color:#fff; font-size:22px; flex:0 0 auto;
  }

  .stats{
    display:grid; grid-template-columns:1fr; gap:12px; margin-top:12px;
  }
  @media (min-width: 980px){ .stats{ grid-template-columns:repeat(3,1fr); } }

  .stat{
    padding:14px; border-radius:18px;
    background: rgba(0,0,0,.16);
    border: 1px solid rgba(255,255,255,.14);
    color: rgba(255,255,255,.92);
    box-shadow: 0 12px 24px rgba(0,0,0,.14);
  }
  .stat b{ display:block; font-size:12px; opacity:.85; margin-bottom:6px; letter-spacing:.2px; }
  .stat .num{ font-size:28px; font-weight:1000; line-height:1; }
  .stat .hint{ margin-top:8px; font-size:12px; opacity:.8; }

  .mini-item{
    display:flex; align-items:center; justify-content:space-between; gap:10px;
    padding:12px 12px; border-radius:16px;
    background: rgba(255,255,255,.16);
    border: 1px solid rgba(255,255,255,.18);
    color: rgba(0,0,0,.78);
  }
  .mini-item .meta{ font-size:12px; opacity:.75; margin-top:3px; font-weight:700; }

  /* Today panels: fixed height + scroll list */
  .today-row{
    display:grid; grid-template-columns:1fr; gap:12px; margin-top:12px;
  }
  @media (min-width: 980px){ .today-row{ grid-template-columns:1fr 1fr; } }

  .today-panel{
    height: 320px;
    padding: 14px !important;
    display:flex;
    flex-direction:column;
  }
  .today-panel h2{
    font-size:30px;
    margin:0 0 4px !important;
    font-family: Georgia, serif;
    text-shadow: 0 2px 0 rgba(0,0,0,.18);
  }
  .today-panel .tagline{
    margin: 2px 0 10px !important;
    text-align:left;
  }

  .today-list{
    flex:1;
    overflow-y:auto;
    padding-right:4px;
    display:flex;
    flex-direction:column;
    gap:10px;
  }
  .today-list .mini-item{ padding:10px 12px !important; min-height:70px; }

  .today-list::-webkit-scrollbar{ width:8px; }
  .today-list::-webkit-scrollbar-thumb{
    background: rgba(0,0,0,.18);
    border-radius: 999px;
  }
</style>

<div class="home-shell">
  <div class="home-top">
    <div class="home-title">
      <h1>Welcome, <?= htmlspecialchars($username) ?>!</h1>
      <p>You only have to think once, NoteCore remembers for you ✨</p>
    </div>

    <div class="quick-actions">
      <a class="btn secondary" href="notes.php">Open Notes</a>
      <a class="btn secondary" href="todos.php">Open To-Dos</a>
      <a class="btn secondary" href="calendar.php">Open Calendar</a>
    </div>
  </div>

  <div class="panel" style="margin-top:14px;">
    <div style="text-align:center;">
      <h2 style="margin:0; font-family:Georgia,serif; text-shadow:0 2px 0 rgba(0,0,0,.18);">
        Your Space, Organized
      </h2>
      <div class="tagline" style="margin-top:6px;">
        Jump into what you need today — notes, tasks, or your schedule.
      </div>
    </div>

    <div class="grid-3">
      <a class="feature-link" href="notes.php">
        <div class="card-lite">
          <div class="card-top">
            <div>
              <h3>Effortless Notes</h3>
              <div class="sub">Capture thoughts, clear your mind</div>
            </div>
            <div class="badge-ico">✍️</div>
          </div>
        </div>
      </a>

      <a class="feature-link" href="todos.php">
        <div class="card-lite">
          <div class="card-top">
            <div>
              <h3>Gentle To-Dos</h3>
              <div class="sub">Tasks without the mental load</div>
            </div>
            <div class="badge-ico">✅</div>
          </div>
        </div>
      </a>

      <a class="feature-link" href="calendar.php">
        <div class="card-lite">
          <div class="card-top">
            <div>
              <h3>Calendar Peace</h3>
              <div class="sub">See notes & tasks by day</div>
            </div>
            <div class="badge-ico">🗓️</div>
          </div>
        </div>
      </a>
    </div>
  </div>

  <div class="stats">
    <div class="stat">
      <b>Total Notes</b>
      <div class="num" id="statNotes">—</div>
      <div class="hint">All notes you’ve created.</div>
    </div>

    <div class="stat">
      <b>Pending Tasks</b>
      <div class="num" id="statPending">—</div>
      <div class="hint">To-dos not yet completed.</div>
    </div>

    <div class="stat">
      <b>Today</b>
      <div class="num" id="statToday">—</div>
      <div class="hint">Notes/tasks scheduled today.</div>
    </div>
  </div>

  <div class="today-row">
    <div class="panel today-panel">
      <h2>Today’s Notes</h2>
      <div class="tagline">What you captured today.</div>

      <div class="today-list" id="todayNotes">
        <div class="mini-item"><div><b>Loading…</b></div><div>⏳</div></div>
      </div>
    </div>

    <div class="panel today-panel">
      <h2>Today’s Tasks</h2>
      <div class="tagline">Due today.</div>

      <div class="today-list" id="todayTodos">
        <div class="mini-item"><div><b>Loading…</b></div><div>⏳</div></div>
      </div>
    </div>
  </div>
</div>

<?php require_once "../includes/footer.php"; ?>
