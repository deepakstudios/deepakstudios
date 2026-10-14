<?php
/**
 * photography.php — Photography Hub | Deepak Studios
 *
 * Royal Luxury redesign (dark maroon / deep black / gold). Page-specific
 * styles only — the navbar, footer, WhatsApp/mobile bar and Call Now pill
 * are the existing shared implementations (navbar CSS copied from the other
 * premium pages; floating controls come from lib_contact.php).
 *
 * IMAGE PLACEHOLDERS — every photograph on this page is a "COMING SOON"
 * placeholder. To add a real photograph, replace the placeholder <div>
 * with an <img> that keeps the SAME aspect-ratio class, e.g.:
 *
 *   <div class="ph ph-43"><span>COMING SOON</span></div>
 *      becomes
 *   <img class="ph ph-43 ph-img" src="YOUR-PHOTO.jpg" alt="...">
 *
 * Slots:
 *   HERO visual .....: set --pg-hero-img below (url) or leave empty
 *   STORY 1..6 ......: search STORY-1 .. STORY-6
 *   OCCASION 1..4 ...: search OCC-1 .. OCC-4
 *   ALBUM A..E ......: search ALBUM-A .. ALBUM-E
 */

declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

header('Content-Type: text/html; charset=UTF-8');

$esc = static function (string $v): string {
    return htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
};

/* =====================================================================
 * HERO BACKGROUND - SINGLE CONFIG POINT
 * ------------------------------------------------------------------------
 * Set $pgHeroImage to a local image path (jpg / jpeg / png / webp) when
 * the real hero photograph is ready. Leave it EMPTY ('') to keep the
 * elegant dark-maroon placeholder with the COMING SOON label.
 * ===================================================================== */
$pgHeroImage = '';

// Shared "Call Now" + WhatsApp floating contact UI (identical to the home page).
require_once __DIR__ . '/lib_contact.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Photography | Deepak Studios</title>
<meta name="description" content="Royal luxury wedding photography by Deepak Studios. Signature wedding stories, occasions, craft and albums.">
<style>
  :root{
    --bg:#0b0b0c; --panel:#141416; --card:#18181a; --line:#26262a;
    --ink:#f2ecdf; --mut:#a8a294; --gold:#c9a86a; --gold2:#e6c980;
    --maroon:#4a1216;
    --serif:Georgia,"Times New Roman",serif;
  }
  *{box-sizing:border-box}
  html,body{margin:0;padding:0;background:var(--bg);color:var(--ink);
        font-family:var(--serif);overflow-x:hidden}
  html{scroll-behavior:smooth}
  a{color:var(--gold);text-decoration:none}
  img{max-width:100%}

/* ======== LUXURY NAV (same implementation as reels.php / cinematography.php) ======== */
:root{--rx-shell:1200px; --rx-head:74px;}

/* ---------- premium navbar ---------- */
.rnav{position:fixed;top:0;left:0;right:0;z-index:1200;border-bottom:1px solid transparent;
      transition:background .35s ease,box-shadow .35s ease,border-color .35s ease}
.rnav.solid{background:rgba(9,9,10,.9);backdrop-filter:blur(14px);-webkit-backdrop-filter:blur(14px);
      border-bottom-color:rgba(201,168,106,.22);box-shadow:0 18px 44px -34px rgba(0,0,0,.95)}
.rnav-in{max-width:var(--rx-shell);margin:0 auto;padding:1.15rem 1.25rem;display:flex;
        align-items:center;justify-content:space-between;gap:1.4rem}
.rbrand{text-decoration:none;display:flex;flex-direction:column;line-height:1.15}
.rbrand-name{font-family:var(--serif);font-size:1.3rem;font-weight:400;letter-spacing:.15em;
        text-transform:uppercase;color:var(--ink);transition:color .3s ease;white-space:nowrap}
.rbrand:hover .rbrand-name{color:var(--gold2)}
.rbrand-sub{margin-top:.32rem;font-size:.55rem;letter-spacing:.34em;text-transform:uppercase;
        color:var(--gold);opacity:.88}
.rnav-desk{display:none;align-items:center;gap:2rem}
.rnav-desk a{position:relative;text-decoration:none;font-size:.7rem;letter-spacing:.24em;
        text-transform:uppercase;color:var(--mut);padding:.45rem 0;transition:color .3s ease}
