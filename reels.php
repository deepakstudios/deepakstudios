<?php
/**
 * reels.php ΓÇö Deepak Studios Reels (vertical Shorts).
 *
 * Premium 9:16 Shorts grid with a dynamic two-level filter:
 *   Main : ALL | WEDDING | PRE-WEDDING | CELEBRATION
 *   All : secondary = Main, WEDDING -> ALL┬╖HALDI┬╖MEHENDI┬╖WEDDING┬╖RECEPTION,
 *         CELEBRATION -> ALL┬╖BIRTHDAY┬╖ANNAPRASHAN
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
 * REELS DATA ΓÇö add a new Short here and the page updates itself.
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
    ['id' => 'NygruuRRXhI', 't' => 'Evening Whispers',   'cat' => 'prewedding', 'sub' => '',       'bg' => '#141016'],
    ['id' => 'NO3eWfyXMKE', 't' => 'Haldi Bloom',         'cat' => 'wedding', 'sub' => 'haldi', 'bg' => '#141210'],
    ['id' => 'eP1Fmmfzfk8', 't' => 'Haldi Rhythms',       'cat' => 'wedding', 'sub' => 'haldi', 'bg' => '#1a1410'],
    ['id' => 'gkjS6aAZis8', 't' => 'Wedding Vows',       'cat' => 'wedding', 'sub' => 'wedding', 'bg' => '#161210'],
    ['id' => 'JbotebbamyI', 't' => 'First Dance',        'cat' => 'wedding', 'sub' => 'wedding', 'bg' => '#131018'],
    ['id' => 'xaNML7Jg7Dw', 't' => 'Birthday Sparkle',   'cat' => 'celebration', 'sub' => 'birthday', 'bg' => '#1c1410'],
    ['id' => 'cp0jqejvtJ0', 't' => 'Candlelight Wishes', 'cat' => 'celebration', 'sub' => 'birthday', 'bg' => '#1a1612'],
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

/* =====================================================================
 * REELS HERO IMAGE - SINGLE CONFIG POINT
 * ------------------------------------------------------------------------
 * To change the hero picture: edit ONLY the $reelsHeroImage path below.
 * No HTML, CSS or JS change is needed anywhere else on this page.
 *   $reelsHeroImage : image path (jpg / jpeg / png / webp all work)
 *   $reelsHeroPos   : desktop object-position  (keeps the subject in frame)
 *   $reelsHeroPosMb : phone object-position    (stops the subject being cropped)
 * ===================================================================== */
$reelsHeroImage = 'assets/images/reels-hero.jpg';
$reelsHeroPos   = 'center center';
$reelsHeroPosMb = 'center 30%';

