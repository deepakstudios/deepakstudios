<?php
/**
 * cinematography.php — Cinematography Hub | Deepak Studios
 *
 * Premium luxury cinematic hub. Shows SIX category cards in the dark +
 * gold studio theme (navbar/header match index.html):
 *
 *   Wedding        → wedding.php        (LIVE)
 *   Pre-Wedding    → prewedding.php     (LIVE — videos-only page)
 *   Engagement     → "Coming Soon" card
 *   Anniversary    → "Coming Soon" card
 *   Birthday       → "Coming Soon" card
 *   Housewarming   → "Coming Soon" card
 *
 * The four "Coming Soon" cards are intentionally NOT links yet — they are
 * static "coming soon" tiles so visitors never hit a 404. Reels stay on
 * prewedding_videos.php (linked from the nav + the ribbon below).
 */
declare(strict_types=1);

header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cinematography | Deepak Studios</title>
<meta name="description" content="Luxury wedding cinematography by Deepak Studios — cinematic wedding films, pre-wedding stories, engagement, anniversary, birthday and housewarming films.">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet">
<link rel="preload" as="image" href="https://images.unsplash.com/photo-1485846234645-a62644f84728?q=80&w=2070&auto=format&fit=crop">
<style>
/* ============ tokens (same palette as Home) ============ */
:root{
  --bg:#ffffff; --fg:#09090b; --muted:#71717a; --card:#fafafa; --border:#e4e4e7;
  --primary:#D4AF37; --primary-fg:#09090b;
  --ink:#f5efe0; --ink-dim:#a1a1aa; --line:rgba(255,255,255,.10);
  --night:#080808; --night2:#0d0d0d;
}
html.dark{
  --bg:#09090b; --fg:#fafafa; --muted:#a1a1aa; --card:#18181b; --border:rgba(255,255,255,.10);
  --primary:#D4AF37; --primary-fg:#09090b;
}
*{box-sizing:border-box}
html{scroll-behavior:smooth}
body{margin:0;background:var(--night);color:var(--ink);
  font-family:Inter,system-ui,-apple-system,"Segoe UI",sans-serif;
  -webkit-font-smoothing:antialiased;overflow-x:hidden}
