<?php
/**
 * reels.php — Deepak Studios Reels (vertical Shorts).
 *
 * Premium 9:16 Shorts grid with a dynamic two-level filter:
 *   Main : ALL | WEDDING | PRE-WEDDING | CELEBRATION
 *   All : secondary = Main, WEDDING -> ALL·HALDI·MEHENDI·WEDDING·RECEPTION,
 *         CELEBRATION -> ALL·BIRTHDAY·ANNAPRASHAN
 *
 * Every Short is stored once in the $REELS array below so more Shorts can be
 * added later without touching any layout code. The same array also drives the
 * thumbnail, the 9:16 card and the play link.
 *
 * Category faces used:
 *   prewedding  -> displayed right away (5 Shorts)
 *   wedding     -> card shows when WEDDING (or its sub-filter) is active
 *   celebration -> card shows when CELEBRATION (or its sub-filter) is active
 */

declare(strict_types=1);
header('Content-Type: text/html; charset=UTF-8');

$esc = static function (string $v): string {
    return htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
};

/* =====================================================================
 * REELS DATA — add a new Short here and the page updates itself.
 *   id   : YouTube video ID          cat  : prewedding | wedding | celebration
 *   sub  : optional secondary label  (wedding: haldi|mehendi|wedding|reception
 *          celebration: birthday|annaprashan)
 * ===================================================================== */
$REELS = [
    ['id' => 'jWbsPrWggaM', 't' => 'City Lights Prelude',  'cat' => 'prewedding', 'sub' => '',    'bg' => '#1a1410'],
    ['id' => 'JkVg9HcHKic', 't' => 'Golden Hour Waltz',    'cat' => 'prewedding', 'sub' => '',    'bg' => '#10141a'],
    ['id' => 'oJyx7SCV3Rc', 't' => 'Rooftop Rendezvous',   'cat' => 'prewedding', 'sub' => '',    'bg' => '#141210'],
    ['id' => 'AMqMUmnb0K4', 't' => 'Monsoon Frames',       'cat' => 'prewedding', 'sub' => '',    'bg' => '#0f1412'],
    ['id' => 'kmblV9ddjJo', 't' => 'Falling Leaves',       'cat' => 'prewedding', 'sub' => '',    'bg' => '#1a1210'],
    // Add wedding / celebration Shorts here, e.g.:
    // ['id' => 'XXXX', 't' => 'Haldi Joy', 'cat' => 'wedding', 'sub' => 'haldi'],
    // ['id' => 'YYYY', 't' => 'Birthday Bash', 'cat' => 'celebration', 'sub' => 'birthday'],
];