// Shared "Call Now" + WhatsApp floating contact UI (identical to the home page).
require_once __DIR__ . '/lib_contact.php';
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
.social{display:flex;gap:.7rem;justify-content:center;margin:0 0 1rem}
.social a{width:2.4rem;height:2.4rem;border-radius:999px;display:inline-flex;align-items:center;justify-content:center;
  border:1px solid rgba(201,168,106,.35);color:#8a8a8a;transition:all .3s}
.social a:hover{color:#0d0d0d;background:var(--gold);border-color:var(--gold);transform:translateY(-2px);
  box-shadow:0 0 18px rgba(201,168,106,.45)}
.social svg{width:1.1rem;height:1.1rem;fill:currentColor}
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
/* ======== PREMIUM REEL MODAL PLAYER (Deepak Studios) ======== */
.vmodal{position:fixed;z-index:1600;inset:0;display:none;align-items:center;justify-content:center;padding:1.1rem;
      background:rgba(5,6,8,.96);backdrop-filter:blur(14px)}
.vmodal.open{display:flex}
.vshell{width:min(94vw,352px);max-height:96vh;display:flex;flex-direction:column;border-radius:18px;overflow:hidden;
      background:var(--card,#151517);border:1px solid var(--gold2,#26262a);box-shadow:0 64px 150px -24px rgba(0,0,0,.98)}
.vhead{display:flex;align-items:center;justify-content:space-between;gap:.8rem;padding:.9rem 1rem;
      border-bottom:1px solid var(--line,#26262a)}
.vhead h3{margin:0;font-size:.98rem;font-weight:400;color:var(--ink,#efe9dc);line-height:1.35}
.vhead h3 small{display:block;color:var(--gold,#c9a86a);font-size:.6rem;letter-spacing:.18em;
      text-transform:uppercase;margin-bottom:.28rem}
.vclose{all:unset;cursor:pointer;display:grid;place-items:center;width:2.3rem;height:2.3rem;border-radius:50%;
      color:var(--mut,#8a867c);font-size:1.02rem;line-height:1;border:1px solid var(--line,#26262a);transition:.2s ease}
.vclose:hover{color:var(--ink);border-color:var(--gold);background:rgba(201,168,106,.1)}
.vwrap{position:relative;width:100%;aspect-ratio:9/16;background:#000}
.vshow{position:absolute;inset:0;display:grid;place-items:center;cursor:pointer;
      background-image:radial-gradient(ellipse at center,rgba(201,168,106,.22),transparent 60%)}
.pbtn2{width:3.7rem;height:3.7rem;border-radius:50%;display:grid;place-items:center;background:var(--gold,#c9a86a);
      color:#0b0b0c;box-shadow:0 14px 36px rgba(0,0,0,.55)}
.pbtn2 svg{margin-left:3px}
.vframe{position:absolute;inset:0;width:100%;height:100%;border:0}
.vfoot{display:flex;align-items:center;justify-content:space-between;gap:.6rem;padding:.7rem 1rem;
      border-top:1px solid var(--line,#26262a)}
.vnav{display:flex;gap:.5rem}
.vnav button{cursor:pointer;font-family:var(--serif,Georgia,serif);font-size:.74rem;letter-spacing:.14em;
      text-transform:uppercase;color:var(--mut,#8a867c);background:transparent;border:1px solid var(--line,#26262a);
      padding:.5rem .95rem;border-radius:999px;transition:.2s ease}
.vnav button:hover{color:var(--gold);border-color:var(--gold)}
.vpos{color:var(--mut,#8a867c);font-size:.72rem;letter-spacing:.12em}
body.vlock,html.vlock{overflow:hidden}
</style>
<style id="reelpremiumcss">
#reelmodal{position:fixed;inset:0;z-index:99999;display:none;align-items:center;justify-content:center;padding:3vmin;background:rgba(4,4,5,.97)}
#reelmodal.open{display:flex}
body.reel-lock{overflow:hidden}
#reelbox{width:min(94vw,430px);aspect-ratio:9/16;max-height:94vh;display:flex;flex-direction:column;background:#0d0c0a;border:1px solid rgba(212,180,120,.45);border-radius:18px;overflow:hidden;box-shadow:0 60px 220px rgba(0,0,0,.98)}
#reelhead{display:flex;align-items:center;justify-content:space-between;padding:.8rem 1.1rem;background:#151310;border-bottom:1px solid rgba(212,180,120,.4)}
#reelhead h3{margin:0;font-size:1rem;font-weight:800;color:#e9d19a;letter-spacing:.04em}
#reelclose{background:none;border:0;color:#e9d19a;font-size:1.8rem;line-height:1;cursor:pointer}
#reelposter{position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:radial-gradient(circle at 50% 35%,rgba(212,180,120,.08),transparent 55%)}
#reelplay{display:flex;align-items:center;justify-content:center;width:4.6rem;height:4.6rem;border-radius:50%;border:2px solid rgba(232,208,150,.8);color:#e9d19a;background:rgba(232,208,150,.14);font-size:1.5rem;padding-left:.3rem;cursor:pointer}
#reelframe{position:absolute;inset:0;width:100%;height:100%;border:0}
#reelslot{position:relative;flex:1;background:#000}
#reelfoot{display:flex;align-items:center;justify-content:space-between;padding:.65rem 1rem;background:#151310;border-top:1px solid rgba(212,180,120,.4)}
#reelfoot button{background:none;border:1px solid rgba(232,208,150,.55);color:#e9d19a;padding:.5rem 1rem;border-radius:999px;cursor:pointer;font-weight:800;font-size:.82rem;font-family:inherit;letter-spacing:.05em}
#reelcount{color:#cbb061;font-size:.85rem;font-weight:800;letter-spacing:.06em}
</style>
<style id="reelsluxcss">
/* ======== LUXURY REELS EXPERIENCE : fixed nav + cinematic hero ======== */
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
        object-fit:cover;object-position:<?= $esc($reelsHeroPos) ?>;user-select:none}
.rhero-ov{position:absolute;inset:0;z-index:-2;pointer-events:none;background:
        linear-gradient(180deg,rgba(8,8,10,.88) 0%,rgba(8,8,10,.3) 44%,rgba(8,8,10,.93) 100%),
        radial-gradient(ellipse at 62% 46%,rgba(6,6,8,.05) 0%,rgba(6,6,8,.8) 78%)}
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
.rhero-h{margin:0;font-size:clamp(2rem,7vw,5rem);font-weight:400;line-height:1.07;
        letter-spacing:.1em;text-transform:uppercase;color:#f6f1e6;
        text-shadow:0 2px 30px rgba(0,0,0,.72),0 0 62px rgba(201,168,106,.12)}
.rhero-sub{margin:1.65rem auto 0;max-width:34rem;font-size:clamp(1rem,2.3vw,1.26rem);
        line-height:1.62;color:#ddd5c6;font-style:italic}
.rhero-sup{margin:.9rem auto 0;max-width:30rem;font-size:.85rem;letter-spacing:.05em;
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

/* ---------- reels section spacing (existing grid untouched) ---------- */
html{overflow-x:hidden}
body{overflow-x:hidden}
main{padding-top:3.1rem}
.rsec{text-align:center;margin:0 auto 1.9rem}
.rsec-eyebrow{margin:0;font-size:.58rem;letter-spacing:.42em;text-transform:uppercase;color:var(--gold);opacity:.9}
.rsec-h{margin:.7rem 0 0;font-size:clamp(1.15rem,3.4vw,1.7rem);font-weight:400;letter-spacing:.16em;
        text-transform:uppercase;color:var(--ink)}
.rsec-h i{font-style:normal;color:var(--gold);padding:0 .18em}

/* ---------- mobile polish ---------- */
@media (max-width:640px){
  .rhero-img{object-position:<?= $esc($reelsHeroPosMb) ?>}
  .rhero-in{padding-top:calc(var(--rx-head) + 2.1rem);padding-bottom:5.6rem}
  .rhero-h{letter-spacing:.06em}
  .rhero-eyebrow{font-size:.53rem;letter-spacing:.3em}
  .rhero-sub{font-size:.97rem}
  .rhero-cta{width:100%;max-width:19rem;padding:.92rem 1.4rem}
  .rhero-rule{margin:1.15rem auto}
  .rhero-scroll{letter-spacing:.3em}
}
@media (max-width:380px){
  .rbrand-name{font-size:1.08rem;letter-spacing:.1em}
  .rbrand-sub{font-size:.48rem;letter-spacing:.24em}
}
/* ======== end luxury nav + hero ======== */
</style>
<?php ds_contact_head(); ?>
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
      <a href="photography.php">Photography</a>
      <a href="cinematography.php">Cinematography</a>
      <a href="reels.php" class="on" aria-current="page">Reels</a>
      <a href="index.html#contact">Contact Us</a>
      <?php ds_contact_header_call(); ?>
    </nav>
    <button type="button" class="rburger" id="rburger" aria-label="Open menu"
            aria-expanded="false" aria-controls="rmobmenu">
      <span></span><span></span><span></span>
    </button>
  </div>
  <div class="rmobmenu" id="rmobmenu">
    <a href="index.html">Home</a>
    <a href="photography.php">Photography</a>
    <a href="cinematography.php">Cinematography</a>
    <a href="reels.php" class="on" aria-current="page">Reels</a>
    <a href="index.html#contact">Contact Us</a>
  </div>
</header>

<!-- ======== CINEMATIC HERO ======== -->
<section class="rhero" aria-labelledby="rhero-h">
  <img class="rhero-img"
       src="<?= $esc($reelsHeroImage) ?>"
       alt="Deepak Studios cinematic wedding reel"
       width="1920" height="1080"
       fetchpriority="high" decoding="async">
  <span class="rhero-ov" aria-hidden="true"></span>
  <span class="rhero-grain" aria-hidden="true"></span>
  <div class="rhero-in">
    <p class="rhero-eyebrow">DEEPAK STUDIOS <i>&bull;</i> PHOTOGRAPHY</p>
    <span class="rhero-rule" aria-hidden="true"></span>
    <h1 class="rhero-h" id="rhero-h">THE STORIES WE CAPTURE</h1>
    <p class="rhero-sub">Every emotion. Every celebration. Every unforgettable moment.</p>
    <p class="rhero-sup">Explore our Wedding, Pre-Wedding &amp; Celebration Reels.</p>
    <a class="rhero-cta" id="rheroCta" href="#reels">WATCH OUR REELS</a>
  </div>
  <p class="rhero-scroll" aria-hidden="true">SCROLL TO EXPLORE <i>&#8595;</i></p>
</section>

<main id="reels">
  <div class="rsec">
    <p class="rsec-eyebrow">OUR COLLECTION</p>
    <h2 class="rsec-h">WEDDING <i>&bull;</i> PRE-WEDDING <i>&bull;</i> CELEBRATION</h2>
  </div>
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
      <button type="button" class="rc"
data-v="<?= $esc($r['id']) ?>"
data-t="<?= $esc($r['t']) ?>"
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
      </button>
    <?php endforeach; ?>
  </div>
</main>

<footer><div class="social">
<a href="https://www.instagram.com/deepakstudiosofficial_/" target="_blank" rel="noopener noreferrer" aria-label="Deepak Studios on Instagram"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg></a>
<a href="https://www.facebook.com/deepakstudiosfotography" target="_blank" rel="noopener noreferrer" aria-label="Deepak Studios on Facebook"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg></a>
<a href="https://www.youtube.com/@deepakstudiosfotography" target="_blank" rel="noopener noreferrer" aria-label="Deepak Studios on YouTube"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg></a>
</div>&copy; <?= date('Y') ?> Deepak Studios. All rights reserved.</footer>

<?php ds_contact_fab(); ?>

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
        return '<button type="button" class="rc" data-v="' + r.id + '" data-t="' + r.t + '" aria-label="Play ' + r.t + '">' +
          '<img class="th" loading="lazy" src="https://i.ytimg.com/vi/' + r.id + '/hqdefault.jpg" alt="' + r.t + '" style="background:' + r.bg + '">' +
          '<span class="shade"></span>' +
          '<span class="pbtn"><svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M8 5.14v13.72a1 1 0 0 0 1.54.84l11.06-6.86a1 1 0 0 0 0-1.68L9.54 4.3A1 1 0 0 0 8 5.14z"/></svg></span>' +
          '<span class="cat">' + r.cat + '</span>' +
          '<span class="ttl"><small>Short</small>' + r.t + '</span>' +
          '</button>';
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
  function liveCards() { return Array.prototype.slice.call(document.querySelectorAll('#grid [data-v]')); }
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
    var v = cards[i].getAttribute('data-v');
    frame.src = 'https://www.youtube-nocookie.com/embed/' + v + '?autoplay=1&rel=0';
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
    if (c) { fx(e); open = true; openAt(0, c); return; }
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

<!-- ======== PREMIUM NAV + HERO CTA (added, existing reel logic untouched) ======== -->
<script>
(function () {
  'use strict';
  var nav   = document.getElementById('rnav');
  var burger= document.getElementById('rburger');
  var menu  = document.getElementById('rmobmenu');
  var cta   = document.getElementById('rheroCta');

  if (nav) {
    var onScroll = function () { nav.classList.toggle('solid', (window.pageYOffset || 0) > 24); };
    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });
  }

  if (burger && menu) {
    var setMenu = function (open) {
      menu.classList.toggle('open', open);
      burger.classList.toggle('x', open);
      burger.setAttribute('aria-expanded', open ? 'true' : 'false');
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
      var target = document.getElementById('reels');
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
