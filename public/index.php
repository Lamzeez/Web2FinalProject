<?php require_once "../includes/guest_guard.php"; ?>
<?php
// public/index.php
$page_title = "Welcome • NoteCore";
$show_nav = false;

// Big sticker logo for welcome page (kept responsive)
$show_logo = true;
$logo_variant = "hero";
$logo_sticker = true;

// Body class for page-specific styling
$body_class = "welcome-page";


require_once "../includes/header.php";
?>

<style>
  /* =========================
     Welcome Page (VERTICAL)
     ========================= */

  .welcome-page .wrap{
    padding-top: 10px;
    padding-bottom: 110px;
  }

  /* Tighten gap between logo and title */
  .welcome-page .brand{
    margin: 0 auto 4px !important;
  }
  .welcome-page .title{
    margin-top: 0 !important;
  }

  /* Logo: responsive but capped */
  .welcome-page .brand .logo-hero{
    width: clamp(220px, 32vw, 500px);
    height: clamp(220px, 32vw, 500px);
    border-radius: 42px;
  }

  /* Sticker mode */
  .welcome-page .logo-sticker{
    background: transparent !important;
    border: 0 !important;
    box-shadow: none !important;
    padding: 0 !important;
  }
  .welcome-page .logo-sticker img{
    width:100%;
    height:100%;
    object-fit: contain;
    display:block;
    filter: drop-shadow(0 18px 22px rgba(0,0,0,.22));
  }

  /* Shell */
  .welcome-shell{
    max-width: 980px;  /* narrower = cleaner vertical flow */
    margin: 0 auto;
  }

  /* Hero */
  .welcome-hero{
    text-align: center;
    padding: 6px 0 10px;
  }
  .welcome-hero .tagline{
    max-width: 840px;
    margin: 8px auto 0;
    line-height: 1.6;
    font-size: 16px;
  }

  /* Section spacing */
  .section{
    margin-top: 14px;
  }

  /* Features grid stays responsive */
  .feature-grid{
    display:grid;
    grid-template-columns: 1fr;
    gap: 12px;
    margin-top: 12px;
  }
  @media (min-width: 900px){
    .feature-grid{ grid-template-columns: repeat(3, 1fr); }
  }

  .feature-card{
    border-radius: var(--radius);
    background: rgba(255,255,255,.20);
    border: 1px solid rgba(255,255,255,.22);
    padding: 16px;
    backdrop-filter: blur(10px);
    box-shadow: 0 12px 26px rgba(0,0,0,.16);
    text-align: left;
    color: rgba(0,0,0,.78);
    min-height: 170px;
  }

  .feature-top{
    display:flex;
    align-items:center;
    gap:10px;
    margin-bottom: 8px;
  }
  .feature-icon{
    width:46px;
    height:46px;
    border-radius:14px;
    display:grid;
    place-items:center;
    background: rgba(0,0,0,.16);
    color:#fff;
    font-size:22px;
    flex: 0 0 auto;
  }
  .feature-card h3{
    margin:0;
    font-size:18px;
    font-weight:900;
    color: rgba(0,0,0,.82);
  }
  .feature-card p{
    margin: 8px 0 0;
    font-size: 14px;
    line-height: 1.5;
    opacity: .92;
  }

  /* Titles */
  .section-title{
    margin: 0;
    font-family: Georgia, serif;
    text-shadow: 0 2px 0 rgba(0,0,0,.18);
    text-align: center;
  }

  /* How it works panel content width */
  .how-lead{
    text-align:left;
    margin: 8px 0 0;
  }

  /* CTA panel */
  .cta-panel{
    text-align:center;
    padding: 22px 18px;
  }
  .cta-panel .tagline{
    max-width: 720px;
    margin: 10px auto 0;
  }
  .cta-buttons{
    display:flex;
    gap:10px;
    justify-content:center;
    flex-wrap:wrap;
    margin-top: 14px;
  }
  .cta-buttons a{
    text-decoration:none;
    display:inline-block;
    min-width: 220px;
  }
</style>