$MAIN = ['all', 'wedding', 'prewedding', 'celebration'];
$SUB = [
    'wedding'    => ['all', 'haldi', 'mehendi', 'wedding', 'reception'],
    'celebration' => ['all', 'birthday', 'annaprashan'],
];
$SUBLABEL = [
    'wedding' => ['All', 'Haldi', 'Mehendi', 'Wedding', 'Reception'],
    'celebration' => ['All', 'Birthday', 'Annaprashan'],
];
$CATLABEL = ['all' => 'All', 'wedding' => 'Wedding', 'prewedding' => 'Pre-Wedding', 'celebration' => 'Celebration'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reels | Deepak Studios</title>
<style>
  :root{
    --bg:#0b0b0c; --panel:#141416; --card:#18181a; --line:#26262a;
    --ink:#f2ecdf; --mut:#a8a294; --gold:#c9a86a; --gold2:#e6c980;
    --serif:Georgia,"Times New Roman",serif;
  }
  *{box-sizing:border-box}
  html,body{margin:0;padding:0;background:var(--bg);color:var(--ink);
            font-family:var(--serif);}
  a{color:var(--gold);text-decoration:none}
  header{padding:2.1rem 1rem 1.2rem;text-align:center;background:#101012;
         border-bottom:1px solid var(--line)}
  header h1{margin:0;font-size:2.2rem;letter-spacing:2px;font-weight:400}
  header p{margin:.6rem 0 0;color:var(--gold);font-style:italic}
  nav.top{margin-top:1.1rem;font-size:.92rem;display:flex;gap:1.1rem;
          justify-content:center;flex-wrap:wrap}
  nav.top a:hover{color:var(--gold2)}
  main{max-width:1120px;margin:0 auto;padding:2rem 1.1rem 3.4rem}
  .lead{text-align:center;color:var(--mut);max-width:640px;margin:0 auto 1.8rem}

  /* ---------- filter bar ---------- */
  .filters{display:flex;gap:.6rem .9rem;justify-content:center;flex-wrap:wrap;
           margin:0 0 1.9rem}
  .filters button{cursor:pointer;color:var(--mut);font-family:var(--serif);
           font-size:.95rem;letter-spacing:.12em;background:transparent;
           border:0;border-bottom:2px solid transparent;padding:.5rem .15rem;
           transition:color .25s ease,border-color .25s ease}
  .filters button:hover{color:var(--ink)}
  .filters button.on{color:var(--gold2);border-bottom-color:var(--gold)}
  .subfilters{display:flex;gap:.55rem .9rem;justify-content:center;flex-wrap:wrap;
           margin:-1rem 0 2rem;min-height:2.2rem}
  .subfilters button{cursor:pointer;color:var(--mut);font-family:var(--serif);
           font-size:.82rem;letter-spacing:.14em;background:transparent;border:0;
           border-bottom:2px solid transparent;padding:.45rem .15rem;
           transition:color .25s ease,border-color .25s ease}
  .subfilters button.on{color:var(--gold2);border-bottom-color:var(--gold)}
  .subfilters.hidden{visibility:hidden}

  /* ---------- 9:16 vertical cards ---------- */
  .grid{display:grid;grid-template-columns:1fr;gap:1.15rem}
  @media (min-width:520px){.grid{grid-template-columns:repeat(2,1fr)}}
  @media (min-width:820px){.grid{grid-template-columns:repeat(3,1fr)}}
  @media (min-width:1080px){.grid{grid-template-columns:repeat(5,1fr)}}
  .rc{position:relative;display:block;border-radius:10px;overflow:hidden;
      border:1px solid var(--line);background:var(--card);aspect-ratio:9/16;
      box-shadow:0 10px 28px -12px rgba(0,0,0,.7)}
  .rc .th{display:block;width:100%;height:100%;object-fit:cover}
  .rc .shade{position:absolute;inset:0;background:linear-gradient(180deg,
      rgba(0,0,0,.16) 0%,rgba(0,0,0,0) 30%,rgba(0,0,0,.78) 100%)}
  .rc .pbtn{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);
      width:3.6rem;height:3.6rem;border-radius:50%;display:grid;place-items:center;
      background:rgba(201,168,106,.94);color:#0b0b0c;box-shadow:0 6px 18px rgba(0,0,0,.45)}
  .rc .pbtn svg{margin-left:3px}
  .rc .cat{position:absolute;top:.72rem;left:.72rem;font-size:.62rem;letter-spacing:.16em;
      text-transform:uppercase;color:#e9d9b5;background:rgba(10,10,10,.55);
      border:1px solid rgba(201,168,106,.5);padding:.28rem .55rem;border-radius:999px}
  .rc .ttl{position:absolute;left:.85rem;right:.85rem;bottom:.8rem;color:var(--ink);
      font-size:.98rem;line-height:1.25}
  .rc .ttl small{display:block;color:var(--gold);font-size:.68rem;letter-spacing:.12em;
      text-transform:uppercase;margin-bottom:.25rem}
  .rc:hover .th{transform:scale(1.045)}
  .rc:hover{border-color:#3a3a3e}
  .rc:hover .pbtn{transform:translate(-50%,-50%) scale(1.06)}
  .rc .th{transition:transform .5s cubic-bezier(.22,.61,.36,1)}
  .rc .pbtn{transition:transform .25s ease,background .25s ease}

  .empty{text-align:center;color:var(--mut);padding:3.4rem 1rem;border:1px dashed var(--line);
         border-radius:12px}
  .empty strong{color:var(--gold2);font-style:italic;font-weight:400}
  footer{text-align:center;padding:1.6rem 1rem;color:#6e6a60;font-size:.85rem}
.vmodal{position:fixed;z-index:1500;inset:0;display:none;align-items:center;justify-content:center;padding:1rem;
      background:rgba(5,6,8,.9);backdrop-filter:blur(12px)}
  .vmodal.open{display:flex}
  .vshell{width:min(94vw,352px);max-height:96vh;display:flex;flex-direction:column;border-radius:16px;overflow:hidden;
      background:var(--card,#151517);border:1px solid var(--gold2,#26262a);box-shadow:0 48px 110px -20px rgba(0,0,0,.95)}
  .vhead{display:flex;align-items:center;justify-content:space-between;gap:.8rem;padding:.85rem 1rem;
      border-bottom:1px solid var(--line,#26262a)}
  .vhead h3{margin:0;font-size:.98rem;font-weight:400;color:var(--ink,#efe9dc);line-height:1.3}
  .vhead h3 small{display:block;color:var(--gold,#c9a86a);font-size:.62rem;letter-spacing:.18em;
      text-transform:uppercase;margin-bottom:.25rem}
  .vclose{all:unset;cursor:pointer;display:grid;place-items:center;width:2.15rem;height:2.15rem;border-radius:50%;
      color:var(--mut,#8a867c);font-size:1.15rem;line-height:1;border:1px solid var(--line,#26262a);transition:.2s ease}
  .vclose:hover{color:var(--ink);border-color:var(--gold);background:rgba(201,168,106,.1)}
  .vwrap{position:relative;width:100%;aspect-ratio:9/16;background:#000}
  .vshow{position:absolute;inset:0;display:grid;place-items:center;cursor:pointer;
      background-image:radial-gradient(ellipse at center,rgba(201,168,106,.22),transparent 60%)}
  .pbtn2{width:3.7rem;height:3.7rem;border-radius:50%;display:grid;place-items:center;
      background:var(--gold2,#c9a86a);color:#0b0b0c;box-shadow:0 14px 36px rgba(0,0,0,.55)}
  .pbtn2 svg{margin-left:3px}
  .vframe{position:absolute;inset:0;width:100%;height:100%;border:0}
  .vfoot{display:flex;align-items:center;justify-content:space-between;gap:.6rem;padding:.7rem 1rem;
      border-top:1px solid var(--line,#26262a)}
  .vnav{display:flex;gap:.5rem}
  .vnav button{cursor:pointer;font-family:var(--serif,Georgia,serif);font-size:.78rem;letter-spacing:.14em;
      text-transform:uppercase;color:var(--mut,#8a867c);background:transparent;border:1px solid var(--line,#26262a);
      padding:.5rem .95rem;border-radius:999px;transition:.2s ease}
  .vnav button:hover{color:var(--gold);border-color:var(--gold)}
  .vpos{color:var(--mut,#8a867c);font-size:.72rem;letter-spacing:.12em}
  body.vlock,html.vlock{overflow:hidden}
.vmodal{position:fixed;z-index:1500;inset:0;display:none;align-items:center;justify-content:center;padding:1rem;
      background:rgba(5,6,8,.9);backdrop-filter:blur(12px)}
  .vmodal.open{display:flex}
  .vshell{width:min(94vw,352px);max-height:96vh;display:flex;flex-direction:column;border-radius:16px;overflow:hidden;
      background:var(--card,#151517);border:1px solid var(--gold2,#26262a);box-shadow:0 48px 110px -20px rgba(0,0,0,.95)}
  .vhead{display:flex;align-items:center;justify-content:space-between;gap:.8rem;padding:.85rem 1rem;
      border-bottom:1px solid var(--line,#26262a)}
  .vhead h3{margin:0;font-size:.98rem;font-weight:400;color:var(--ink,#efe9dc);line-height:1.3}
  .vhead h3 small{display:block;color:var(--gold,#c9a86a);font-size:.62rem;letter-spacing:.18em;
      text-transform:uppercase;margin-bottom:.25rem}
  .vclose{all:unset;cursor:pointer;display:grid;place-items:center;width:2.15rem;height:2.15rem;border-radius:50%;
      color:var(--mut,#8a867c);font-size:1.15rem;line-height:1;border:1px solid var(--line,#26262a);transition:.2s ease}
  .vclose:hover{color:var(--ink);border-color:var(--gold);background:rgba(201,168,106,.1)}
  .vwrap{position:relative;width:100%;aspect-ratio:9/16;background:#000}
  .vshow{position:absolute;inset:0;display:grid;place-items:center;cursor:pointer;
      background-image:radial-gradient(ellipse at center,rgba(201,168,106,.22),transparent 60%)}
  .pbtn2{width:3.7rem;height:3.7rem;border-radius:50%;display:grid;place-items:center;
      background:var(--gold2,#c9a86a);color:#0b0b0c;box-shadow:0 14px 36px rgba(0,0,0,.55)}
  .pbtn2 svg{margin-left:3px}
  .vframe{position:absolute;inset:0;width:100%;height:100%;border:0}
  .vfoot{display:flex;align-items:center;justify-content:space-between;gap:.6rem;padding:.7rem 1rem;
      border-top:1px solid var(--line,#26262a)}
  .vnav{display:flex;gap:.5rem}
  .vnav button{cursor:pointer;font-family:var(--serif,Georgia,serif);font-size:.78rem;letter-spacing:.14em;
      text-transform:uppercase;color:var(--mut,#8a867c);background:transparent;border:1px solid var(--line,#26262a);
      padding:.5rem .95rem;border-radius:999px;transition:.2s ease}
  .vnav button:hover{color:var(--gold);border-color:var(--gold)}
  .vpos{color:var(--mut,#8a867c);font-size:.72rem;letter-spacing:.12em}
  body.vlock,html.vlock{overflow:hidden}
</style>
</head>
<body>

<header>
  <h1>REELS</h1>
  <p>Cinematic Moments, Beautifully Preserved.</p>
  <nav class="top">
    <a href="index.html">Home</a>
    <a href="cinematography.php">Cinematography</a>
    <a href="reels.php" style="color:var(--gold2)">Reels</a>
    <a href="index.html#contact">Contact Us</a>
  </nav>
</header>

<main>
  <p class="lead">Short films, portrait-style. Filter by occasion and tap any frame to play.</p>

  <div class="filters" id="filters" role="tablist" aria-label="Reel category">
    <?php foreach ($MAIN as $m): ?>
      <button data-m="<?= $esc($m) ?>"
              class="<?= $m === 'all' ? 'on' : '' ?>"><?= $esc($CATLABEL[$m]) ?></button>
    <?php endforeach; ?>
  </div>

  <div class="subfilters <?= /* kept hidden until a parent with subgroups is active */ 'hidden' ?>"
       id="subfilters" role="tablist" aria-label="Reel sub-category"></div>

  <div class="grid" id="grid">
    <?php foreach ($REELS as $r): ?>
      <a class="rc" href="https://youtube.com/shorts/<?= $esc($r['id']) ?>"
         target="_blank" rel="noopener"
         data-cat="<?= $esc($r['cat']) ?>"
         data-sub="<?= $esc($r['sub']) ?>"
         aria-label="Play <?= $esc($r['t']) ?>">
        <img class="th" loading="lazy"
             src="https://i.ytimg.com/vi/<?= $esc($r['id']) ?>/hqdefault.jpg"
             alt="<?= $esc($r['t']) ?>" style="background:<?= $esc($r['bg']) ?>">
        <span class="shade"></span>
        <span class="pbtn"><svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"
              aria-hidden="true"><path d="M8 5.14v13.72a1 1 0 0 0 1.54.84l11.06-6.86a1 1 0 0 0 0-1.68L9.54 4.3A1 1 0 0 0 8 5.14z"/></svg></span>
        <span class="cat"><?= $esc($CATLABEL[$r['cat']]) ?></span>
        <span class="ttl"><small>Short</small><?= $esc($r['t']) ?></span>
      </a>
    <?php endforeach; ?>
  </div>
</main>

<footer>&copy; <?= date('Y') ?> Deepak Studios. All rights reserved.</footer>

<script>
(function () {
  'use strict';
  var DATA = <?= json_encode($REELS, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>;
  var SUB = <?= json_encode($SUB, JSON_UNESCAPED_SLASHES) ?>;
  var SUBLABEL = <?= json_encode($SUBLABEL, JSON_UNESCAPED_SLASHES) ?>;
  var cats = document.querySelector('#filters');
  var subs = document.querySelector('#subfilters');
  var grid = document.querySelector('#grid');
  var main = 'all', sub = 'all';
  var count = document.createElement('div');
  count.id = 'reelcount';
  count.style.cssText = 'text-align:center;color:var(--mut);font-size:.82rem;letter-spacing:.12em;margin-bottom:1.4rem';
  grid.parentNode.insertBefore(count, grid);

  function filterFor(m, s) {
    return DATA.filter(function (r) {
      if (m === 'all') return true;              /* ALL -> every Short */
      if (r.cat !== m) return false;             /* main bucket */
      var blanks = !(SUB[m]);                    /* prewedding has no sub-bar */
      return blanks || s === 'all' || s === r.sub;
    });
  }

  function render() {
    var m = main, s = sub;
    if (m === 'prewedding') s = 'all';
    if (!(SUB[m])) {
      subs.classList.add('hidden');
      subs.innerHTML = '';
      sub = 'all';
      s = 'all';
    } else {
      subs.classList.remove('hidden');
      subs.innerHTML = SUBLABEL[m].map(function (l, i) {
        return '<button data-s="' + SUB[m][i] + '"' +
               ((i === 0 ? ' on class=""' : ' class=') === 'on' && i === 0 ? ' class="on"' : (i === 0 ? ' class="on"' : '')) +
               (i === 0 ? ' class="on"' : '') + '>' + l + '</button>';
      }).join('');
    }
    var list = filterFor(m, s);
    var cur = document.querySelector('.filters button.on');
    var cur2 = document.querySelector('.subfilters button.on');

    if (list.length === 0) {
      grid.innerHTML = '<div class="empty">No <strong>' +
        (m === 'wedding' ? 'Wedding' : m === 'celebration' ? 'Celebration' : '') +
        '</strong> reels yet &mdash; coming soon.</div>';
      count.textContent = '';
    } else {
      grid.innerHTML = list.map(function (r) {
        return '<a class="rc" href="https://youtube.com/shorts/' + r.id + '" target="_blank" rel="noopener" aria-label="Play ' + r.t + '">' +
          '<img class="th" loading="lazy" src="https://i.ytimg.com/vi/' + r.id + '/hqdefault.jpg" alt="' + r.t + '" style="background:' + r.bg + '">' +
          '<span class="shade"></span>' +
          '<span class="pbtn"><svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M8 5.14v13.72a1 1 0 0 0 1.54.84l11.06-6.86a1 1 0 0 0 0-1.68L9.54 4.3A1 1 0 0 0 8 5.14z"/></svg></span>' +
          '<span class="cat">' + r.cat + '</span>' +
          '<span class="ttl"><small>Short</small>' + r.t + '</span>' +
          '</a>';
      }).join('');
      count.textContent = list.length + ' ' + (list.length === 1 ? 'reel' : 'reels');
    }
  }

  cats.addEventListener('click', function (e) {
    var b = e.target.closest('button');
    if (!b) return;
    main = b.getAttribute('data-m');
    document.querySelectorAll('#filters button').forEach(function (x) { x.classList.remove('on'); });
    b.classList.add('on');
    render();
  });

  subs.addEventListener('click', function (e) {
    var b = e.target.closest('button');
    if (!b) return;
    sub = b.getAttribute('data-s');
    document.querySelectorAll('#subfilters button').forEach(function (x) { x.classList.remove('on'); });
    b.classList.add('on');
    render();
  });

  render();
})();
</script>
<div class="vmodal" id="vmodal" role="dialog" aria-modal="true" aria-labelledby="vttl">
  <div class="vshell">
    <div class="vhead">
      <h3 id="vttl"><small id="vcat">Reel</small><span id="vtit">Now Playing</span></h3>
      <button type="button" class="vclose" id="vclose" aria-label="Close player">&#10005;</button>
    </div>
    <div class="vwrap">
      <div class="vshow" id="vshow"><span class="pbtn2" aria-hidden="true">
        <svg width="26" height="26" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5.14v13.72a1 1 0 0 0 1.54.84l11.06-6.86a1 1 0 0 0 0-1.68L9.54 4.3A1 1 0 0 0 8 5.14z"/></svg>
      </span></div>
      <iframe id="vframe" class="vframe" title="YouTube video player"
          allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
          referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
    </div>
    <div class="vfoot">
      <div class="vnav">
        <button type="button" id="vprev">&#8249; Prev</button>
        <button type="button" id="vnext">Next &#8250;</button>
      </div>
      <span class="vpos" id="vpos"></span>
    </div>
  </div>
</div>
<script>
(function () {
  'use strict';
  var modal = document.getElementById('vmodal');
  if (!modal) return;
  var frame = document.getElementById('vframe');
  var vttl = document.getElementById('vttl');
  var vttl2 = document.getElementById('vtit');
  var vcat = document.getElementById('vcat');
  var vpos = document.getElementById('vpos');
  var vclose = document.getElementById('vclose');
  var vprev = document.getElementById('vprev');
  var vnext = document.getElementById('vnext');
  var cards = Array.prototype.slice.call(document.querySelectorAll('[data-v]'));
  var ids = cards.map(function (c) { return c.getAttribute('data-v'); });
  var i = 0, open = false, lastScroll = 0;

  function openAt(n) {
    if (!cards.length) return;
    i = (n % cards.length + cards.length) % cards.length;
    var v = ids[i];
    frame.src = 'https://www.youtube.com/embed/' + v + '?autoplay=1&rel=0&playsinline=1';
    vttl2.textContent = cards[i].getAttribute('data-t') || 'Reel';
    vpos.textContent = (i + 1) + ' / ' + cards.length;
    lastScroll = (window.pageYOffset || document.documentElement.scrollTop) || 0;
    modal.classList.add('open');
    document.body.classList.add('vlock');
    document.documentElement.classList.add('vlock');
    window.scrollTo(0, 0);
    vclose.focus();
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
    var c = e.target.closest ? e.target.closest('[data-v]') : null;
    if (c) { fx(e); open = true; openAt(cards.indexOf(c)); return; }
    if (e.target.closest && e.target.closest('#vclose')) { fx(e); closeAt(); open = false; return; }
    if (e.target.closest && e.target.closest('#vprev')) { fx(e); openAt(i - 1); return; }
    if (e.target.closest && e.target.closest('#vnext')) { fx(e); openAt(i + 1); return; }
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
<div class="vmodal" id="vmodal" role="dialog" aria-modal="true" aria-labelledby="vttl">
  <div class="vshell">
    <div class="vhead">
      <h3 id="vttl"><small id="vcat">Reel</small><span id="vtit">Now Playing</span></h3>
      <button type="button" class="vclose" id="vclose" aria-label="Close player">&#10005;</button>
    </div>
    <div class="vwrap">
      <div class="vshow" id="vshow"><span class="pbtn2" aria-hidden="true">
        <svg width="26" height="26" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5.14v13.72a1 1 0 0 0 1.54.84l11.06-6.86a1 1 0 0 0 0-1.68L9.54 4.3A1 1 0 0 0 8 5.14z"/></svg>
      </span></div>
      <iframe id="vframe" class="vframe" title="YouTube video player"
          allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
          referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
    </div>
    <div class="vfoot">
      <div class="vnav">
        <button type="button" id="vprev">&#8249; Prev</button>
        <button type="button" id="vnext">Next &#8250;</button>
      </div>
      <span class="vpos" id="vpos"></span>
    </div>
  </div>
</div>
<script>
(function () {
  'use strict';
  var modal = document.getElementById('vmodal');
  if (!modal) return;
  var frame = document.getElementById('vframe');
  var vttl = document.getElementById('vttl');
  var vttl2 = document.getElementById('vtit');
  var vcat = document.getElementById('vcat');
  var vpos = document.getElementById('vpos');
  var vclose = document.getElementById('vclose');
  var vprev = document.getElementById('vprev');
  var vnext = document.getElementById('vnext');
  var cards = Array.prototype.slice.call(document.querySelectorAll('[data-v]'));
  var ids = cards.map(function (c) { return c.getAttribute('data-v'); });
  var i = 0, open = false, lastScroll = 0;

  function openAt(n) {
    if (!cards.length) return;
    i = (n % cards.length + cards.length) % cards.length;
    var v = ids[i];
    frame.src = 'https://www.youtube.com/embed/' + v + '?autoplay=1&rel=0&playsinline=1';
    vttl2.textContent = cards[i].getAttribute('data-t') || 'Reel';
    vpos.textContent = (i + 1) + ' / ' + cards.length;
    lastScroll = (window.pageYOffset || document.documentElement.scrollTop) || 0;
    modal.classList.add('open');
    document.body.classList.add('vlock');
    document.documentElement.classList.add('vlock');
    window.scrollTo(0, 0);
    vclose.focus();
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
    var c = e.target.closest ? e.target.closest('[data-v]') : null;
    if (c) { fx(e); open = true; openAt(cards.indexOf(c)); return; }
    if (e.target.closest && e.target.closest('#vclose')) { fx(e); closeAt(); open = false; return; }
    if (e.target.closest && e.target.closest('#vprev')) { fx(e); openAt(i - 1); return; }
    if (e.target.closest && e.target.closest('#vnext')) { fx(e); openAt(i + 1); return; }
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
</body>
</html>
