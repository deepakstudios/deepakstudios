<?php
/**
 * cinematography.php - Cinematography Hub | Deepak Studios
 *
 * Cinematic films hub in the same luxury dark + gold studio language as reels.php:
 *   fixed premium navbar, full-bleed cinematic hero, single-level category filter
 *   (ALL | PRE-WEDDING | WEDDING | ENGAGEMENT | BIRTHDAY | ANNIVERSARY) and a
 *   16:9 film-card grid that plays inside a same-page premium modal.
 *
 * Every film is stored once in the $FILMS array below, so adding a new film means
 * adding one row - the thumbnail, the card and the play link all follow automatically.
 *
 * Cat values: prewedding | wedding | engagement | birthday | anniversary
 * Categories with no films yet render a premium empty state instead of breaking,
 * and any film added later shows up under its own category and under ALL.
 */

declare(strict_types=1);
header('Content-Type: text/html; charset=UTF-8');

$esc = static function (string $v): string {
    return htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
};

/* =====================================================================
 * HERO IMAGE - SINGLE CONFIG POINT
 * ------------------------------------------------------------------------
 * To change the hero picture: edit ONLY the $cineHeroImage path below.
 * No HTML, CSS or JS change is needed anywhere else on this page.
 *   $cineHeroImage : image path (jpg / jpeg / png / webp all work)
 *   $cineHeroPos   : desktop object-position  (keeps the subject in frame)
 *   $cineHeroPosMb : phone object-position    (stops the subject being cropped)
 * ===================================================================== */
$cineHeroImage = 'assets/images/cinematography-hero.jpg';
$cineHeroPos   = 'center center';
$cineHeroPosMb = 'center 30%';

/* =====================================================================
 * FILM DATA - add a new film here and the page updates itself.
 *   id  : YouTube video ID     t : film title     cat : category
 * ===================================================================== */
$FILMS = [
    ['id' => 'Cztd9udNlSo', 't' => 'The Vows',             'cat' => 'wedding',    'bg' => '#141210'],
    ['id' => 'JRoyOgR0Ckk', 't' => 'Baraat Nights',        'cat' => 'wedding',    'bg' => '#1a1410'],
    ['id' => 'IyRO4IKhl8g', 't' => 'Sangeet Sessions',     'cat' => 'wedding',    'bg' => '#131018'],
    ['id' => '7ZubbaOMFxY', 't' => 'The Mandap',          'cat' => 'wedding',    'bg' => '#161210'],
    ['id' => 'vTg374RrzU0', 't' => 'First Look',          'cat' => 'wedding',    'bg' => '#1a1210'],
    ['id' => 'WmZLZMhtlOk', 't' => 'Mehendi Mornings',    'cat' => 'wedding',    'bg' => '#10141a'],
    ['id' => 'iAsgIhoLAQg', 't' => 'Ceremony in Gold',    'cat' => 'wedding',    'bg' => '#181308'],
    ['id' => 'qZ3feBEq_d8', 't' => 'Reception Waltz',     'cat' => 'wedding',    'bg' => '#121016'],
    ['id' => 'uVRB2XNLA1w', 't' => 'Vidaai',              'cat' => 'wedding',    'bg' => '#1c1414'],
    ['id' => '9I9NnX8wAkE', 't' => 'Blessings',           'cat' => 'wedding',    'bg' => '#141418'],
    ['id' => 'lOxZme5S_e4', 't' => 'The Aarti',           'cat' => 'wedding',    'bg' => '#1a120e'],
    ['id' => 'BDBufwZhnK0', 't' => 'Forever Begins',      'cat' => 'wedding',    'bg' => '#0f1412'],
    ['id' => 'oWrlcf2tb_4', 't' => 'The Proposal',        'cat' => 'engagement', 'bg' => '#16121a'],
    ['id' => 'yvW6COhgwV0', 't' => 'The First Glimpse',   'cat' => 'prewedding', 'bg' => '#141210'],
    ['id' => 'Tcm2kFq0-sA', 't' => 'Before We Say Yes',   'cat' => 'prewedding', 'bg' => '#10141a'],
    ['id' => 'YfccOo4h4Kk', 't' => 'Rooftop Confessions', 'cat' => 'prewedding', 'bg' => '#161210'],
    ['id' => 'LhjChNCyl9E', 't' => 'Golden Hour Walk',    'cat' => 'prewedding', 'bg' => '#1a1410'],
    ['id' => 'NEJ4yZ-cs6g', 't' => 'Frames of Forever',   'cat' => 'prewedding', 'bg' => '#131018'],
    ['id' => 'r0EflaC0a_U', 't' => 'Before the Bells',    'cat' => 'prewedding', 'bg' => '#121016'],
    // Add more films below, e.g.:
    // ['id' => 'XXXX', 't' => 'Engagement Sunset', 'cat' => 'engagement', 'bg' => '#141210'],
    // ['id' => 'YYYY', 't' => 'Pre-Wedding Dream', 'cat' => 'prewedding', 'bg' => '#141210'],
];