.rnav-desk a::after{content:"";position:absolute;left:0;bottom:0;height:1px;width:0;
        background:var(--gold);transition:width .35s ease}
.rnav-desk a:hover{color:var(--ink)}
.rnav-desk a:hover::after{width:100%}
.rnav-desk a.on{color:var(--gold2)}
.rnav-desk a.on::after{width:100%;height:1px;background:var(--gold);
        box-shadow:0 0 12px rgba(230,201,128,.8)}
.rburger{display:inline-flex;flex-direction:column;justify-content:center;gap:5px;
        width:2.5rem;height:2.5rem;padding:0 .58rem;background:transparent;cursor:pointer;
        border:1px solid rgba(201,168,106,.34);border-radius:999px}
.rburger span{display:block;height:1px;background:var(--gold2);
        transition:transform .3s ease,opacity .3s ease}
.rburger.x span:nth-child(1){transform:translateY(6px) rotate(45deg)}
.rburger.x span:nth-child(2){opacity:0}
.rburger.x span:nth-child(3){transform:translateY(-6px) rotate(-45deg)}
.rmobmenu{display:none;background:rgba(9,9,10,.97);backdrop-filter:blur(14px);
        -webkit-backdrop-filter:blur(14px);border-top:1px solid rgba(201,168,106,.18)}
.rmobmenu.open{display:block}
.rmobmenu a{display:block;padding:1.05rem 1.5rem;text-decoration:none;font-size:.72rem;
        letter-spacing:.24em;text-transform:uppercase;color:var(--mut);
        border-bottom:1px solid rgba(255,255,255,.045)}
.rmobmenu a.on{color:var(--gold2)}
@media (min-width:900px){
  .rnav-desk{display:flex}
  .rburger{display:none}
  .rmobmenu{display:none!important}
}