<div class="welcome-shell">

  <!-- TOP: Title + tagline -->
  <section class="welcome-hero">
    <!-- Logo renders from header.php -->
    <h1 class="title">NoteCore</h1>
    <p class="tagline">
      <b>You only have to think once, NoteCore remembers for you.</b><br>
      A calm, reliable space for your notes, tasks, and schedule — built to help you stay clear-headed and consistent.
    </p>
  </section>

  <!-- Next container: The 3 Core Features -->
  <section class="panel section">
    <h2 class="section-title">The 3 Core Features</h2>

    <div class="feature-grid">
      <div class="feature-card">
        <div class="feature-top">
          <div class="feature-icon">✍️</div>
          <div>
            <h3>Effortless Notes</h3>
            <div style="font-size:12px; opacity:.8;">Quick capture for ideas and thoughts</div>
          </div>
        </div>
        <p>Create, edit, delete, and search notes anytime — with date support for your calendar view.</p>
      </div>

      <div class="feature-card">
        <div class="feature-top">
          <div class="feature-icon">✅</div>
          <div>
            <h3>Gentle To-Dos</h3>
            <div style="font-size:12px; opacity:.8;">Tasks without the mental load</div>
          </div>
        </div>
        <p>Add tasks fast, set due dates, and mark done/undone instantly — all synced to Calendar Peace.</p>
      </div>

      <div class="feature-card">
        <div class="feature-top">
          <div class="feature-icon">🗓️</div>
          <div>
            <h3>Calendar Peace</h3>
            <div style="font-size:12px; opacity:.8;">Visualize your days clearly</div>
          </div>
        </div>
        <p>Monthly view with day badges, click a date to see notes/tasks, and add items directly.</p>
      </div>
    </div>
  </section>

  <!-- Next container: How it works -->
    <section class="panel section">
        <h2 class="section-title">How it works</h2>
        <div class="tagline" style="text-align:center; margin-top:8px;">Start in under a minute.</div>

        <style>
            /* How it works: 2-column steps, responsive */
            .how-steps{
            display:grid;
            grid-template-columns: 1fr;
            gap: 12px;
            margin-top: 14px;
            }
            @media (min-width: 900px){
            .how-steps{
                grid-template-columns: 1fr 1fr;
                gap: 14px;
            }
            }

            .how-col{
            display:flex;
            flex-direction:column;
            gap: 10px;
            }

            /* Make each item feel consistent height on desktop */
            @media (min-width: 900px){
            .how-col .item{
                min-height: 64px;
            }
            }
        </style>

        <div class="how-steps">
            <!-- Left column: 1, 2 -->
            <div class="how-col">
            <div class="item">
                <div>
                <b>1) Create an account</b>
                <div style="font-size:12px; opacity:.75;">Register with a username, email, password</div>
                </div>
                <div>🧾</div>
            </div>

            <div class="item">
                <div>
                <b>2) Capture & plan</b>
                <div style="font-size:12px; opacity:.75;">Add notes and tasks whenever they appear</div>
                </div>
                <div>✍️✅</div>
            </div>
            </div>

            <!-- Right column: 3, 4 -->
            <div class="how-col">
            <div class="item">
                <div>
                <b>3) Stay peaceful</b>
                <div style="font-size:12px; opacity:.75;">Use Calendar Peace to see your days clearly</div>
                </div>
                <div>🗓️</div>
            </div>

            <div class="item">
                <div>
                <b>4) Make it yours</b>
                <div style="font-size:12px; opacity:.75;">Adjust theme anytime in Profile Settings</div>
                </div>
                <div>🎨</div>
            </div>
            </div>
        </div>
    </section>


  <!-- Next container: CTA -->
  <section class="panel section cta-panel">
    <h2 class="section-title">Ready to feel organized — without stress?</h2>
    <p class="tagline">
      NoteCore helps you capture your thoughts, gently track what matters, and view your days with calm confidence.
    </p>

    <div class="cta-buttons">
      <a class="btn" href="register.php">Create an account</a>
      <a class="btn secondary" href="login.php">Login</a>
    </div>
  </section>

</div>

<?php require_once "../includes/footer.php"; ?>