/* subtle film grain */
body::after{content:"";position:fixed;inset:0;z-index:1;pointer-events:none;opacity:.05;
  background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='140' height='140'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.9' numOctaves='2'/%3E%3C/filter%3E%3Crect width='140' height='140' filter='url(%23n)' opacity='.6'/%3E%3C/svg%3E")}
img{max-width:100%;display:block}
a{color:inherit;text-decoration:none}
::selection{background:rgba(212,175,55,.3);color:var(--primary)}
.pf{font-family:"Playfair Display",Georgia,serif}
.container{max-width:1280px;margin:0 auto;padding:0 1rem}
@media(min-width:768px){.container{padding:0 1.5rem}}
.gold{color:var(--primary)}

/* ============ header (mirrors Home page) ============ */
header{position:fixed;top:0;left:0;right:0;z-index:50;padding:1.25rem 0;transition:all .3s;background:transparent}
header.scrolled{padding:.75rem 0;background:color-mix(in srgb,var(--bg) 85%,transparent);
  backdrop-filter:blur(12px);border-bottom:1px solid var(--border)}
.hdr{display:flex;align-items:center;justify-content:space-between}
.logo-name{font-size:1.5rem;font-weight:700;line-height:1.1;transition:color .2s}
@media(min-width:768px){.logo-name{font-size:1.875rem}}
.brand:hover .logo-name{color:var(--primary)}
.logo-sub{display:block;font-size:10px;text-transform:uppercase;letter-spacing:.2em;
  color:color-mix(in srgb,var(--primary) 80%,transparent)}
nav.desk{display:none;align-items:center;gap:2rem}
@media(min-width:768px){nav.desk{display:flex}}
nav.desk ul{display:flex;align-items:center;gap:1.5rem;list-style:none;margin:0;padding:0}
nav.desk a.nl{position:relative;font-size:.875rem;font-weight:500;color:var(--muted);padding-bottom:.25rem;transition:color .2s}
nav.desk a.nl::after{content:"";position:absolute;left:0;bottom:0;height:2px;width:0;background:var(--primary);transition:width .3s}
nav.desk a.nl:hover{color:var(--primary)}
nav.desk a.nl:hover::after{width:100%}
nav.desk a.nl.active{color:var(--primary)}
nav.desk a.nl.active::after{width:100%}
.btn{display:inline-flex;align-items:center;justify-content:center;gap:.5rem;border:0;cursor:pointer;
  font-family:inherit;white-space:nowrap;transition:all .3s}
.btn-gold{background:var(--primary);color:var(--primary-fg);font-weight:600;border-radius:999px;
  padding:.5rem 1.5rem;font-size:.875rem;box-shadow:0 0 15px rgba(212,175,55,.4)}
.btn-gold:hover{background:color-mix(in srgb,var(--primary) 90%,black);box-shadow:0 0 25px rgba(212,175,55,.6)}
.icon-btn{width:2rem;height:2rem;border-radius:999px;background:transparent;color:var(--muted);cursor:pointer;border:0;
  display:inline-flex;align-items:center;justify-content:center;transition:all .2s}
.icon-btn:hover{color:var(--primary);background:color-mix(in srgb,var(--fg) 8%,transparent)}
.mob-actions{display:flex;align-items:center;gap:.5rem}
@media(min-width:768px){.mob-actions{display:none}}
#mobmenu{display:none;background:var(--bg);border-top:1px solid var(--border)}
#mobmenu.open{display:block}
#mobmenu a{display:block;padding:1rem 1.5rem;border-bottom:1px solid var(--border);font-weight:500}
#mobmenu a:hover{color:var(--primary)}
#mobmenu a.active{color:var(--primary)}
.sun,.moon{width:1.2rem;height:1.2rem}
html.dark .sun{display:none} html:not(.dark) .moon{display:none}

/* ============ cinematic hero ============ */
.hero{position:relative;min-height:78vh;display:flex;align-items:center;justify-content:center;
  overflow:hidden;padding:8.5rem 1rem 4.5rem}
.hero-bg{position:absolute;inset:0;background-size:cover;background-position:center;
  background-image:url('https://images.unsplash.com/photo-1485846234645-a62644f84728?q=80&w=2070&auto=format&fit=crop');
  animation:kb 26s ease-in-out infinite alternate}
@keyframes kb{from{transform:scale(1)}to{transform:scale(1.06)}}
.hero-shade{position:absolute;inset:0;z-index:10;background:rgba(0,0,0,.55)}
.hero-grade{position:absolute;inset:0;z-index:10;
  background:linear-gradient(to top,var(--night) 2%,rgba(8,8,8,.55) 38%,rgba(8,8,8,.25) 65%,rgba(8,8,8,.55)),
             radial-gradient(60% 45% at 50% 62%,rgba(212,175,55,.14),transparent 70%)}
.hero-vig{position:absolute;inset:0;z-index:10;box-shadow:inset 0 0 180px 60px rgba(0,0,0,.75);pointer-events:none}
.hero-in{position:relative;z-index:20;text-align:center;max-width:56rem;display:flex;flex-direction:column;align-items:center}
.hero-in>*{animation:rise .8s both}
.hero-in>*:nth-child(1){animation-delay:.05s}
.hero-in>*:nth-child(2){animation-delay:.15s}
.hero-in>*:nth-child(3){animation-delay:.25s}
.hero-in>*:nth-child(4){animation-delay:.35s}
.hero-in>*:nth-child(5){animation-delay:.45s}
@keyframes rise{from{opacity:0;transform:translateY(24px)}to{opacity:1;transform:none}}
@media(prefers-reduced-motion:reduce){.hero-bg,.hero-in>*{animation:none}}
.crumbs{font-size:.8rem;letter-spacing:.18em;text-transform:uppercase;color:#b9b3a6;margin-bottom:1.25rem}
.crumbs a{color:var(--primary)}
.crumbs a:hover{text-decoration:underline}
.hero h1{font-size:clamp(2.9rem,9vw,6.5rem);font-weight:700;margin:0;line-height:1.04;letter-spacing:.01em;
  background:linear-gradient(to bottom,#FFF5D1,#D4AF37 55%,#8B7322);-webkit-background-clip:text;background-clip:text;
  color:transparent;filter:drop-shadow(0 5px 5px rgba(0,0,0,.8))}
.hero-rule{display:flex;align-items:center;gap:.9rem;margin:1.6rem 0 1.3rem;color:var(--primary)}
.hero-rule::before,.hero-rule::after{content:"";height:1px;width:clamp(3rem,12vw,7rem);
  background:linear-gradient(to right,transparent,var(--primary))}
.hero-rule::after{background:linear-gradient(to left,transparent,var(--primary))}
.hero-rule svg{width:1.4rem;height:1.4rem}
.hero-sub{font-family:"Playfair Display",Georgia,serif;font-style:italic;
  font-size:clamp(1.15rem,3vw,1.6rem);color:#e9e2d2;margin:0;text-shadow:0 2px 8px rgba(0,0,0,.7)}
.hero-tag{font-size:.95rem;font-weight:300;letter-spacing:.14em;text-transform:uppercase;color:#b9b3a6;margin:1rem 0 0}

/* ============ films section ============ */
main{position:relative;z-index:2}
.films{padding:4.5rem 0 3.5rem;position:relative;overflow:hidden;
  background:radial-gradient(50% 30% at 50% 0%,rgba(212,175,55,.07),transparent 70%),var(--night)}
.sec-head{text-align:center;margin-bottom:3rem}
.eyebrow{font-size:.72rem;font-weight:600;letter-spacing:.32em;text-transform:uppercase;color:var(--primary);margin:0 0 1rem}
.sec-head h2{font-size:clamp(1.9rem,5vw,2.9rem);font-weight:700;margin:0}
.sec-head h2 .gold{color:var(--primary)}

.grid{display:grid;grid-template-columns:1fr;gap:1.5rem}
@media(min-width:640px){.grid{grid-template-columns:repeat(2,1fr)}}
@media(min-width:1024px){.grid{grid-template-columns:repeat(3,1fr)}}

/* premium cinematic cards */
.card{position:relative;display:block;border-radius:1rem;overflow:hidden;text-decoration:none;color:var(--ink);
  background:var(--night2);border:1px solid rgba(212,175,55,.22);
  box-shadow:0 18px 45px -20px rgba(0,0,0,.8);
  transition:transform .35s ease,border-color .35s ease,box-shadow .35s ease}
a.card:hover,a.card:focus-visible{transform:translateY(-6px);border-color:rgba(212,175,55,.65);
  box-shadow:0 24px 55px -18px rgba(0,0,0,.85),0 0 32px rgba(212,175,55,.18)}
.card-media{position:relative;aspect-ratio:3/4;overflow:hidden}
.card-media img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;
  transition:transform .7s ease;transform:scale(1)}
a.card:hover .card-media img{transform:scale(1.06)}
.card-shade{position:absolute;inset:0;
  background:linear-gradient(to top,rgba(5,5,5,.96) 0%,rgba(5,5,5,.55) 42%,rgba(5,5,5,.05) 68%,rgba(5,5,5,.25) 100%)}
.badge{position:absolute;top:1rem;right:1rem;z-index:3;padding:.32rem .8rem;
  font-size:.64rem;font-weight:600;letter-spacing:.18em;text-transform:uppercase;border-radius:999px;
  border:1px solid rgba(212,175,55,.7);color:#f0d896;background:rgba(10,10,10,.45);
  backdrop-filter:blur(8px);-webkit-backdrop-filter:blur(8px)}
.badge.live{border-color:rgba(111,174,122,.8);color:#9fd3a8;background:rgba(8,18,10,.5)}
.card-body{position:absolute;left:0;right:0;bottom:0;z-index:2;padding:1.6rem 1.5rem 1.4rem;text-align:left}
.card-ico{display:inline-flex;align-items:center;justify-content:center;width:2.6rem;height:2.6rem;
  border-radius:999px;color:var(--primary);border:1px solid rgba(212,175,55,.45);
  background:rgba(10,10,10,.5);backdrop-filter:blur(6px);-webkit-backdrop-filter:blur(6px);margin-bottom:1rem}
.card-ico svg{width:1.25rem;height:1.25rem}
.card h3{font-family:"Playfair Display",Georgia,serif;font-size:1.55rem;font-weight:700;margin:0 0 .5rem;letter-spacing:.01em}
a.card:hover h3{color:var(--primary)}
.card p{margin:0;color:#c9c2b2;font-size:.9rem;line-height:1.65;font-weight:300}
.go{display:inline-flex;align-items:center;gap:.45rem;margin-top:1.1rem;color:var(--primary);
  font-size:.78rem;font-weight:600;letter-spacing:.16em;text-transform:uppercase}
.go .arr{transition:transform .3s;display:inline-block}
a.card:hover .go .arr{transform:translateX(4px)}
.go.dim{color:#6a6a6a}
.soon{cursor:default}

/* scroll reveal */
html.has-js .rv{opacity:0;transform:translateY(26px);transition:opacity .7s ease,transform .7s ease}
html.has-js .rv.in{opacity:1;transform:none}
@media(prefers-reduced-motion:reduce){html.has-js .rv{opacity:1;transform:none;transition:none}}

/* ============ reels ribbon ============ */
.ribbon{margin-top:3rem;text-align:center;padding:2.4rem 1.5rem;border-radius:1rem;position:relative;overflow:hidden;
  border:1px solid rgba(212,175,55,.3);background:linear-gradient(180deg,rgba(212,175,55,.08),rgba(13,13,13,.9) 70%)}
.ribbon p{margin:0 0 1.2rem;color:#c9c2b2;font-size:.95rem}
.ribbon .pf{font-size:1.4rem;color:var(--ink);display:block;margin-bottom:.4rem}

/* ============ footer ============ */
footer{border-top:1px solid var(--line);padding:2rem 1rem;text-align:center;color:#6a6a6a;font-size:.85rem;
  background:#060606;position:relative;z-index:2}
footer .fname{font-family:"Playfair Display",serif;color:var(--primary);font-weight:700}
</style>
</head>
<body>

<!-- HEADER (same structure/styling as Home page) -->
<header id="hdr">
  <div class="container hdr">
    <a href="index.html" class="brand">
      <span class="logo-name pf">Deepak Studios</span>
      <span class="logo-sub">Cinematic Photography</span>
    </a>
    <nav class="desk">
      <ul>
        <li><a class="nl" href="index.html">Home</a></li>
        <li><a class="nl" href="index.html#portfolio">Photography</a></li>
        <li><a class="nl active" href="cinematography.php" aria-current="page">Cinematography</a></li>
        <li><a class="nl" href="prewedding_videos.php">Reels</a></li>
        <li><a class="nl" href="index.html#contact">Contact Us</a></li>
      </ul>
      <a href="tel:+919031700464" class="btn btn-gold">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"/></svg>
        Call Now
      </a>
      <button class="icon-btn" onclick="toggleTheme()" aria-label="Toggle theme">
        <svg class="sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/></svg>
        <svg class="moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.985 12.486a9 9 0 1 1-9.473-9.472c.405-.022.617.46.402.803a6 6 0 0 0 8.268 8.268c.344-.215.825-.004.803.401"/></svg>
      </button>
    </nav>
    <div class="mob-actions">
      <button class="icon-btn" onclick="toggleTheme()" aria-label="Toggle theme">
        <svg class="sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/></svg>
        <svg class="moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.985 12.486a9 9 0 1 1-9.473-9.472c.405-.022.617.46.402.803a6 6 0 0 0 8.268 8.268c.344-.215.825-.004.803.401"/></svg>
      </button>
      <button class="icon-btn" onclick="document.getElementById('mobmenu').classList.toggle('open')" aria-label="Menu">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 5h16M4 12h16M4 19h16"/></svg>
      </button>
    </div>
  </div>
  <div id="mobmenu">
    <a href="index.html">Home</a>
    <a href="index.html#portfolio">Photography</a>
    <a href="cinematography.php" class="active">Cinematography</a>
    <a href="prewedding_videos.php">Reels</a>
    <a href="index.html#contact">Contact Us</a>
  </div>
</header>

<!-- CINEMATIC HERO -->
<section class="hero">
  <div class="hero-bg" role="img" aria-label="Professional cinema camera on a wedding film set"></div>
  <div class="hero-shade"></div><div class="hero-grade"></div><div class="hero-vig"></div>
  <div class="hero-in">
    <div class="crumbs"><a href="index.html">Home</a> &nbsp;&rsaquo;&nbsp; Cinematography</div>
    <h1 class="pf">Cinematography</h1>
    <div class="hero-rule" aria-hidden="true">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M7 3v18M3 7.5h4M3 12h18M3 16.5h4M17 3v18M17 7.5h4M17 16.5h4"/></svg>
    </div>
    <p class="hero-sub">Cinematic Films &amp; Short Films</p>
    <p class="hero-tag">Wedding stories told like cinema</p>
  </div>
</section>

<main>
  <!-- FILMS -->
  <section class="films">
    <div class="container">
      <div class="sec-head rv">
        <p class="eyebrow">Select your story</p>
        <h2 class="pf">Every Occasion, <span class="gold">Filmed Like Cinema</span></h2>
      </div>

      <div class="grid">

        <a class="card rv" href="wedding.php">
          <div class="card-media">
            <img loading="lazy" decoding="async" src="https://images.unsplash.com/photo-1519741497674-611481863552?q=80&w=1200&auto=format&fit=crop" alt="Cinematic wedding couple film by Deepak Studios">
            <div class="card-shade"></div>
            <span class="badge live">Live</span>
            <div class="card-body">
              <span class="card-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="14" r="5"/><circle cx="15" cy="10" r="5"/></svg></span>
              <h3>Wedding</h3>
              <p>Full-day cinematic coverage — vows, rituals, baraat &amp; the moments in between.</p>
              <span class="go">Explore Wedding Films <span class="arr">&rarr;</span></span>
            </div>
          </div>
        </a>

        <a class="card rv" href="prewedding.php">
          <div class="card-media">
            <img loading="lazy" decoding="async" src="https://images.unsplash.com/photo-1606800052052-a08af7148866?q=80&w=1200&auto=format&fit=crop" alt="Romantic pre-wedding couple film by Deepak Studios">
            <div class="card-shade"></div>
            <span class="badge live">Live</span>
            <div class="card-body">
              <span class="card-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M2 9.5a5.5 5.5 0 0 1 9.591-3.676.56.56 0 0 0 .818 0A5.49 5.49 0 0 1 22 9.5c0 2.29-1.5 4-3 5.5l-5.492 5.313a2 2 0 0 1-3 .019L5 15c-1.5-1.5-3-3.2-3-5.5"/></svg></span>
              <h3>Pre-Wedding</h3>
              <p>Your love story before the big day — dreamy films of the couple.</p>
              <span class="go">Watch Pre-Wedding Films <span class="arr">&rarr;</span></span>
            </div>
          </div>
        </a>

        <div class="card soon rv">
          <div class="card-media">
            <img loading="lazy" decoding="async" src="https://images.unsplash.com/photo-1605100804763-247f67b3557e?q=80&w=1200&auto=format&fit=crop" alt="Engagement ring cinematic still">
            <div class="card-shade"></div>
            <span class="badge">Coming Soon</span>
            <div class="card-body">
              <span class="card-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="14" r="6"/><path d="M9.5 9.5 11 6l1 2 1-2 1.5 3.5"/></svg></span>
              <h3>Engagement</h3>
              <p>Ring moments &amp; proposal stories. Coming soon.</p>
              <span class="go dim">Coming Soon</span>
            </div>
          </div>
        </div>

        <div class="card soon rv">
          <div class="card-media">
            <img loading="lazy" decoding="async" src="https://images.unsplash.com/photo-1465495976277-4387d4b0b4c6?q=80&w=1200&auto=format&fit=crop" alt="Romantic anniversary celebration cinematic still">
            <div class="card-shade"></div>
            <span class="badge">Coming Soon</span>
            <div class="card-body">
              <span class="card-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v3M12 18v3M3 12h3M18 12h3M5.6 5.6l2.1 2.1M16.3 16.3l2.1 2.1M5.6 18.4l2.1-2.1M16.3 7.7l2.1-2.1"/></svg></span>
              <h3>Anniversary</h3>
              <p>Celebrating years of togetherness. Coming soon.</p>
              <span class="go dim">Coming Soon</span>
            </div>
          </div>
        </div>

        <div class="card soon rv">
          <div class="card-media">
            <img loading="lazy" decoding="async" src="https://images.unsplash.com/photo-1530103862676-de8c9debad1d?q=80&w=1200&auto=format&fit=crop" alt="Elegant birthday celebration cinematic still">
            <div class="card-shade"></div>
            <span class="badge">Coming Soon</span>
            <div class="card-body">
              <span class="card-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 21h16M5 21v-6a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v6M12 13V9M12 9a1.5 1.5 0 0 0 1.5-1.5M12 9a1.5 1.5 0 0 1-1.5-1.5M12 9v.5M8 13v-2M16 13v-2M8 11V9.5M16 11V9.5"/></svg></span>
              <h3>Birthday</h3>
              <p>Fun-filled birthday celebrations. Coming soon.</p>
              <span class="go dim">Coming Soon</span>
            </div>
          </div>
        </div>

        <div class="card soon rv">
          <div class="card-media">
            <img loading="lazy" decoding="async" src="https://images.unsplash.com/photo-1560518883-ce09059eeffa?q=80&w=1200&auto=format&fit=crop" alt="Beautiful new home celebration cinematic still">
            <div class="card-shade"></div>
            <span class="badge">Coming Soon</span>
            <div class="card-body">
              <span class="card-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 11.5 12 4l9 7.5M5.5 10v10h13V10"/></svg></span>
              <h3>Housewarming</h3>
              <p>New-home &amp; Griha Pravesh celebrations. Coming soon.</p>
              <span class="go dim">Coming Soon</span>
            </div>
          </div>
        </div>

      </div>

      <div class="ribbon rv">
        <span class="pf">Short-form highlights from every shoot?</span>
        <p>Cutdowns, teasers and reels — crafted for sharing.</p>
        <a class="btn btn-gold" href="prewedding_videos.php">Browse Reels &rarr;</a>
      </div>
    </div>
  </section>
</main>

<footer><span class="fname">Deepak Studios</span> &nbsp;·&nbsp; &copy; <?= date('Y') ?> All rights reserved.</footer>

<script>
/* theme (same as Home page) */
(function(){ try{ var t=localStorage.getItem('theme'); if(t==='light') document.documentElement.classList.remove('dark'); }catch(e){} })();
function toggleTheme(){
  var d=document.documentElement.classList.toggle('dark');
  try{ localStorage.setItem('theme', d?'dark':'light'); }catch(e){}
}
/* shrinking header on scroll (same as Home page) */
(function(){
  var h=document.getElementById('hdr');
  function onScroll(){ if(window.scrollY>30){h.classList.add('scrolled');}else{h.classList.remove('scrolled');} }
  window.addEventListener('scroll',onScroll,{passive:true}); onScroll();
})();
/* staggered card reveal */
document.documentElement.classList.add('has-js');
(function(){
  var els=document.querySelectorAll('.rv');
  if(!('IntersectionObserver' in window)){els.forEach(function(e){e.classList.add('in');});return;}
  var io=new IntersectionObserver(function(entries){
    entries.forEach(function(en){
      if(en.isIntersecting){
        var i=Array.prototype.indexOf.call(els,en.target);
        en.target.style.transitionDelay=Math.min((i%3)*90,180)+'ms';
        en.target.classList.add('in'); io.unobserve(en.target);
      }
    });
  },{threshold:.12});
  els.forEach(function(e){io.observe(e);});
})();
</script>

</body>
</html>