/* ======== HERO (placeholder until $pgHeroImage is set) ======== */
.pg-hero{position:relative;min-height:100vh;min-height:100svh;display:flex;
        align-items:center;justify-content:center;overflow:hidden;isolation:isolate;background:#08080a}
.pg-hero-bg{position:absolute;inset:0;z-index:-3;
        background:
          radial-gradient(ellipse at 50% 38%,rgba(201,168,106,.14),transparent 62%),
          radial-gradient(ellipse at 50% 110%,rgba(74,18,22,.55),transparent 60%),
          linear-gradient(180deg,#141013 0%,#0b0b0e 55%,#08080a 100%);
        background-size:cover;background-position:center}
.pg-hero-ov{position:absolute;inset:0;z-index:-2;pointer-events:none;background:
        linear-gradient(180deg,rgba(8,8,10,.82) 0%,rgba(8,8,10,.38) 46%,rgba(8,8,10,.94) 100%)}
.pg-hero-in{position:relative;max-width:var(--rx-shell);margin:0 auto;text-align:center;
        padding:calc(var(--rx-head) + 3.2rem) 1.35rem 6.4rem}
.pg-hero-eyebrow{margin:0;font-size:.6rem;letter-spacing:.42em;text-transform:uppercase;
        color:var(--gold);opacity:.92}
.pg-hero-rule{display:block;width:62px;height:1px;margin:1.45rem auto;
        background:linear-gradient(90deg,transparent,var(--gold),transparent);
        box-shadow:0 0 12px rgba(230,201,128,.55)}
.pg-hero-h{margin:0;font-size:clamp(1.85rem,6.4vw,4.4rem);font-weight:400;line-height:1.1;
        letter-spacing:.1em;text-transform:uppercase;color:#f6f1e6;
        text-shadow:0 2px 30px rgba(0,0,0,.72),0 0 62px rgba(201,168,106,.12)}
.pg-hero-sub{margin:1.65rem auto 0;max-width:36rem;font-size:clamp(1rem,2.3vw,1.26rem);
        line-height:1.62;color:#ddd5c6;font-style:italic}
.pg-hero-ph{display:inline-block;margin-top:1.8rem;font-size:.62rem;letter-spacing:.3em;
        text-transform:uppercase;color:var(--gold2);border:1px dashed rgba(201,168,106,.55);
        border-radius:999px;padding:.55rem 1.4rem;background:rgba(10,10,12,.5)}
.pg-hero-cta{display:inline-block;margin-top:2.2rem;padding:.96rem 2.5rem;text-decoration:none;
        font-size:.7rem;letter-spacing:.3em;text-transform:uppercase;color:#0b0b0c;
        background:var(--gold2);border:1px solid var(--gold2);border-radius:999px;
        box-shadow:0 14px 38px -14px rgba(230,201,128,.6);
        transition:transform .3s ease,box-shadow .3s ease,background .3s ease}
.pg-hero-cta:hover{transform:translateY(-2px);background:#f0dcae;
        box-shadow:0 20px 46px -14px rgba(230,201,128,.75)}
.pg-hero-scroll{position:absolute;left:0;right:0;bottom:1.5rem;margin:0;text-align:center;
        font-size:.55rem;letter-spacing:.4em;text-transform:uppercase;color:var(--mut);opacity:.72}
.pg-hero-scroll i{font-style:normal;display:inline-block;margin-left:.45em;color:var(--gold)}

/* ======== shared section chrome ======== */
.pg-sec{padding:5rem 0;position:relative}
.pg-wrap{max-width:1120px;margin:0 auto;padding:0 1.2rem;position:relative;z-index:2}
.pg-head{text-align:center;margin:0 auto 2.6rem;max-width:46rem}
.pg-eyebrow{margin:0;font-size:.58rem;letter-spacing:.42em;text-transform:uppercase;color:var(--gold);opacity:.9}
.pg-h{margin:.8rem 0 0;font-size:clamp(1.5rem,4.5vw,2.6rem);font-weight:400;letter-spacing:.08em;
        text-transform:uppercase;color:var(--ink);line-height:1.25}
.pg-h em{font-style:normal;color:var(--gold2)}
.pg-sub{margin:1rem auto 0;max-width:38rem;color:var(--mut);font-size:.95rem;font-style:italic;line-height:1.7}
.pg-rule{display:block;width:62px;height:1px;margin:1.4rem auto 0;
        background:linear-gradient(90deg,transparent,var(--gold),transparent)}

/* ======== reusable image placeholder ========
   Swap any .ph <div> for <img class="ph <same-ratio> ph-img" src="...">.
   Ratios: .ph-169 (16:9) .ph-43 (4:3) .ph-34 (3:4) .ph-11 (1:1) .ph-219 (21:9) */
.ph{position:relative;display:flex;align-items:center;justify-content:center;overflow:hidden;
        width:100%;border-radius:12px;background:linear-gradient(160deg,#2a1215,#120c0d 72%);
        border:1px solid rgba(201,168,106,.32)}
.ph span{font-size:.62rem;letter-spacing:.3em;text-transform:uppercase;color:var(--gold2);opacity:.9;
        border:1px dashed rgba(201,168,106,.5);border-radius:999px;padding:.45rem 1.1rem;background:rgba(8,8,10,.45)}
.ph-169{aspect-ratio:16/9}
.ph-43{aspect-ratio:4/3}
.ph-34{aspect-ratio:3/4}
.ph-11{aspect-ratio:1/1}
.ph-219{aspect-ratio:21/9}
img.ph-img{object-fit:cover;padding:0}
img.ph-img span{display:none}
/* category label pinned over a story/occasion slot */
.ph-tag{position:absolute;left:.9rem;bottom:.8rem;z-index:2;font-size:.6rem;letter-spacing:.2em;
        text-transform:uppercase;color:#f0d896;background:rgba(9,9,10,.6);
        border:1px solid rgba(201,168,106,.45);border-radius:999px;padding:.32rem .75rem}

/* ======== signature stories: asymmetric editorial grid ======== */
.stories-grid{display:grid;grid-template-columns:1fr;gap:1rem}
.st{display:block;position:relative}
a.st{transition:transform .35s ease}
a.st:hover{transform:translateY(-3px)}
@media(min-width:640px){
  .stories-grid{grid-template-columns:repeat(2,1fr)}
  .st-wide{grid-column:span 2}
}
@media(min-width:1024px){
  .stories-grid{grid-template-columns:repeat(6,1fr);gap:1.15rem}
  .st-a{grid-column:span 4}
  .st-b{grid-column:span 2}
  .st-c,.st-d,.st-e{grid-column:span 2}
  .st-f{grid-column:span 6}
}

/* ======== occasions ======== */
.occ-grid{display:grid;grid-template-columns:1fr;gap:1.15rem}
@media(min-width:640px){.occ-grid{grid-template-columns:repeat(2,1fr)}}
@media(min-width:1024px){.occ-grid{grid-template-columns:repeat(4,1fr)}}
.occ{display:block;background:var(--card);border:1px solid var(--line);border-radius:14px;
        overflow:hidden;transition:border-color .3s ease,transform .3s ease,box-shadow .3s ease}
a.occ:hover{border-color:rgba(201,168,106,.6);transform:translateY(-3px);
        box-shadow:0 18px 44px -20px rgba(0,0,0,.9),0 0 22px rgba(201,168,106,.12)}
.occ .ph{border:0;border-radius:0;border-bottom:1px solid var(--line)}
.occ h3{margin:0;padding:1.1rem 1.2rem 1.25rem;font-size:1.02rem;font-weight:400;
        letter-spacing:.12em;text-transform:uppercase;color:var(--ink)}
a.occ:hover h3{color:var(--gold2)}

/* ======== craft ======== */
.craft{background:#0d0d0f;border-top:1px solid var(--line);border-bottom:1px solid var(--line)}
.craft-grid{display:grid;grid-template-columns:1fr;gap:1rem;margin-top:2.6rem}
@media(min-width:640px){.craft-grid{grid-template-columns:repeat(2,1fr)}}
@media(min-width:1024px){.craft-grid{grid-template-columns:repeat(4,1fr)}}
.craft-card{background:var(--card);border:1px solid var(--line);border-radius:14px;
        padding:1.7rem 1.4rem;transition:border-color .3s ease,transform .3s ease}
.craft-card:hover{border-color:rgba(201,168,106,.5);transform:translateY(-3px)}
.craft-ico{display:inline-flex;padding:.7rem;border-radius:999px;color:var(--gold);
        border:1px solid rgba(201,168,106,.45);background:rgba(201,168,106,.07);margin-bottom:1.1rem}
.craft-ico svg{width:1.4rem;height:1.4rem}
.craft-card h3{margin:0 0 .55rem;font-size:1rem;font-weight:400;letter-spacing:.1em;
        text-transform:uppercase;color:var(--ink)}
.craft-card p{margin:0;font-size:.86rem;line-height:1.65;color:var(--mut)}

/* ======== albums ======== */
.alb-grid{display:grid;grid-template-columns:1fr;gap:1.15rem}
@media(min-width:640px){.alb-grid{grid-template-columns:repeat(2,1fr)}}
@media(min-width:1024px){.alb-grid{grid-template-columns:repeat(5,1fr);gap:1rem}}
.alb{background:var(--card);border:1px solid var(--line);border-radius:14px;overflow:hidden}
.alb .ph{border:0;border-radius:0;border-bottom:1px solid var(--line)}
.alb-in{padding:1.1rem 1.15rem 1.3rem}
.alb-in h3{margin:0 0 .45rem;font-size:.95rem;font-weight:400;letter-spacing:.14em;
        text-transform:uppercase;color:var(--gold2)}
.alb-in p{margin:0;font-size:.82rem;line-height:1.6;color:var(--mut);font-style:italic}

/* ======== booking ======== */
.book{background:radial-gradient(60% 60% at 50% 28%,rgba(74,18,22,.5),transparent 70%),#08080a;
        border-top:1px solid var(--line);text-align:center}
.book h2{margin:.8rem 0 0;font-size:clamp(1.4rem,4.2vw,2.4rem);font-weight:400;line-height:1.35;
        letter-spacing:.06em;text-transform:uppercase;color:#f6f1e6}
.book h2 em{font-style:normal;color:var(--gold2)}
.book p{margin:1rem auto 0;max-width:36rem;color:var(--mut);font-style:italic;line-height:1.7}
.book-btns{display:flex;flex-wrap:wrap;gap:.9rem;justify-content:center;margin-top:2.2rem}
.book-btn{display:inline-flex;align-items:center;gap:.6rem;padding:.95rem 2.2rem;border-radius:999px;
        font-size:.72rem;letter-spacing:.24em;text-transform:uppercase;font-weight:600;
        transition:transform .3s ease,box-shadow .3s ease,background .3s ease,border-color .3s ease}
.book-gold{color:#0b0b0c;background:var(--gold2);border:1px solid var(--gold2);
        box-shadow:0 14px 38px -14px rgba(230,201,128,.6)}
.book-gold:hover{transform:translateY(-2px);background:#f0dcae}
.book-ghost{color:#fff;background:rgba(24,24,27,.35);border:1px solid rgba(201,168,106,.55)}
.book-ghost:hover{transform:translateY(-2px);border-color:var(--gold2)}

/* ---------- footer ---------- */
footer{border-top:1px solid var(--line);padding:2rem 1rem;text-align:center;color:#6a6a6a;
  font-size:.85rem;background:#060606}
footer .fname{font-family:"Playfair Display",Georgia,serif;color:var(--gold);font-weight:700}
.social{display:flex;gap:.7rem;justify-content:center;margin:1rem 0 0}
.social a{width:2.4rem;height:2.4rem;border-radius:999px;display:inline-flex;align-items:center;justify-content:center;
  border:1px solid rgba(201,168,106,.35);color:#8a8a8a;transition:all .3s}
.social a:hover{color:#0d0d0d;background:var(--gold);border-color:var(--gold);transform:translateY(-2px)}
.social svg{width:1.1rem;height:1.1rem;fill:currentColor}

/* ---------- mobile polish ---------- */
@media (max-width:640px){
  .pg-hero-in{padding-top:calc(var(--rx-head) + 2.1rem);padding-bottom:5.6rem}
  .pg-hero-h{letter-spacing:.06em}
  .pg-hero-eyebrow{font-size:.53rem;letter-spacing:.3em}
  .pg-hero-sub{font-size:.97rem}
  .pg-hero-cta{width:100%;max-width:19rem;padding:.92rem 1.4rem}
  .pg-hero-rule{margin:1.15rem auto}
  .pg-hero-scroll{letter-spacing:.3em}
  .pg-sec{padding:3.4rem 0}
  .book-btns .book-btn{width:100%;max-width:19rem;justify-content:center}
}
@media (max-width:380px){
  .rbrand-name{font-size:1.08rem;letter-spacing:.1em}
  .rbrand-sub{font-size:.48rem;letter-spacing:.24em}
}
</style>
<?php ds_contact_head(); ?>
</head>
<body>

<!-- ======== PREMIUM NAVBAR ======== -->
<header class="rnav" id="rnav">
  <div class="rnav-in">
    <a href="index.html" class="rbrand">
      <span class="rbrand-name">Deepak Studios</span>
      <span class="rbrand-sub">Wedding Photography</span>
    </a>
    <nav class="rnav-desk" aria-label="Primary">
      <a href="index.html">Home</a>
      <a href="photography.php" class="on" aria-current="page">Photography</a>
      <a href="cinematography.php">Cinematography</a>
      <a href="reels.php">Reels</a>
      <a href="about.php">About Us</a>
      <?php ds_contact_header_call(); ?>
    </nav>
    <button type="button" class="rburger" id="rburger" aria-label="Open menu"
            aria-expanded="false" aria-controls="rmobmenu">
      <span></span><span></span><span></span>
    </button>
  </div>
  <div class="rmobmenu" id="rmobmenu">
    <a href="index.html">Home</a>
    <a href="photography.php" class="on" aria-current="page">Photography</a>
    <a href="cinematography.php">Cinematography</a>
    <a href="reels.php">Reels</a>
    <a href="about.php">About Us</a>
  </div>
</header>

<!-- ======== HERO (bg photo set via $pgHeroImage; empty = maroon placeholder) ======== -->
<section class="pg-hero" aria-label="Wedding photography">
  <?php if ($pgHeroImage !== ''): ?>
    <img style="position:absolute;inset:0;z-index:-3;width:100%;height:100%;object-fit:cover" src="<?= $esc($pgHeroImage) ?>"
         alt="Deepak Studios wedding photography" fetchpriority="high" decoding="async">
  <?php endif; ?>
  <div class="pg-hero-bg" aria-hidden="true"></div>
  <div class="pg-hero-ov" aria-hidden="true"></div>
  <div class="pg-hero-in">
    <p class="pg-hero-eyebrow">Deepak Studios</p>
    <span class="pg-hero-rule" aria-hidden="true"></span>
    <h1 class="pg-hero-h">The Art of Capturing Moments</h1>
    <p class="pg-hero-sub">Every celebration has a story. We turn your moments into timeless memories.</p>
    <div><span class="pg-hero-ph">Hero Image &mdash; Coming Soon</span></div>
    <div><a class="pg-hero-cta" id="pgHeroCta" href="#portfolio">Explore Photography</a></div>
  </div>
  <p class="pg-hero-scroll" aria-hidden="true">Scroll <i>&darr;</i></p>
</section>

<!-- ======== PORTFOLIO INTRO ======== -->
<section class="pg-sec" id="portfolio" aria-label="Portfolio introduction">
  <div class="pg-wrap">
    <div class="pg-head">
      <p class="pg-eyebrow">Our Portfolio</p>
      <h2 class="pg-h">Every Frame Tells <em>a Love Story</em></h2>
      <span class="pg-rule" aria-hidden="true"></span>
      <p class="pg-sub">From heartfelt candid moments to timeless wedding portraits, every photograph preserves a piece of your story.</p>
    </div>
  </div>
</section>

<!-- ======== SIGNATURE WEDDING STORIES (placeholders STORY-1..6) ======== -->
<section class="pg-sec" style="padding-top:0" aria-label="Signature wedding stories">
  <div class="pg-wrap">
    <div class="stories-grid">
      <!-- STORY-1: replace div with <img class="ph ph-169 ph-img" src="..." alt="Bride and Groom"> -->
      <div class="st st-a"><div class="ph ph-169"><span>Coming Soon</span></div><span class="ph-tag">Bride &amp; Groom</span></div>
      <!-- STORY-2: replace div with <img class="ph ph-34 ph-img" src="..." alt="Candid Moments"> -->
      <div class="st st-b"><div class="ph ph-34"><span>Coming Soon</span></div><span class="ph-tag">Candid Moments</span></div>
      <!-- STORY-3: replace div with <img class="ph ph-11 ph-img" src="..." alt="Wedding Rituals"> -->
      <div class="st st-c"><div class="ph ph-11"><span>Coming Soon</span></div><span class="ph-tag">Wedding Rituals</span></div>
      <!-- STORY-4: replace div with <img class="ph ph-11 ph-img" src="..." alt="Wedding Portraits"> -->
      <div class="st st-d"><div class="ph ph-11"><span>Coming Soon</span></div><span class="ph-tag">Wedding Portraits</span></div>
      <!-- STORY-5: replace div with <img class="ph ph-11 ph-img" src="..." alt="Family Emotions"> -->
      <div class="st st-e"><div class="ph ph-11"><span>Coming Soon</span></div><span class="ph-tag">Family Emotions</span></div>
      <!-- STORY-6: replace div with <img class="ph ph-219 ph-img" src="..." alt="Wedding Details"> -->
      <div class="st st-f"><div class="ph ph-219"><span>Coming Soon</span></div><span class="ph-tag">Wedding Details</span></div>
    </div>
  </div>
</section>

<!-- ======== EXPLORE BY OCCASION (cards OCC-1..4) ======== -->
<section class="pg-sec" style="background:#0d0d0f;border-top:1px solid var(--line);border-bottom:1px solid var(--line)" aria-label="Explore by occasion">
  <div class="pg-wrap">
    <div class="pg-head">
      <p class="pg-eyebrow">Occasions</p>
      <h2 class="pg-h">Explore By <em>Occasion</em></h2>
      <span class="pg-rule" aria-hidden="true"></span>
    </div>
    <div class="occ-grid">
      <!-- OCC-1: Wedding -> wedding.php -->
      <a class="occ" href="wedding.php">
        <!-- OCC-1 image: replace div with <img class="ph ph-169 ph-img" src="..." alt="Wedding"> -->
        <div class="ph ph-169"><span>Coming Soon</span></div>
        <h3>Wedding</h3>
      </a>
      <!-- OCC-2: Pre-Wedding -> prewedding.php -->
      <a class="occ" href="prewedding.php">
        <!-- OCC-2 image: replace div with <img class="ph ph-169 ph-img" src="..." alt="Pre-Wedding"> -->
        <div class="ph ph-169"><span>Coming Soon</span></div>
        <h3>Pre-Wedding</h3>
      </a>
      <!-- OCC-3: Haldi & Mehendi -> NO dedicated page yet (non-navigating until destination is configured) -->
      <div class="occ">
        <!-- OCC-3 image: replace div with <img class="ph ph-169 ph-img" src="..." alt="Haldi and Mehendi"> -->
        <div class="ph ph-169"><span>Coming Soon</span></div>
        <h3>Haldi &amp; Mehendi</h3>
      </div>
      <!-- OCC-4: Birthday & Celebrations -> NO dedicated page yet (non-navigating until destination is configured) -->
      <div class="occ">
        <!-- OCC-4 image: replace div with <img class="ph ph-169 ph-img" src="..." alt="Birthday and Celebrations"> -->
        <div class="ph ph-169"><span>Coming Soon</span></div>
        <h3>Birthday &amp; Celebrations</h3>
      </div>
    </div>
  </div>
</section>

<!-- ======== OUR CRAFT & QUALITY ======== -->
<section class="pg-sec" aria-label="Our craft and quality">
  <div class="pg-wrap">
    <div class="pg-head">
      <p class="pg-eyebrow">Craft</p>
      <h2 class="pg-h">The Art Behind <em>Every Frame</em></h2>
      <span class="pg-rule" aria-hidden="true"></span>
      <p class="pg-sub">Beautiful photography is more than a picture. It is the art of observing emotions, mastering light and preserving the moments that matter most.</p>
    </div>
    <div class="craft-grid">
      <div class="craft-card">
        <span class="craft-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 8V5a2 2 0 0 1 2-2h3M16 3h3a2 2 0 0 1 2 2v3M21 16v3a2 2 0 0 1-2 2h-3M8 21H5a2 2 0 0 1-2-2v-3"/></svg></span>
        <h3>Creative Composition</h3>
        <p>Every frame is composed with intention — balanced, honest and built to last.</p>
      </div>
      <div class="craft-card">
        <span class="craft-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/></svg></span>
        <h3>Professional Lighting</h3>
        <p>Natural and crafted light, shaped patiently to flatter every face and venue.</p>
      </div>
      <div class="craft-card">
        <span class="craft-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M2 9.5a5.5 5.5 0 0 1 9.591-3.676.56.56 0 0 0 .818 0A5.49 5.49 0 0 1 22 9.5c0 2.29-1.5 4-3 5.5l-5.492 5.313a2 2 0 0 1-3 .019L5 15c-1.5-1.5-3-3.2-3-5.5"/></svg></span>
        <h3>Candid Storytelling</h3>
        <p>Unposed emotions and fleeting glances, captured quietly as they happen.</p>
      </div>
      <div class="craft-card">
        <span class="craft-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v3M12 18v3M3 12h3M18 12h3M5.6 5.6l2.1 2.1M16.3 16.3l2.1 2.1M5.6 18.4l2.1-2.1M16.3 7.7l2.1-2.1"/></svg></span>
        <h3>Premium Photo Finishing</h3>
        <p>Careful retouching and colour grading that keeps skin real and moments vivid.</p>
      </div>
    </div>
  </div>
</section>

<!-- ======== WEDDING ALBUMS (covers ALBUM-A..E) ======== -->
<section class="pg-sec" style="background:#0d0d0f;border-top:1px solid var(--line);border-bottom:1px solid var(--line)" aria-label="Wedding albums">
  <div class="pg-wrap">
    <div class="pg-head">
      <p class="pg-eyebrow">Albums</p>
      <h2 class="pg-h">Wedding <em>Albums</em></h2>
      <span class="pg-rule" aria-hidden="true"></span>
      <p class="pg-sub">Heirloom albums designed to be opened, shared and treasured for generations.</p>
    </div>
    <div class="alb-grid">
      <!-- ALBUM-A: cover -> <img class="ph ph-34 ph-img" src="..."> | destination -> a.php (when ready) -->
      <div class="alb"><div class="ph ph-34"><span>Coming Soon</span></div><div class="alb-in"><h3>Wedding Album A</h3><p>Album description — edit this text when the album is ready.</p></div></div>
      <!-- ALBUM-B: cover -> <img class="ph ph-34 ph-img" src="..."> | destination -> b.php (when ready) -->
      <div class="alb"><div class="ph ph-34"><span>Coming Soon</span></div><div class="alb-in"><h3>Wedding Album B</h3><p>Album description — edit this text when the album is ready.</p></div></div>
      <!-- ALBUM-C: cover -> <img class="ph ph-34 ph-img" src="..."> | destination -> c.php (when ready) -->
      <div class="alb"><div class="ph ph-34"><span>Coming Soon</span></div><div class="alb-in"><h3>Wedding Album C</h3><p>Album description — edit this text when the album is ready.</p></div></div>
      <!-- ALBUM-D: cover -> <img class="ph ph-34 ph-img" src="..."> | destination -> d.php (when ready) -->
      <div class="alb"><div class="ph ph-34"><span>Coming Soon</span></div><div class="alb-in"><h3>Wedding Album D</h3><p>Album description — edit this text when the album is ready.</p></div></div>
      <!-- ALBUM-E: cover -> <img class="ph ph-34 ph-img" src="..."> | destination -> e.php (when ready) -->
      <div class="alb"><div class="ph ph-34"><span>Coming Soon</span></div><div class="alb-in"><h3>Wedding Album E</h3><p>Album description — edit this text when the album is ready.</p></div></div>
    </div>
  </div>
</section>

<!-- ======== FINAL BOOKING ======== -->
<section class="pg-sec book" aria-label="Book your wedding photography">
  <div class="pg-wrap">
    <p class="pg-eyebrow">Reserve Your Date</p>
    <h2>Your Wedding Happens Once.<br><em>Your Photographs Last Forever.</em></h2>
    <p>Let Deepak Studios capture your celebrations with creativity, care and timeless storytelling.</p>
    <div class="book-btns">
      <a class="book-btn book-gold" target="_blank" rel="noopener noreferrer" href="https://wa.me/919031700464?text=Hi%20Deepak%20Studios,%20I%20would%20like%20to%20inquire%20about%20a%20photoshoot...">Enquire on WhatsApp</a>
      <a class="book-btn book-ghost" href="tel:+919031700464">Call Now</a>
    </div>
  </div>
</section>

<footer><span class="fname">Deepak Studios</span> &nbsp;·&nbsp; &copy; <?= date('Y') ?> All rights reserved.
<div class="social">
<a href="https://www.instagram.com/deepakstudiosofficial_/" target="_blank" rel="noopener noreferrer" aria-label="Deepak Studios on Instagram"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg></a>
<a href="https://www.facebook.com/deepakstudiosfotography" target="_blank" rel="noopener noreferrer" aria-label="Deepak Studios on Facebook"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg></a>
<a href="https://www.youtube.com/@deepakstudiosfotography" target="_blank" rel="noopener noreferrer" aria-label="Deepak Studios on YouTube"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg></a>
</div></footer>

<?php ds_contact_fab(); ?>

<!-- ======== NAV + HERO CTA ======== -->
<script>
(function () {
  'use strict';
  var nav    = document.getElementById('rnav');
  var burger = document.getElementById('rburger');
  var menu   = document.getElementById('rmobmenu');
  var cta    = document.getElementById('pgHeroCta');

  if (nav) {
    var onScroll = function () { nav.classList.toggle('solid', (window.pageYOffset || 0) > 24); };
    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });
  }

  if (burger && menu) {
    var setMenu = function (isOpen) {
      menu.classList.toggle('open', isOpen);
      burger.classList.toggle('x', isOpen);
      burger.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    };
    burger.addEventListener('click', function (e) {
      e.preventDefault();
      e.stopPropagation();
      setMenu(!menu.classList.contains('open'));
    });
    menu.addEventListener('click', function (e) {
      var a = e.target.closest ? e.target.closest('a') : null;
      if (a) setMenu(false);
    });
  }

  if (cta) {
    cta.addEventListener('click', function (e) {
      var target = document.getElementById('portfolio');
      if (!target) return;
      e.preventDefault();
      e.stopPropagation();
      var top = target.getBoundingClientRect().top + (window.pageYOffset || 0) - 70;
      if ('scrollBehavior' in document.documentElement.style) {
        window.scrollTo({ top: top, behavior: 'smooth' });
      } else {
        window.scrollTo(0, top);
      }
    });
  }
})();
</script>
</body>
</html>