$MAIN = ['all', 'prewedding', 'wedding', 'engagement', 'birthday', 'anniversary'];
$CATLABEL = [
    'all'         => 'All',
    'prewedding'  => 'Pre-Wedding',
    'wedding'     => 'Wedding',
    'engagement'  => 'Engagement',
    'birthday'    => 'Birthday',
    'anniversary' => 'Anniversary',
];
/* Copy shown in the premium empty state for categories with no films yet. */
$EMPTYTEXT = [
    'birthday'    => 'Birthday films are in the edit. Explore the Wedding and Engagement collections in the meantime.',
    'anniversary' => 'Anniversary films are in the edit. Explore the Wedding and Engagement collections in the meantime.',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cinematography | Deepak Studios</title>
<meta name="description" content="Cinematic wedding, pre-wedding, engagement and celebration films by Deepak Studios.">
<style>
  :root{
    --bg:#0b0b0c; --panel:#141416; --card:#18181a; --line:#26262a;
    --ink:#f2ecdf; --mut:#a8a294; --gold:#c9a86a; --gold2:#e6c980;
    --serif:Georgia,"Times New Roman",serif;
  }
  *{box-sizing:border-box}
  html,body{margin:0;padding:0;background:var(--bg);color:var(--ink);font-family:var(--serif);overflow-x:hidden}
  a{color:var(--gold);text-decoration:none}
  main{max-width:1200px;margin:0 auto;padding:3.1rem 1.15rem 3.6rem}
  .lead{text-align:center;color:var(--mut);max-width:640px;margin:0 auto 2rem}

/* ======== LUXURY NAV + CINEMATIC HERO (shared design language with reels.php) ======== */
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

/* ---------- cinematic hero ---------- */
.rhero{position:relative;min-height:100vh;min-height:100svh;display:flex;
        align-items:center;justify-content:center;overflow:hidden;isolation:isolate;background:#08080a}
.rhero-img{position:absolute;inset:0;z-index:-3;width:100%;height:100%;
        object-fit:cover;object-position:<?= $esc($cineHeroPos) ?>;user-select:none}
.rhero-ov{position:absolute;inset:0;z-index:-2;pointer-events:none;background:
        linear-gradient(180deg,rgba(8,8,10,.88) 0%,rgba(8,8,10,.3) 44%,rgba(8,8,10,.93) 100%),
        radial-gradient(ellipse at 38% 46%,rgba(6,6,8,.05) 0%,rgba(6,6,8,.8) 78%)}
.rhero-grain{position:absolute;inset:0;z-index:-1;pointer-events:none;opacity:.035;
        background-image:radial-gradient(rgba(255,255,255,.9) .5px,transparent .5px);
        background-size:3px 3px}
.rhero-in{position:relative;max-width:var(--rx-shell);margin:0 auto;text-align:center;
        padding:calc(var(--rx-head) + 3.2rem) 1.35rem 6.4rem}
.rhero-eyebrow{margin:0;font-size:.6rem;letter-spacing:.42em;text-transform:uppercase;
        color:var(--gold);opacity:.92}
.rhero-eyebrow i{font-style:normal;opacity:.5;padding:0 .3em}
.rhero-rule{display:block;width:62px;height:1px;margin:1.45rem auto;
        background:linear-gradient(90deg,transparent,var(--gold),transparent);
        box-shadow:0 0 12px rgba(230,201,128,.55)}
.rhero-h{margin:0;font-size:clamp(1.85rem,6.4vw,4.6rem);font-weight:400;line-height:1.08;
        letter-spacing:.1em;text-transform:uppercase;color:#f6f1e6;
        text-shadow:0 2px 30px rgba(0,0,0,.72),0 0 62px rgba(201,168,106,.12)}
.rhero-sub{margin:1.65rem auto 0;max-width:34rem;font-size:clamp(1rem,2.3vw,1.26rem);
        line-height:1.62;color:#ddd5c6;font-style:italic}
.rhero-sup{margin:.9rem auto 0;max-width:31rem;font-size:.85rem;letter-spacing:.05em;
        color:var(--mut)}
.rhero-cta{display:inline-block;margin-top:2.4rem;padding:.96rem 2.5rem;text-decoration:none;
        font-size:.7rem;letter-spacing:.3em;text-transform:uppercase;color:#0b0b0c;
        background:var(--gold2);border:1px solid var(--gold2);border-radius:999px;
        box-shadow:0 14px 38px -14px rgba(230,201,128,.6);
        transition:transform .3s ease,box-shadow .3s ease,background .3s ease}
.rhero-cta:hover{transform:translateY(-2px);background:#f0dcae;
        box-shadow:0 20px 46px -14px rgba(230,201,128,.75)}
.rhero-scroll{position:absolute;left:0;right:0;bottom:1.5rem;margin:0;text-align:center;
        font-size:.55rem;letter-spacing:.4em;text-transform:uppercase;color:var(--mut);opacity:.72}
.rhero-scroll i{font-style:normal;display:inline-block;margin-left:.45em;color:var(--gold)}

/* ---------- section head ---------- */
.rsec{text-align:center;margin:0 auto 1.9rem}
.rsec-eyebrow{margin:0;font-size:.58rem;letter-spacing:.42em;text-transform:uppercase;color:var(--gold);opacity:.9}
.rsec-h{margin:.7rem 0 0;font-size:clamp(1.15rem,3.4vw,1.7rem);font-weight:400;letter-spacing:.16em;
        text-transform:uppercase;color:var(--ink)}
.rsec-sup{margin:.75rem 0 0;color:var(--mut);font-size:.9rem;font-style:italic}

/* ---------- filter bar ---------- */
.filters{display:flex;gap:.6rem .95rem;justify-content:center;flex-wrap:wrap;margin:0 0 2rem}
.filters button{cursor:pointer;color:var(--mut);font-family:var(--serif);font-size:.78rem;
        letter-spacing:.16em;text-transform:uppercase;background:transparent;border:0;
        border-bottom:2px solid transparent;padding:.5rem .15rem;
        transition:color .25s ease,border-color .25s ease}
.filters button:hover{color:var(--ink)}
.filters button.on{color:var(--gold2);border-bottom-color:var(--gold)}

/* ---------- 16:9 film cards ---------- */
.fgrid{display:grid;grid-template-columns:1fr;gap:1.15rem}
@media (min-width:560px){.fgrid{grid-template-columns:repeat(2,1fr)}}
@media (min-width:900px){.fgrid{grid-template-columns:repeat(3,1fr)}}
.fc{position:relative;display:block;width:100%;padding:0;margin:0;text-align:left;cursor:pointer;
      font-family:var(--serif);border-radius:12px;overflow:hidden;border:1px solid var(--line);
      background:var(--card);aspect-ratio:16/9;box-shadow:0 10px 28px -12px rgba(0,0,0,.7)}
.fc .th{display:block;width:100%;height:100%;object-fit:cover;
      transition:transform .5s cubic-bezier(.22,.61,.36,1)}
.fc .shade{position:absolute;inset:0;background:linear-gradient(180deg,rgba(0,0,0,.14) 0%,
      rgba(0,0,0,0) 34%,rgba(0,0,0,.82) 100%)}
.fc .pbtn{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:3.5rem;height:3.5rem;
      border-radius:50%;display:grid;place-items:center;background:rgba(201,168,106,.94);color:#0b0b0c;
      box-shadow:0 6px 18px rgba(0,0,0,.45);transition:transform .25s ease}
.fc .pbtn svg{margin-left:3px}
.fc .cat{position:absolute;top:.7rem;left:.7rem;font-size:.6rem;letter-spacing:.16em;
      text-transform:uppercase;color:#e9d9b5;background:rgba(10,10,10,.55);
      border:1px solid rgba(201,168,106,.5);padding:.26rem .55rem;border-radius:999px}
.fc .ttl{position:absolute;left:.85rem;right:.85rem;bottom:.72rem;color:var(--ink);
      font-size:.95rem;line-height:1.25}
.fc .ttl small{display:block;color:var(--gold);font-size:.66rem;letter-spacing:.12em;
      text-transform:uppercase;margin-bottom:.22rem}
.fc:hover .th{transform:scale(1.045)}
.fc:hover{border-color:#3a3a3e}
.fc:hover .pbtn{transform:translate(-50%,-50%) scale(1.06)}

/* ---------- premium empty state ---------- */
.empty{text-align:center;color:var(--mut);padding:3.4rem 1.4rem;border:1px dashed var(--line);
      border-radius:14px;background:linear-gradient(180deg,rgba(201,168,106,.035),transparent)}
.empty .emark{display:block;font-size:.58rem;letter-spacing:.42em;text-transform:uppercase;
      color:var(--gold);opacity:.85;margin-bottom:.95rem}
.empty strong{display:block;color:var(--gold2);font-weight:400;font-size:1.08rem;letter-spacing:.14em;
      text-transform:uppercase;margin-bottom:.8rem}
.empty p{margin:0 auto;max-width:32rem;font-size:.9rem;line-height:1.75}

footer{text-align:center;padding:1.6rem 1rem;color:#6e6a60;font-size:.85rem}

/* ---------- 16:9 premium in-page modal ---------- */
.cmodal{position:fixed;z-index:1600;inset:0;display:none;align-items:center;justify-content:center;padding:1.1rem;
      background:rgba(5,6,8,.96);backdrop-filter:blur(14px);-webkit-backdrop-filter:blur(14px)}
.cmodal.open{display:flex}
.cshell{width:min(96vw,1080px);max-height:92vh;display:flex;flex-direction:column;border-radius:18px;
      overflow:hidden;background:var(--card);border:1px solid rgba(201,168,106,.42);
      box-shadow:0 64px 150px -24px rgba(0,0,0,.98)}
.chead{display:flex;align-items:center;justify-content:space-between;gap:.8rem;padding:.85rem 1.1rem;
      border-bottom:1px solid var(--line)}
.chead h3{margin:0;font-size:1rem;font-weight:400;color:var(--ink);line-height:1.35}
.chead h3 small{display:block;color:var(--gold);font-size:.6rem;letter-spacing:.18em;
      text-transform:uppercase;margin-bottom:.26rem}
.cclose{all:unset;cursor:pointer;display:grid;place-items:center;width:2.3rem;height:2.3rem;border-radius:50%;
      color:var(--mut);font-size:1.02rem;line-height:1;border:1px solid var(--line);transition:.2s ease}
.cclose:hover{color:var(--ink);border-color:var(--gold);background:rgba(201,168,106,.1)}
.cwrap{position:relative;width:100%;aspect-ratio:16/9;background:#000}
.cframe{position:absolute;inset:0;width:100%;height:100%;border:0}
.cfoot{display:flex;align-items:center;justify-content:space-between;gap:.6rem;padding:.7rem 1.1rem;
      border-top:1px solid var(--line)}
.cnav{display:flex;gap:.5rem}
.cnav button{cursor:pointer;font-family:var(--serif);font-size:.74rem;letter-spacing:.14em;
      text-transform:uppercase;color:var(--mut);background:transparent;border:1px solid var(--line);
      padding:.5rem .95rem;border-radius:999px;transition:.2s ease}
.cnav button:hover{color:var(--gold);border-color:var(--gold)}
.cpos{color:var(--mut);font-size:.72rem;letter-spacing:.12em}
body.vlock,html.vlock{overflow:hidden}

/* ---------- mobile polish ---------- */
@media (max-width:640px){
  .rhero-img{object-position:<?= $esc($cineHeroPosMb) ?>}
  .rhero-in{padding-top:calc(var(--rx-head) + 2.1rem);padding-bottom:5.6rem}
  .rhero-h{letter-spacing:.06em}
  .rhero-eyebrow{font-size:.53rem;letter-spacing:.3em}
  .rhero-sub{font-size:.97rem}
  .rhero-cta{width:100%;max-width:19rem;padding:.92rem 1.4rem}
  .rhero-rule{margin:1.15rem auto}
  .rhero-scroll{letter-spacing:.3em}
  .cshell{width:100%;max-height:94vh}
  .chead{padding:.7rem .85rem}
  .chead h3{font-size:.9rem}
  .cfoot{padding:.6rem .85rem}
  .cnav button{font-size:.68rem;padding:.45rem .7rem}
}
@media (max-width:380px){
  .rbrand-name{font-size:1.08rem;letter-spacing:.1em}
  .rbrand-sub{font-size:.48rem;letter-spacing:.24em}
  .filters button{font-size:.7rem;letter-spacing:.12em}
}
/* ======== end luxury nav + hero ======== */
</style>
</head>
<body>

<!-- ======== PREMIUM NAVBAR ======== -->
<header class="rnav" id="rnav">
  <div class="rnav-in">
    <a href="index.html" class="rbrand">
      <span class="rbrand-name">Deepak Studios</span>
      <span class="rbrand-sub">Cinematic Photography</span>
    </a>
    <nav class="rnav-desk" aria-label="Primary">
      <a href="index.html">Home</a>
      <a href="index.html#portfolio">Photography</a>
      <a href="cinematography.php" class="on" aria-current="page">Cinematography</a>
      <a href="reels.php">Reels</a>
      <a href="index.html#contact">Contact Us</a>
    </nav>
    <button type="button" class="rburger" id="rburger" aria-label="Open menu"
            aria-expanded="false" aria-controls="rmobmenu">
      <span></span><span></span><span></span>
    </button>
  </div>
  <div class="rmobmenu" id="rmobmenu">
    <a href="index.html">Home</a>
    <a href="index.html#portfolio">Photography</a>
    <a href="cinematography.php" class="on" aria-current="page">Cinematography</a>
    <a href="reels.php">Reels</a>
    <a href="index.html#contact">Contact Us</a>
  </div>
</header>

<!-- ======== CINEMATIC HERO ======== -->
<section class="rhero" aria-labelledby="rhero-h">
  <img class="rhero-img"
       src="<?= $esc($cineHeroImage) ?>"
       alt="Deepak Studios cinematic wedding film"
       width="1920" height="1080"
       fetchpriority="high" decoding="async">
  <span class="rhero-ov" aria-hidden="true"></span>
  <span class="rhero-grain" aria-hidden="true"></span>
  <div class="rhero-in">
    <p class="rhero-eyebrow">DEEPAK STUDIOS <i>&bull;</i> CINEMATOGRAPHY</p>
    <span class="rhero-rule" aria-hidden="true"></span>
    <h1 class="rhero-h" id="rhero-h">THE ART OF CINEMATIC STORYTELLING</h1>
    <p class="rhero-sub">Every emotion. Every celebration. Every unforgettable frame.</p>
    <p class="rhero-sup">Explore our Wedding, Pre-Wedding, Engagement &amp; Celebration Films.</p>
    <a class="rhero-cta" id="rheroCta" href="#films">EXPLORE FILMS</a>
  </div>
  <p class="rhero-scroll" aria-hidden="true">SCROLL TO EXPLORE <i>&#8595;</i></p>
</section>

<main id="films">
  <div class="rsec">
    <p class="rsec-eyebrow">FILM COLLECTION</p>
    <h2 class="rsec-h">CINEMATIC FILMS</h2>
    <p class="rsec-sup">Stories crafted with emotion, light and movement.</p>
  </div>

  <div class="filters" id="filters" role="tablist" aria-label="Film category">
    <?php foreach ($MAIN as $m): ?>
      <button type="button" data-m="<?= $esc($m) ?>"
              class="<?= $m === 'all' ? 'on' : '' ?>"><?= $esc($CATLABEL[$m]) ?></button>
    <?php endforeach; ?>
  </div>

  <div class="fgrid" id="grid">
    <?php foreach ($FILMS as $r): ?>
      <button type="button" class="fc"
data-v="<?= $esc($r['id']) ?>"
data-t="<?= $esc($r['t']) ?>"
data-cat="<?= $esc($r['cat']) ?>"
aria-label="Play <?= $esc($r['t']) ?>">
        <img class="th" loading="lazy"
             src="https://i.ytimg.com/vi/<?= $esc($r['id']) ?>/hqdefault.jpg"
             alt="<?= $esc($r['t']) ?>" style="background:<?= $esc($r['bg']) ?>">
        <span class="shade"></span>
        <span class="pbtn"><svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"
              aria-hidden="true"><path d="M8 5.14v13.72a1 1 0 0 0 1.54.84l11.06-6.86a1 1 0 0 0 0-1.68L9.54 4.3A1 1 0 0 0 8 5.14z"/></svg></span>
        <span class="cat"><?= $esc($CATLABEL[$r['cat']]) ?></span>
        <span class="ttl"><small>Film</small><?= $esc($r['t']) ?></span>
      </button>
    <?php endforeach; ?>
  </div>
</main>

<footer>&copy; <?= date('Y') ?> Deepak Studios. All rights reserved.</footer>

<!-- ======== FILTER RENDER ======== -->
<script>
(function () {
  'use strict';
  var DATA = <?= json_encode($FILMS, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>;
  var CATLABEL = <?= json_encode($CATLABEL, JSON_UNESCAPED_SLASHES) ?>;
  var EMPTY = <?= json_encode($EMPTYTEXT, JSON_UNESCAPED_SLASHES) ?>;
  var cats = document.querySelector('#filters');
  var grid = document.querySelector('#grid');
  var main = 'all';
  var count = document.createElement('div');
  count.id = 'filmcount';
  count.style.cssText = 'text-align:center;color:var(--mut);font-size:.72rem;letter-spacing:.2em;text-transform:uppercase;margin:-.6rem 0 1.6rem';
  grid.parentNode.insertBefore(count, grid);

  var PLAY = '<svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">' +
    '<path d="M8 5.14v13.72a1 1 0 0 0 1.54.84l11.06-6.86a1 1 0 0 0 0-1.68L9.54 4.3A1 1 0 0 0 8 5.14z"/></svg>';

  function filterFor(m) {
    if (m === 'all') return DATA.slice();
    return DATA.filter(function (r) { return r.cat === m; });
  }

  function esc(s) {
    return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;')
      .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
  }

  function render() {
    var list = filterFor(main);
    count.textContent = list.length + (list.length === 1 ? ' film' : ' films');

    if (list.length === 0) {
      var label = CATLABEL[main] || 'Films';
      var copy = EMPTY[main] ||
        'New films for this collection are in the edit. Explore the rest of our cinematic work in the meantime.';
      grid.innerHTML = '<div class="empty">' +
        '<span class="emark">COMING SOON</span>' +
        '<strong>' + esc(label) + ' Films</strong>' +
        '<p>' + esc(copy) + '</p></div>';
      return;
    }

    grid.innerHTML = list.map(function (r) {
      return '<button type="button" class="fc" data-v="' + r.id + '" data-t="' + esc(r.t) +
        '" data-cat="' + r.cat + '" aria-label="Play ' + esc(r.t) + '">' +
        '<img class="th" loading="lazy" src="https://i.ytimg.com/vi/' + r.id +
        '/hqdefault.jpg" alt="' + esc(r.t) + '" style="background:' + r.bg + '">' +
        '<span class="shade"></span>' +
        '<span class="pbtn">' + PLAY + '</span>' +
        '<span class="cat">' + esc(CATLABEL[r.cat] || r.cat) + '</span>' +
        '<span class="ttl"><small>Film</small>' + esc(r.t) + '</span>' +
        '</button>';
    }).join('');
  }

  cats.addEventListener('click', function (e) {
    var b = e.target.closest('button');
    if (!b) return;
    e.preventDefault();
    e.stopPropagation();
    main = b.getAttribute('data-m');
    document.querySelectorAll('#filters button').forEach(function (x) { x.classList.remove('on'); });
    b.classList.add('on');
    render();
  });

  render();
})();
</script>

<!-- ======== 16:9 PREMIUM IN-PAGE PLAYER ======== -->
<div class="cmodal" id="cmodal" role="dialog" aria-modal="true" aria-labelledby="cttl">
  <div class="cshell">
    <div class="chead">
      <h3 id="cttl"><small id="ccat">Film</small><span id="ctit">Now Playing</span></h3>
      <button type="button" class="cclose" id="cclose" aria-label="Close player">&#10005;</button>
    </div>
    <div class="cwrap">
      <iframe id="cframe" class="cframe" title="YouTube video player"
          allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
          referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
    </div>
    <div class="cfoot">
      <div class="cnav">
        <button type="button" id="cprev">&#8249; Prev</button>
        <button type="button" id="cnext">Next &#8250;</button>
      </div>
      <span class="cpos" id="cpos"></span>
    </div>
  </div>
</div>
<script>
(function () {
  'use strict';
  var modal = document.getElementById('cmodal');
  if (!modal) return;
  var frame = document.getElementById('cframe');
  var ctit = document.getElementById('ctit');
  var ccat = document.getElementById('ccat');
  var cpos = document.getElementById('cpos');
  var cclose = document.getElementById('cclose');
  var cprev = document.getElementById('cprev');
  var cnext = document.getElementById('cnext');

  /* Cards are re-rendered by the filter, so always resolve them from the live DOM
     instead of a one-time snapshot - otherwise the index would go stale and every
     card would open the first film. */
  function liveCards() { return Array.prototype.slice.call(document.querySelectorAll('#grid .fc[data-v]')); }
  var i = 0, open = false, lastScroll = 0;

  function openAt(n, el) {
    var cards = liveCards();
    if (!cards.length) return;
    if (el) {
      var at = cards.indexOf(el);
      if (at < 0) return;
      i = at;
    } else {
      i = (n % cards.length + cards.length) % cards.length;
    }
    var wasOpen = modal.classList.contains('open');
    /* Only remember the page position when the player is actually being opened.
       Prev/Next call this too, and re-reading the offset there would overwrite the
       saved value (the page sits at 0 while the modal is open) and break restore. */
    if (!wasOpen) lastScroll = (window.pageYOffset || document.documentElement.scrollTop) || 0;
    var v = cards[i].getAttribute('data-v');
    frame.src = 'https://www.youtube-nocookie.com/embed/' + v + '?autoplay=1&rel=0';
    ctit.textContent = cards[i].getAttribute('data-t') || 'Film';
    var cat = cards[i].getAttribute('data-cat') || '';
    ccat.textContent = cat ? cat.charAt(0).toUpperCase() + cat.slice(1) : 'Film';
    cpos.textContent = (i + 1) + ' / ' + cards.length;
    modal.classList.add('open');
    document.body.classList.add('vlock');
    document.documentElement.classList.add('vlock');
    if (!wasOpen) { window.scrollTo(0, 0); cclose.focus(); }
  }

  function closeAt() {
    if (!open) return;
    modal.classList.remove('open');
    frame.src = 'about:blank';
    document.body.classList.remove('vlock');
    document.documentElement.classList.remove('vlock');
    window.scrollTo(0, lastScroll);
  }

  function fx(e) { e.preventDefault(); e.stopPropagation(); }

  document.addEventListener('click', function (e) {
    var c = e.target.closest ? e.target.closest('.fc[data-v]') : null;
    if (c) { fx(e); open = true; openAt(0, c); return; }
    if (e.target.closest && e.target.closest('#cclose')) { fx(e); closeAt(); open = false; return; }
    if (e.target.closest && e.target.closest('#cprev')) { fx(e); openAt(i - 1); return; }
    if (e.target.closest && e.target.closest('#cnext')) { fx(e); openAt(i + 1); return; }
    if (e.target === modal) { e.preventDefault(); closeAt(); open = false; }
  });

  document.addEventListener('keydown', function (e) {
    if (!modal.classList.contains('open')) return;
    if (e.key === 'Escape') { e.preventDefault(); closeAt(); open = false; }
    else if (e.key === 'ArrowLeft') { e.preventDefault(); openAt(i - 1); }
    else if (e.key === 'ArrowRight') { e.preventDefault(); openAt(i + 1); }
  });
})();
</script>

<!-- ======== PREMIUM NAV + HERO CTA ======== -->
<script>
(function () {
  'use strict';
  var nav    = document.getElementById('rnav');
  var burger = document.getElementById('rburger');
  var menu   = document.getElementById('rmobmenu');
  var cta    = document.getElementById('rheroCta');

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
      var target = document.getElementById('films');
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
