<?php
/**
 * photography.php — Client-wise Photography Portfolio | Deepak Studios
 *
 * Royal luxury page (dark maroon / deep black / gold). Page-specific styles
 * only — the navbar, footer, WhatsApp/mobile bar and Call Now pill are the
 * existing shared implementations (floating controls come from lib_contact.php).
 *
 * HIERARCHY
 *   Client story -> event-wise galleries -> event photographs.
 *
 * FOLDER STRUCTURE (auto-scanned)
 *   photos/photography/clients/
 *       client-01/
 *           cover.webp              (optional client cover)
 *           mehendi/                (event folder = event name)
 *               cover.webp          (optional event cover)
 *               01.webp
 *               02.webp
 *           wedding/
 *               ...
 *
 *   Add a client by creating a folder, adding event subfolders and uploading
 *   photographs. No HTML/PHP edit is needed for a new client or event.
 *   Folder / file names should use letters, numbers, hyphens or underscores.
 *
 * DISPLAY TITLES
 *   $CLIENT_META below maps a client folder to its approved title and intro.
 *   $EVENT_LABELS maps event folder names to readable names; anything not
 *   listed is converted automatically ("mehendi" -> "Mehendi").
 *
 * EXISTING PHOTOGRAPHS
 *   The four Mehendi/Wedding photographs under photos/photography/ are not
 *   assigned to a client yet. They stay in place and appear in the
 *   "Awaiting Client Assignment" section. To attach one to a client story,
 *   add a row to $PHOTO_ASSIGNMENTS (file path => client + event). Nothing is
 *   moved or renamed.
 */

declare(strict_types=1);

require_once __DIR__ . '/lib_contact.php';

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
 * Set $pgHeroImage to a local image path (jpg / jpeg / png / webp) when the
 * real hero photograph is ready. Leave it EMPTY ('') to keep the elegant
 * dark-maroon placeholder with the COMING SOON label.
 * ===================================================================== */
$pgHeroImage = '';

/* =====================================================================
 * IMAGE / FOLDER CONFIGURATION
 * ===================================================================== */
$IMAGE_EXT  = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'avif'];
$CLIENTS_DIR = 'photos/photography/clients';

/* Approved client titles + intros. Add one entry per client folder when the
   name and copy are ready; otherwise the folder name is shown readably. */
$CLIENT_META = [
    // 'client-01' => [
    //     'title'    => 'Nidhi & Shubham',
    //     'intro'    => 'A short introduction supplied by the client.',
    // ],
];

/* Attach existing photographs (outside clients/) to a client story + event
   without moving or renaming the file. Key = path relative to this page. */
$PHOTO_ASSIGNMENTS = [
    // 'photos/photography/Mehendi/Mehendi.webp' => ['client' => 'client-01', 'event' => 'mehendi'],
    // 'photos/photography/Wedding/Bride.webp'   => ['client' => 'client-01', 'event' => 'wedding'],
    // 'photos/photography/Wedding/bride2.webp'  => ['client' => 'client-01', 'event' => 'wedding'],
    // 'photos/photography/Wedding/Couple.webp'  => ['client' => 'client-01', 'event' => 'wedding'],
];

/* Event folder name -> readable label. Unknown folders are converted
   automatically: "mehendi" -> "Mehendi", "pre-wedding" -> "Pre-Wedding". */
$EVENT_LABELS = [
    'prewedding'   => 'Pre-Wedding',
    'pre-wedding'  => 'Pre-Wedding',
    'pre_wedding'  => 'Pre-Wedding',
    'haldi'        => 'Haldi',
    'mehendi'      => 'Mehendi',
    'sangeet'      => 'Sangeet',
    'engagement'   => 'Engagement',
    'wedding'      => 'Wedding',
    'reception'    => 'Reception',
    'birthday'     => 'Birthday',
    'anniversary'  => 'Anniversary',
    'housewarming' => 'Housewarming',
    'celebration'  => 'Celebration',
    'celebrations' => 'Celebrations',
];

/* Main-page filter definitions. Only categories that hold clients render. */
$FILTER_LABELS = [
    'all'         => 'All',
    'wedding'     => 'Wedding',
    'prewedding'  => 'Pre-Wedding',
    'celebration' => 'Celebration',
];

/* Which filter a client belongs to, based on its event folders. */
$EVENT_CATEGORY = [
    'wedding'      => 'wedding',
    'reception'    => 'wedding',
    'engagement'   => 'wedding',
    'haldi'        => 'wedding',
    'mehendi'      => 'wedding',
    'sangeet'      => 'wedding',
    'cocktail'     => 'wedding',
    'baraat'       => 'wedding',
    'nikah'        => 'wedding',
    'phera'        => 'wedding',
    'sagai'        => 'wedding',
    'roka'         => 'wedding',
    'vidai'        => 'wedding',
    'prewedding'   => 'prewedding',
    'pre-wedding'  => 'prewedding',
    'pre_wedding'  => 'prewedding',
    'couple'       => 'prewedding',
    'outdoor'      => 'prewedding',
    'birthday'     => 'celebration',
    'anniversary'  => 'celebration',
    'housewarming' => 'celebration',
    'celebration'  => 'celebration',
    'celebrations' => 'celebration',
    'baby'         => 'celebration',
    'maternity'    => 'celebration',
];

/* Canonical event order inside a client story. */
$EVENT_ORDER = [
    'haldi'       => 10,
    'mehendi'     => 20,
    'sangeet'     => 30,
    'engagement'  => 40,
    'prewedding'  => 50,
    'pre-wedding' => 50,
    'pre_wedding' => 50,
    'wedding'     => 60,
    'reception'   => 70,
    'birthday'    => 80,
    'anniversary' => 90,
    'housewarming' => 100,
];

/* =====================================================================
 * FILESYSTEM HELPERS (all reads are guarded; no user input reaches a path)
 * ===================================================================== */
$slugOk = static function (string $s): bool {
    return preg_match('/^[A-Za-z0-9][A-Za-z0-9_-]*$/', $s) === 1;
};

/** Direct sub-folders whose names are safe URL slugs. */
$listDirs = static function (string $dir) use ($slugOk): array {
    if (!is_dir($dir)) {
        return [];
    }
    $entries = @scandir($dir);
    if ($entries === false) {
        return [];
    }
    $out = [];
    foreach ($entries as $name) {
        if ($name === '' || $name[0] === '.') {
            continue;
        }
        if (!$slugOk($name) || !is_dir($dir . '/' . $name)) {
            continue;
        }
        $out[] = $name;
    }
    sort($out, SORT_NATURAL | SORT_FLAG_CASE);
    return $out;
};

/** Image files in one folder, sorted; cover.* is treated as a cover only. */
$scanImages = static function (string $dir) use ($IMAGE_EXT): array {
    if (!is_dir($dir)) {
        return [];
    }
    $entries = @scandir($dir);
    if ($entries === false) {
        return [];
    }
    $out = [];
    foreach ($entries as $file) {
        if ($file === '' || $file[0] === '.') {
            continue;
        }
        if (!is_file($dir . '/' . $file)) {
            continue;
        }
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        if (!in_array($ext, $IMAGE_EXT, true)) {
            continue;
        }
        if (strtolower(pathinfo($file, PATHINFO_FILENAME)) === 'cover') {
            continue;
        }
        $out[] = $file;
    }
    sort($out, SORT_NATURAL | SORT_FLAG_CASE);
    return $out;
};

/** Optional cover.* file inside a folder. */
$findCover = static function (string $dir) use ($IMAGE_EXT): ?string {
    if (!is_dir($dir)) {
        return null;
    }
    $entries = @scandir($dir);
    if ($entries === false) {
        return null;
    }
    sort($entries, SORT_NATURAL | SORT_FLAG_CASE);
    foreach ($entries as $file) {
        if ($file === '' || $file[0] === '.') {
            continue;
        }
        if (!is_file($dir . '/' . $file)) {
            continue;
        }
        if (strtolower(pathinfo($file, PATHINFO_FILENAME)) !== 'cover') {
            continue;
        }
        if (!in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), $IMAGE_EXT, true)) {
            continue;
        }
        return $file;
    }
    return null;
};

/** Folder slug -> readable label. */
$pretty = static function (string $slug): string {
    return ucwords(str_replace(['-', '_'], ' ', $slug));
};

/** Intrinsic dimensions for a local file; [0,0] when unreadable. */
$imageSize = static function (string $path): array {
    $info = @getimagesize($path);
    return $info ? [(int) $info[0], (int) $info[1]] : [0, 0];
};

/** Public URL for a file inside a relative folder (encoded per segment). */
$imgSrc = static function (string $relDir, string $file): string {
    return $relDir . '/' . rawurlencode($file);
};

/* =====================================================================
 * EXISTING PHOTOGRAPHS - staged until a client is assigned
 * ------------------------------------------------------------------------
 * Every image folder directly inside photos/photography/ (except the
 * clients/ folder) is treated as an unassigned event group. Nothing is
 * moved, renamed or deleted; the photographs simply stay visible in the
 * "Awaiting Client Assignment" strip until $PHOTO_ASSIGNMENTS maps them.
 * ===================================================================== */
$UNASSIGNED_TITLES = [
    'mehendi/mehendi.webp' => 'Mehendi Ceremony',
    'wedding/bride.webp'   => 'Bridal Portrait',
    'wedding/bride2.webp'  => 'Bridal Portrait',
    'wedding/couple.webp'  => 'Wedding Couple',
];

$UNASSIGNED = [];
foreach ($listDirs(__DIR__ . '/photos/photography') as $groupSlug) {
    if ($groupSlug === 'clients') {
        continue; // assigned client stories are scanned separately
    }

    $groupRel = 'photos/photography/' . $groupSlug;
    $label    = $EVENT_LABELS[$groupSlug] ?? $pretty($groupSlug);

    foreach ($scanImages(__DIR__ . '/' . $groupRel) as $file) {
        $rel = $groupRel . '/' . $file;
        [$w, $h] = $imageSize(__DIR__ . '/' . $rel);
        $UNASSIGNED[$rel] = [
            'src'      => $imgSrc($groupRel, $file),
            't'        => $UNASSIGNED_TITLES[strtolower($groupSlug . '/' . $file)] ?? $label . ' Photograph',
            'meta'     => $label,
            'w'        => $w,
            'h'        => $h,
            'assigned' => false,
        ];
    }
}

/* =====================================================================
 * CLIENT STORIES - auto-scanned from photos/photography/clients/
 * ===================================================================== */
$CLIENTS = [];
foreach ($listDirs(__DIR__ . '/' . $CLIENTS_DIR) as $clientSlug) {
    $clientRel = $CLIENTS_DIR . '/' . $clientSlug;
    $events = [];

    foreach ($listDirs(__DIR__ . '/' . $clientRel) as $eventSlug) {
        $eventRel = $clientRel . '/' . $eventSlug;
        $photos = [];
        foreach ($scanImages(__DIR__ . '/' . $eventRel) as $file) {
            [$w, $h] = $imageSize(__DIR__ . '/' . $eventRel . '/' . $file);
            $photos[] = [
                'src' => $imgSrc($eventRel, $file),
                'w'   => $w,
                'h'   => $h,
            ];
        }
        $events[$eventSlug] = [
            'slug'   => $eventSlug,
            'label'  => $EVENT_LABELS[$eventSlug] ?? $pretty($eventSlug),
            'dir'    => $eventRel,
            'photos' => $photos,
            'cover'  => null,
            'cover_w' => 0,
            'cover_h' => 0,
        ];
    }

    $CLIENTS[$clientSlug] = [
        'slug'   => $clientSlug,
        'dir'    => $clientRel,
        'events' => $events,
    ];
}

/* Attach the staged photographs to client events when mapped. */
foreach ($PHOTO_ASSIGNMENTS as $rel => $map) {
    if (!isset($UNASSIGNED[$rel])) {
        continue;
    }
    $assignedClient = isset($map['client']) ? (string) $map['client'] : '';
    $assignedEvent  = isset($map['event']) ? (string) $map['event'] : '';
    if (!isset($CLIENTS[$assignedClient]['events'][$assignedEvent])) {
        continue;
    }
    $CLIENTS[$assignedClient]['events'][$assignedEvent]['photos'][] = [
        'src' => $UNASSIGNED[$rel]['src'],
        'w'   => $UNASSIGNED[$rel]['w'],
        'h'   => $UNASSIGNED[$rel]['h'],
    ];
    $UNASSIGNED[$rel]['assigned'] = true;
}

/* Finalise every client: drop empty events, sort, resolve covers + counts. */
foreach ($CLIENTS as $clientSlug => &$client) {
    $client['events'] = array_filter(
        $client['events'],
        static function (array $event): bool {
            return count($event['photos']) > 0;
        }
    );

    if (!$client['events']) {
        unset($CLIENTS[$clientSlug]);
        continue;
    }

    uksort($client['events'], static function (string $a, string $b) use ($EVENT_ORDER): int {
        $oa = $EVENT_ORDER[$a] ?? 500;
        $ob = $EVENT_ORDER[$b] ?? 500;
        return $oa === $ob ? strnatcasecmp($a, $b) : $oa <=> $ob;
    });

    $photoCount = 0;
    foreach ($client['events'] as &$event) {
        $photoCount += count($event['photos']);

        $coverFile = $findCover(__DIR__ . '/' . $event['dir']);
        if ($coverFile !== null) {
            $event['cover']   = $imgSrc($event['dir'], $coverFile);
            [$event['cover_w'], $event['cover_h']] = $imageSize(__DIR__ . '/' . $event['dir'] . '/' . $coverFile);
        } else {
            $first = $event['photos'][0];
            $event['cover']   = $first['src'];
            $event['cover_w'] = $first['w'];
            $event['cover_h'] = $first['h'];
        }
    }
    unset($event);

    $meta    = $CLIENT_META[$clientSlug] ?? [];
    $title   = trim((string) ($meta['title'] ?? ''));
    $intro   = trim((string) ($meta['intro'] ?? ''));

    /* Categories come from the event folders that actually exist, so the
       filters can never advertise an empty gallery. A story with a wedding
       and a pre-wedding event appears under both filters. */
    $present = [];
    foreach ($client['events'] as $event) {
        $present[$EVENT_CATEGORY[$event['slug']] ?? 'celebration'] = true;
    }
    $clientFilters = [];
    foreach (['wedding', 'prewedding', 'celebration'] as $candidate) {
        if (isset($present[$candidate])) {
            $clientFilters[] = $candidate;
        }
    }
    if (!$clientFilters) {
        $clientFilters = ['celebration'];
    }

    $firstEvent = reset($client['events']); // events are sorted canonically
    $coverFile  = $findCover(__DIR__ . '/' . $client['dir']);
    if ($coverFile !== null) {
        $client['cover']   = $imgSrc($client['dir'], $coverFile);
        [$client['cover_w'], $client['cover_h']] = $imageSize(__DIR__ . '/' . $client['dir'] . '/' . $coverFile);
    } else {
        $client['cover']   = $firstEvent['cover'];
        $client['cover_w'] = $firstEvent['cover_w'];
        $client['cover_h'] = $firstEvent['cover_h'];
    }

    $client['title']        = $title !== '' ? $title : $pretty($clientSlug);
    $client['intro']        = $intro;
    $client['filters']      = $clientFilters;
    $client['filter']       = $clientFilters[0];
    $client['filter_label'] = $FILTER_LABELS[$clientFilters[0]];
    $client['main_event']   = $firstEvent['label'];
    $client['event_count']  = count($client['events']);
    $client['photo_count']  = $photoCount;
}
unset($client);

uasort($CLIENTS, static function (array $a, array $b): int {
    return strnatcasecmp($a['title'], $b['title']);
});

$UNASSIGNED_PHOTOS = [];
foreach ($UNASSIGNED as $photo) {
    if (!$photo['assigned']) {
        $UNASSIGNED_PHOTOS[] = $photo;
    }
}

$CLIENT_COUNT  = count($CLIENTS);
$TOTAL_EVENTS  = 0;
$TOTAL_PHOTOS  = 0;
$FILTER_COUNTS = ['all' => $CLIENT_COUNT];
foreach ($CLIENTS as $client) {
    $TOTAL_EVENTS += $client['event_count'];
    $TOTAL_PHOTOS += $client['photo_count'];
    foreach ($client['filters'] as $filterKey) {
        $FILTER_COUNTS[$filterKey] = ($FILTER_COUNTS[$filterKey] ?? 0) + 1;
    }
}

/* =====================================================================
 * ROUTING
 *   photography.php                          -> client stories
 *   photography.php?client=client-01         -> client story
 *   photography.php?client=client-01&event=wedding -> event gallery
 * ===================================================================== */
$clientSlug = isset($_GET['client']) && is_string($_GET['client']) ? trim($_GET['client']) : '';
$eventSlug  = isset($_GET['event']) && is_string($_GET['event']) ? trim($_GET['event']) : '';

$view         = 'main';
$activeClient = null;
$activeEvent  = null;
$notFound     = false;

if ($clientSlug !== '') {
    if (!isset($CLIENTS[$clientSlug])) {
        $notFound = true;
    } else {
        $activeClient = $CLIENTS[$clientSlug];
        if ($eventSlug !== '') {
            if (!isset($activeClient['events'][$eventSlug])) {
                $notFound = true;
            } else {
                $activeEvent = $activeClient['events'][$eventSlug];
                $view = 'event';
            }
        } else {
            $view = 'client';
        }
    }
} elseif ($eventSlug !== '') {
    $notFound = true;
}

if ($notFound) {
    $view = 'notfound';
    http_response_code(404);
}

$clientUrl = static function (string $slug): string {
    return 'photography.php?client=' . rawurlencode($slug);
};
$eventUrl = static function (string $client, string $event): string {
    return 'photography.php?client=' . rawurlencode($client) . '&event=' . rawurlencode($event);
};

$pageTitle = 'Photography | Deepak Studios';
if ($view === 'client' && $activeClient !== null) {
    $pageTitle = $activeClient['title'] . ' | Photography | Deepak Studios';
} elseif ($view === 'event' && $activeClient !== null && $activeEvent !== null) {
    $pageTitle = $activeEvent['label'] . ' — ' . $activeClient['title'] . ' | Photography | Deepak Studios';
} elseif ($view === 'notfound') {
    $pageTitle = 'Story Not Found | Photography | Deepak Studios';
}

$pageDesc = 'Client-wise wedding photography portfolio by Deepak Studios. Explore each client story, event-wise galleries and photographs.';

/* Shared photo-card renderer (used by event galleries and the staged strip). */
$renderPhotos = static function (array $photos, string $category, bool $showTitle = false) use ($esc): void {
    foreach ($photos as $photo) {
        ?>
        <button type="button" class="photo-item"
                data-src="<?= $esc($photo['src']) ?>"
                data-cat="<?= $esc($category) ?>"
                data-title="<?= $esc($photo['t'] ?? '') ?>"
                aria-label="View photograph">
          <img loading="lazy" decoding="async" src="<?= $esc($photo['src']) ?>" alt="<?= $esc($category) ?>"
               <?php if (!empty($photo['w']) && !empty($photo['h'])): ?>width="<?= (int) $photo['w'] ?>" height="<?= (int) $photo['h'] ?>"<?php endif; ?>>
          <span class="photo-shade" aria-hidden="true"></span>
          <span class="photo-zoom" aria-hidden="true"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M15.5 14h-.79l-.28-.27A6.47 6.47 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg></span>
          <?php if ($showTitle && !empty($photo['t'])): ?><span class="photo-title"><?= $esc($photo['t']) ?></span><?php endif; ?>
        </button>
        <?php
    }
};
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $esc($pageTitle) ?></title>
<meta name="description" content="<?= $esc($pageDesc) ?>">
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

/* ======== HERO ======== */
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

/* ======== main-page filters ======== */
.filters{display:flex;flex-wrap:wrap;gap:.5rem 1.4rem;justify-content:center;margin:0 0 2.6rem}
.filters button{cursor:pointer;color:var(--mut);font-family:var(--serif);font-size:.72rem;
        letter-spacing:.2em;text-transform:uppercase;background:transparent;border:0;
        border-bottom:2px solid transparent;padding:.5rem .15rem;
        transition:color .25s ease,border-color .25s ease}
.filters button .fc{color:var(--gold);font-size:.62rem;letter-spacing:.1em;margin-left:.4em;opacity:.8}
.filters button:hover{color:var(--ink)}
.filters button.on{color:var(--gold2);border-bottom-color:var(--gold)}
.filters button.on .fc{opacity:1}

/* ======== client story cards (main page) ======== */
.client-grid{display:grid;grid-template-columns:1fr;gap:1.15rem}
@media(min-width:620px){.client-grid{grid-template-columns:repeat(2,1fr)}}
@media(min-width:1024px){.client-grid{grid-template-columns:repeat(3,1fr)}}
.client-card{display:flex;flex-direction:column;background:var(--card);
        border:1px solid var(--line);border-radius:14px;overflow:hidden;
        transition:border-color .35s ease,transform .35s ease,box-shadow .35s ease}
.client-card:hover{border-color:rgba(201,168,106,.6);transform:translateY(-4px);
        box-shadow:0 24px 54px -26px rgba(0,0,0,.95),0 0 26px rgba(201,168,106,.12)}
.client-card[hidden]{display:none}
.cc-media{position:relative;display:block;aspect-ratio:4/5;overflow:hidden;background:#120c0d}
.cc-media img{display:block;width:100%;height:100%;object-fit:cover;
        transition:transform .6s cubic-bezier(.22,.61,.36,1)}
.client-card:hover .cc-media img{transform:scale(1.04)}
.cc-shade{position:absolute;inset:0;pointer-events:none;
        background:linear-gradient(180deg,rgba(0,0,0,.05) 0%,rgba(0,0,0,0) 45%,rgba(0,0,0,.72) 100%)}
.cc-tag{position:absolute;top:.8rem;left:.8rem;font-size:.56rem;letter-spacing:.2em;
        text-transform:uppercase;color:#f0d896;background:rgba(9,9,10,.62);
        border:1px solid rgba(201,168,106,.45);border-radius:999px;padding:.3rem .7rem}
.cc-body{display:flex;flex-direction:column;gap:.55rem;padding:1.15rem 1.2rem 1.3rem}
.cc-title{font-size:1.05rem;letter-spacing:.1em;text-transform:uppercase;color:var(--ink);line-height:1.35}
.cc-meta{font-size:.78rem;letter-spacing:.08em;color:var(--mut)}
.cc-cta{font-size:.66rem;letter-spacing:.22em;text-transform:uppercase;color:var(--gold2)}
.cc-cta i{font-style:normal;display:inline-block;margin-left:.35em;transition:transform .3s ease}
.client-card:hover .cc-cta i{transform:translateX(3px)}

/* ======== sub-page header + breadcrumb ======== */
.subhead{padding:calc(var(--rx-head) + 3.4rem) 0 3rem;position:relative}
.crumbs{display:flex;flex-wrap:wrap;align-items:center;gap:.55rem;margin:0 0 2rem;
        font-size:.6rem;letter-spacing:.2em;text-transform:uppercase;color:var(--mut)}
.crumbs a{color:var(--mut);transition:color .25s ease}
.crumbs a:hover{color:var(--gold2)}
.crumbs .sep{color:rgba(201,168,106,.55)}
.crumbs .cur{color:var(--gold2)}
.page-h{margin:.8rem 0 0;font-size:clamp(1.7rem,5vw,3.2rem);font-weight:400;line-height:1.12;
        letter-spacing:.08em;text-transform:uppercase;color:#f6f1e6}
.page-meta{margin:1rem 0 0;color:var(--gold);font-size:.76rem;letter-spacing:.16em;
        text-transform:uppercase}
.story-grid{display:grid;grid-template-columns:1fr;gap:1.6rem;align-items:center}
@media(min-width:900px){.story-grid{grid-template-columns:minmax(0,.9fr) minmax(0,1.1fr);gap:2.6rem}}
.story-media{position:relative;border-radius:16px;overflow:hidden;border:1px solid rgba(201,168,106,.3);
        aspect-ratio:4/5;box-shadow:0 30px 80px -40px rgba(0,0,0,.95);background:#120c0d}
.story-media img{display:block;width:100%;height:100%;object-fit:cover}
.story-intro{margin:1.2rem 0 0;color:var(--mut);font-size:1rem;font-style:italic;line-height:1.75;
        max-width:36rem}
.story-note{margin:1rem 0 0;color:var(--mut);font-size:.82rem;line-height:1.65}
.story-back{display:inline-block;margin-top:1.5rem;font-size:.66rem;letter-spacing:.22em;
        text-transform:uppercase;color:var(--gold2)}

/* ======== event cards (client story) ======== */
.event-grid{display:grid;grid-template-columns:1fr;gap:1.15rem}
@media(min-width:620px){.event-grid{grid-template-columns:repeat(2,1fr)}}
@media(min-width:1024px){.event-grid{grid-template-columns:repeat(3,1fr)}}
.event-card{display:flex;flex-direction:column;background:var(--card);border:1px solid var(--line);
        border-radius:14px;overflow:hidden;transition:border-color .35s ease,transform .35s ease,box-shadow .35s ease}
.event-card:hover{border-color:rgba(201,168,106,.6);transform:translateY(-4px);
        box-shadow:0 24px 54px -26px rgba(0,0,0,.95),0 0 26px rgba(201,168,106,.12)}
.ev-media{position:relative;display:block;aspect-ratio:4/3;overflow:hidden;background:#120c0d}
.ev-media img{display:block;width:100%;height:100%;object-fit:cover;
        transition:transform .6s cubic-bezier(.22,.61,.36,1)}
.event-card:hover .ev-media img{transform:scale(1.04)}
.ev-shade{position:absolute;inset:0;pointer-events:none;
        background:linear-gradient(180deg,rgba(0,0,0,0) 48%,rgba(0,0,0,.7) 100%)}
.ev-count{position:absolute;right:.8rem;bottom:.8rem;font-size:.56rem;letter-spacing:.16em;
        text-transform:uppercase;color:#f0d896;background:rgba(9,9,10,.68);
        border:1px solid rgba(201,168,106,.45);border-radius:999px;padding:.3rem .7rem}
.ev-body{display:flex;align-items:center;justify-content:space-between;gap:1rem;
        padding:1.1rem 1.2rem 1.25rem}
.ev-title{font-size:1rem;letter-spacing:.14em;text-transform:uppercase;color:var(--ink)}
.ev-cta{font-size:.62rem;letter-spacing:.18em;text-transform:uppercase;color:var(--gold2);white-space:nowrap}
.event-card:hover .ev-title{color:var(--gold2)}

/* ======== event photographs (natural aspect ratios, no cropping) ======== */
.photo-grid{columns:1;column-gap:1rem}
@media(min-width:620px){.photo-grid{columns:2}}
@media(min-width:1024px){.photo-grid{columns:3}}
.photo-item{position:relative;display:block;width:100%;margin:0 0 1rem;padding:0;text-align:left;
        font-family:var(--serif);color:inherit;cursor:zoom-in;background:var(--card);
        border:1px solid var(--line);border-radius:12px;overflow:hidden;
        break-inside:avoid;-webkit-column-break-inside:avoid;page-break-inside:avoid;
        box-shadow:0 12px 32px -18px rgba(0,0,0,.85);
        transition:border-color .35s ease,box-shadow .35s ease}
.photo-item:hover{border-color:rgba(201,168,106,.55);
        box-shadow:0 20px 46px -20px rgba(0,0,0,.9),0 0 20px rgba(201,168,106,.1)}
.photo-item img{display:block;width:100%;height:auto;
        transition:transform .6s cubic-bezier(.22,.61,.36,1)}
.photo-item:hover img{transform:scale(1.03)}
.photo-shade{position:absolute;inset:0;pointer-events:none;
        background:linear-gradient(180deg,rgba(0,0,0,0) 55%,rgba(0,0,0,.62) 100%)}
.photo-title{position:absolute;left:.9rem;right:.9rem;bottom:.8rem;color:var(--ink);
        font-size:.88rem;line-height:1.3;text-shadow:0 2px 12px rgba(0,0,0,.85)}
.photo-zoom{position:absolute;top:50%;left:50%;width:3rem;height:3rem;border-radius:50%;
        display:grid;place-items:center;background:rgba(230,201,128,.95);color:#0b0b0c;
        opacity:0;transform:translate(-50%,-50%) scale(.92);
        transition:opacity .3s ease,transform .3s ease;box-shadow:0 6px 18px rgba(0,0,0,.45)}
.photo-item:hover .photo-zoom,.photo-item:focus-visible .photo-zoom{opacity:1;
        transform:translate(-50%,-50%) scale(1)}
.photo-zoom svg{width:1.15rem;height:1.15rem}

/* ======== empty / not-found states ======== */
.empty-state{text-align:center;padding:3.4rem 1.4rem;border:1px dashed var(--line);border-radius:14px;
        background:linear-gradient(180deg,rgba(201,168,106,.035),transparent)}
.empty-state strong{display:block;color:var(--gold2);font-weight:400;font-size:1.05rem;
        letter-spacing:.16em;text-transform:uppercase;margin-bottom:.8rem}
.empty-state p{margin:0 auto;max-width:34rem;color:var(--mut);font-size:.9rem;line-height:1.75;font-style:italic}
.empty-state .es-link{display:inline-block;margin-top:1.5rem;font-size:.66rem;letter-spacing:.22em;
        text-transform:uppercase;color:var(--gold2)}

/* ======== lightbox ======== */
.lightbox{display:none;position:fixed;inset:0;z-index:1800;align-items:center;justify-content:center;
        padding:1.4rem;cursor:zoom-out;background:rgba(6,6,8,.97);
        backdrop-filter:blur(14px);-webkit-backdrop-filter:blur(14px)}
.lightbox.open{display:flex}
.lb-shell{position:relative;max-width:min(94vw,1200px);max-height:90vh;display:flex;
        flex-direction:column;gap:.85rem;cursor:default}
.lightbox img.lb-img{display:block;max-width:100%;max-height:82vh;border-radius:12px;
        border:1px solid rgba(201,168,106,.34);box-shadow:0 50px 120px -30px rgba(0,0,0,.95);
        background:#0d0d0d}
.lb-cap{text-align:center;color:var(--ink);font-size:.92rem;line-height:1.4;margin:0}
.lb-cap small{display:block;color:var(--gold);font-size:.62rem;letter-spacing:.2em;
        text-transform:uppercase;margin-bottom:.24rem}
.lb-count{display:block;margin-top:.35rem;color:var(--mut);font-size:.68rem;letter-spacing:.16em}
.lb-close{position:fixed;top:.9rem;right:1.3rem;width:2.6rem;height:2.6rem;border-radius:50%;
        display:grid;place-items:center;font-size:1.2rem;line-height:1;color:var(--mut);
        background:rgba(12,12,14,.7);border:1px solid var(--line);cursor:pointer;z-index:3;
        transition:color .2s ease,border-color .2s ease,background .2s ease}
.lb-close:hover{color:var(--ink);border-color:var(--gold);background:rgba(201,168,106,.12)}
.lb-nav{position:fixed;top:50%;transform:translateY(-50%);width:2.9rem;height:2.9rem;
        border-radius:50%;display:grid;place-items:center;font-size:1.4rem;line-height:1;
        color:var(--ink);background:rgba(12,12,14,.72);border:1px solid var(--line);
        cursor:pointer;z-index:3;transition:color .2s ease,border-color .2s ease,background .2s ease}
.lb-nav:hover{color:#0b0b0c;background:var(--gold2);border-color:var(--gold2)}
.lb-prev{left:max(.5rem,2vw)}
.lb-next{right:max(.5rem,2vw)}
body.vlock,html.vlock{overflow:hidden}

/* ======== enquiry + footer ======== */
.book{background:radial-gradient(60% 60% at 50% 28%,rgba(74,18,22,.5),transparent 70%),#08080a;
        border-top:1px solid var(--line);text-align:center}
.book h2{margin:.8rem 0 0;font-size:clamp(1.4rem,4.2vw,2.4rem);font-weight:400;line-height:1.35;
        letter-spacing:.06em;text-transform:uppercase;color:#f6f1e6}
.book h2 em{font-style:normal;color:var(--gold2)}
.book-lead{margin:1.3rem 0 0;color:var(--gold2);font-size:.72rem;letter-spacing:.34em;
        text-transform:uppercase}
.book p.book-copy{margin:1rem auto 0;max-width:36rem;color:var(--mut);font-style:italic;line-height:1.7}
.book-btns{display:flex;flex-wrap:wrap;gap:.9rem;justify-content:center;margin-top:2.2rem}
.book-btn{display:inline-flex;align-items:center;gap:.6rem;padding:.95rem 2.2rem;border-radius:999px;
        font-size:.72rem;letter-spacing:.24em;text-transform:uppercase;font-weight:600;
        transition:transform .3s ease,box-shadow .3s ease,background .3s ease,border-color .3s ease}
.book-gold{color:#0b0b0c;background:var(--gold2);border:1px solid var(--gold2);
        box-shadow:0 14px 38px -14px rgba(230,201,128,.6)}
.book-gold:hover{transform:translateY(-2px);background:#f0dcae}
.book-ghost{color:#fff;background:rgba(24,24,27,.35);border:1px solid rgba(201,168,106,.55)}
.book-ghost:hover{transform:translateY(-2px);border-color:var(--gold2)}

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
  .subhead{padding-top:calc(var(--rx-head) + 2.2rem);padding-bottom:2.2rem}
  .book-btns .book-btn{width:100%;max-width:19rem;justify-content:center}
  .lb-nav{width:2.5rem;height:2.5rem}
}
@media (max-width:380px){
  .rbrand-name{font-size:1.08rem;letter-spacing:.1em}
  .rbrand-sub{font-size:.48rem;letter-spacing:.24em}
  .filters button{font-size:.66rem;letter-spacing:.12em}
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

<?php if ($view === 'main'): ?>

<!-- ======== HERO ======== -->
<section class="pg-hero" aria-label="Photography">
  <?php if ($pgHeroImage !== ''): ?>
    <img style="position:absolute;inset:0;z-index:-3;width:100%;height:100%;object-fit:cover"
         src="<?= $esc($pgHeroImage) ?>" alt="Deepak Studios wedding photography"
         fetchpriority="high" decoding="async">
  <?php endif; ?>
  <div class="pg-hero-bg" aria-hidden="true"></div>
  <div class="pg-hero-ov" aria-hidden="true"></div>
  <div class="pg-hero-in">
    <p class="pg-hero-eyebrow">Deepak Studios</p>
    <span class="pg-hero-rule" aria-hidden="true"></span>
    <h1 class="pg-hero-h">The Art of Capturing Moments</h1>
    <p class="pg-hero-sub">Every celebration has a story. We turn your moments into timeless memories.</p>
    <?php if ($pgHeroImage === ''): ?>
      <div><span class="pg-hero-ph">Hero Image &mdash; Coming Soon</span></div>
    <?php endif; ?>
    <div><a class="pg-hero-cta" id="pgHeroCta" href="#portfolio">Explore Photography</a></div>
  </div>
  <p class="pg-hero-scroll" aria-hidden="true">Scroll <i>&darr;</i></p>
</section>

<!-- ======== CLIENT STORIES ======== -->
<section class="pg-sec" id="portfolio" aria-label="Client stories">
  <div class="pg-wrap">
    <div class="pg-head">
      <p class="pg-eyebrow">Portfolio</p>
      <h2 class="pg-h">Our Client <em>Stories</em></h2>
      <span class="pg-rule" aria-hidden="true"></span>
      <p class="pg-sub">Every client has a unique story. Explore the celebrations and beautiful memories we've captured.</p>
    </div>

    <?php if ($CLIENTS): ?>
      <div class="filters" id="filters" aria-label="Filter client stories">
        <?php foreach ($FILTER_LABELS as $filterKey => $filterLabel): ?>
          <?php if ($filterKey !== 'all' && empty($FILTER_COUNTS[$filterKey])) { continue; } ?>
          <button type="button" data-f="<?= $esc($filterKey) ?>"
                  aria-pressed="<?= $filterKey === 'all' ? 'true' : 'false' ?>"
                  class="<?= $filterKey === 'all' ? 'on' : '' ?>"><?= $esc($filterLabel) ?><span class="fc"><?= (int) ($FILTER_COUNTS[$filterKey] ?? 0) ?></span></button>
        <?php endforeach; ?>
      </div>

      <div class="client-grid" id="clientGrid">
        <?php foreach ($CLIENTS as $slug => $client): ?>
          <a class="client-card" href="<?= $esc($clientUrl($slug)) ?>"
             data-filter="<?= $esc(implode(' ', $client['filters'])) ?>">
            <span class="cc-media">
              <img src="<?= $esc($client['cover']) ?>" alt="<?= $esc($client['title']) ?>"
                   loading="lazy" decoding="async"
                   <?php if (!empty($client['cover_w']) && !empty($client['cover_h'])): ?>width="<?= (int) $client['cover_w'] ?>" height="<?= (int) $client['cover_h'] ?>"<?php endif; ?>>
              <span class="cc-shade" aria-hidden="true"></span>
              <span class="cc-tag"><?= $esc($client['filter_label']) ?></span>
            </span>
            <span class="cc-body">
              <span class="cc-title"><?= $esc($client['title']) ?></span>
              <span class="cc-meta"><?= (int) $client['event_count'] ?> event<?= $client['event_count'] === 1 ? '' : 's' ?> &middot; <?= (int) $client['photo_count'] ?> photograph<?= $client['photo_count'] === 1 ? '' : 's' ?></span>
              <span class="cc-cta">View Story <i>&rarr;</i></span>
            </span>
          </a>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="empty-state">
        <strong>Client Stories Coming Soon</strong>
        <p>New client stories are being curated. Please check back shortly, or get in touch to book your own celebration.</p>
        <a class="es-link" href="#enquire">Book Your Photographer &rarr;</a>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php if ($UNASSIGNED_PHOTOS): ?>
<!-- ======== EXISTING PHOTOGRAPHS AWAITING A CLIENT ======== -->
<section class="pg-sec" style="border-top:1px solid var(--line)" aria-label="Photographs awaiting client assignment">
  <div class="pg-wrap">
    <div class="pg-head">
      <p class="pg-eyebrow">Archive</p>
      <h2 class="pg-h">Awaiting <em>Client Assignment</em></h2>
      <span class="pg-rule" aria-hidden="true"></span>
      <p class="pg-sub">These photographs are preserved here until they are linked to their client story.</p>
    </div>
    <div class="photo-grid">
      <?php $renderPhotos($UNASSIGNED_PHOTOS, 'Awaiting Client Assignment', true); ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php elseif ($view === 'client' && $activeClient !== null): ?>

<!-- ======== CLIENT STORY ======== -->
<section class="subhead" aria-label="Client story">
  <div class="pg-wrap">
    <nav class="crumbs" aria-label="Breadcrumb">
      <a href="index.html">Home</a><span class="sep">/</span>
      <a href="photography.php">Photography</a><span class="sep">/</span>
      <span class="cur">Client Story</span>
    </nav>
    <div class="story-grid">
      <div class="story-media">
        <img src="<?= $esc($activeClient['cover']) ?>" alt="<?= $esc($activeClient['title']) ?>"
             fetchpriority="high" decoding="async"
             <?php if (!empty($activeClient['cover_w']) && !empty($activeClient['cover_h'])): ?>width="<?= (int) $activeClient['cover_w'] ?>" height="<?= (int) $activeClient['cover_h'] ?>"<?php endif; ?>>
      </div>
      <div>
        <p class="pg-eyebrow">Client Story</p>
        <h1 class="page-h"><?= $esc($activeClient['title']) ?></h1>
        <p class="page-meta"><?= (int) $activeClient['event_count'] ?> event<?= $activeClient['event_count'] === 1 ? '' : 's' ?> &middot; <?= (int) $activeClient['photo_count'] ?> photograph<?= $activeClient['photo_count'] === 1 ? '' : 's' ?></p>
        <?php if ($activeClient['intro'] !== ''): ?>
          <p class="story-intro"><?= $esc($activeClient['intro']) ?></p>
        <?php endif; ?>
        <p class="story-note">Every event below belongs to this client&rsquo;s story.</p>
        <a class="story-back" href="photography.php">&larr; All Client Stories</a>
      </div>
    </div>
  </div>
</section>

<section class="pg-sec" style="padding-top:2.4rem" aria-label="Client events">
  <div class="pg-wrap">
    <div class="pg-head">
      <p class="pg-eyebrow">Events</p>
      <h2 class="pg-h">Event <em>Galleries</em></h2>
      <span class="pg-rule" aria-hidden="true"></span>
      <p class="pg-sub"><?= (int) $activeClient['event_count'] ?> event<?= $activeClient['event_count'] === 1 ? '' : 's' ?> &middot; <?= (int) $activeClient['photo_count'] ?> photograph<?= $activeClient['photo_count'] === 1 ? '' : 's' ?> in this story.</p>
    </div>
    <div class="event-grid">
      <?php foreach ($activeClient['events'] as $eventKey => $event): ?>
        <a class="event-card" href="<?= $esc($eventUrl($activeClient['slug'], $eventKey)) ?>">
          <span class="ev-media">
            <img src="<?= $esc($event['cover']) ?>" alt="<?= $esc($event['label']) ?>"
                 loading="lazy" decoding="async"
                 <?php if (!empty($event['cover_w']) && !empty($event['cover_h'])): ?>width="<?= (int) $event['cover_w'] ?>" height="<?= (int) $event['cover_h'] ?>"<?php endif; ?>>
            <span class="ev-shade" aria-hidden="true"></span>
            <span class="ev-count"><?= count($event['photos']) ?> Photograph<?= count($event['photos']) === 1 ? '' : 's' ?></span>
          </span>
          <span class="ev-body">
            <span class="ev-title"><?= $esc($event['label']) ?></span>
            <span class="ev-cta">View Gallery &rarr;</span>
          </span>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php elseif ($view === 'event' && $activeClient !== null && $activeEvent !== null): ?>

<!-- ======== EVENT GALLERY ======== -->
<section class="subhead" aria-label="Event gallery">
  <div class="pg-wrap">
    <nav class="crumbs" aria-label="Breadcrumb">
      <a href="index.html">Home</a><span class="sep">/</span>
      <a href="photography.php">Photography</a><span class="sep">/</span>
      <a href="<?= $esc($clientUrl($activeClient['slug'])) ?>"><?= $esc($activeClient['title']) ?></a><span class="sep">/</span>
      <span class="cur"><?= $esc($activeEvent['label']) ?></span>
    </nav>
    <p class="pg-eyebrow">Event Gallery</p>
    <h1 class="page-h"><?= $esc($activeEvent['label']) ?></h1>
    <p class="page-meta"><?= count($activeEvent['photos']) ?> photograph<?= count($activeEvent['photos']) === 1 ? '' : 's' ?> &middot; <?= $esc($activeClient['title']) ?></p>
    <a class="story-back" href="<?= $esc($clientUrl($activeClient['slug'])) ?>">&larr; Back to <?= $esc($activeClient['title']) ?></a>
  </div>
</section>

<section class="pg-sec" style="padding-top:1.6rem" aria-label="Event photographs">
  <div class="pg-wrap">
    <div class="photo-grid">
      <?php $renderPhotos($activeEvent['photos'], $activeEvent['label']); ?>
    </div>
  </div>
</section>

<?php else: ?>

<!-- ======== NOT FOUND ======== -->
<section class="subhead" aria-label="Story not found">
  <div class="pg-wrap">
    <nav class="crumbs" aria-label="Breadcrumb">
      <a href="index.html">Home</a><span class="sep">/</span>
      <a href="photography.php">Photography</a><span class="sep">/</span>
      <span class="cur">Story Not Found</span>
    </nav>
    <div class="empty-state">
      <strong>Story Not Found</strong>
      <p>The client story or event you are looking for is not available. It may have been renamed or not published yet.</p>
      <a class="es-link" href="photography.php">&larr; View All Client Stories</a>
    </div>
  </div>
</section>

<?php endif; ?>

<?php if ($view !== 'notfound'): ?>
<!-- ======== ENQUIRY ======== -->
<section class="pg-sec book" id="enquire" aria-label="Book your photographer">
  <div class="pg-wrap">
    <p class="pg-eyebrow">Reserve Your Date</p>
    <h2>Your Story Could Be <em>Next</em></h2>
    <p class="book-lead">Book Your Photographer</p>
    <p class="book-copy">Let Deepak Studios capture your celebrations with creativity, care and timeless storytelling.</p>
    <div class="book-btns">
      <a class="book-btn book-gold" target="_blank" rel="noopener noreferrer"
         href="https://wa.me/919031700464?text=Hi%20Deepak%20Studios,%20I%20would%20like%20to%20inquire%20about%20a%20photoshoot...">Enquire on WhatsApp</a>
      <a class="book-btn book-ghost" href="tel:+919031700464">Call Now</a>
    </div>
  </div>
</section>
<?php endif; ?>

<footer><span class="fname">Deepak Studios</span> &nbsp;·&nbsp; &copy; <?= date('Y') ?> All rights reserved.
<div class="social">
<a href="https://www.instagram.com/deepakstudiosofficial_/" target="_blank" rel="noopener noreferrer" aria-label="Deepak Studios on Instagram"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg></a>
<a href="https://www.facebook.com/deepakstudiosfotography" target="_blank" rel="noopener noreferrer" aria-label="Deepak Studios on Facebook"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg></a>
<a href="https://www.youtube.com/@deepakstudiosfotography" target="_blank" rel="noopener noreferrer" aria-label="Deepak Studios on YouTube"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg></a>
</div></footer>

<?php ds_contact_fab(); ?>

<!-- ======== LIGHTBOX ======== -->
<div class="lightbox" id="lightbox" role="dialog" aria-modal="true" aria-label="Photograph viewer" aria-hidden="true">
  <button type="button" class="lb-close" id="lb-close" aria-label="Close viewer">&#10005;</button>
  <button type="button" class="lb-nav lb-prev" id="lb-prev" aria-label="Previous photograph">&#8249;</button>
  <button type="button" class="lb-nav lb-next" id="lb-next" aria-label="Next photograph">&#8250;</button>
  <div class="lb-shell">
    <img class="lb-img" id="lb-img" src="" alt="">
    <p class="lb-cap" id="lb-cap"><small id="lb-cat"></small><span id="lb-t"></span><span class="lb-count" id="lb-count"></span></p>
  </div>
</div>

<script>
(function () {
  'use strict';
  var nav    = document.getElementById('rnav');
  var burger = document.getElementById('rburger');
  var menu   = document.getElementById('rmobmenu');
  var cta    = document.getElementById('pgHeroCta');
  var filters = document.getElementById('filters');
  var clientGrid = document.getElementById('clientGrid');
  var box    = document.getElementById('lightbox');
  var lbImg  = document.getElementById('lb-img');
  var lbCat  = document.getElementById('lb-cat');
  var lbT    = document.getElementById('lb-t');
  var lbCount = document.getElementById('lb-count');
  var lbX    = document.getElementById('lb-close');
  var lbPrev = document.getElementById('lb-prev');
  var lbNext = document.getElementById('lb-next');
  var list = [];
  var index = 0;
  var lastFocus = null;

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

  /* ---- client-story filters ---- */
  if (filters && clientGrid) {
    filters.addEventListener('click', function (e) {
      var btn = e.target.closest ? e.target.closest('button[data-f]') : null;
      if (!btn) return;
      e.preventDefault();
      var key = btn.getAttribute('data-f');
      Array.prototype.forEach.call(filters.querySelectorAll('button'), function (b) {
        var on = b === btn;
        b.classList.toggle('on', on);
        b.setAttribute('aria-pressed', on ? 'true' : 'false');
      });
      var cards = clientGrid.querySelectorAll('.client-card');
      for (var i = 0; i < cards.length; i++) {
        var cardFilters = ' ' + (cards[i].getAttribute('data-filter') || '') + ' ';
        cards[i].hidden = !(key === 'all' || cardFilters.indexOf(' ' + key + ' ') !== -1);
      }
    });
  }

  /* ---- lightbox (open, prev/next, keyboard, backdrop, Esc, X) ---- */
  if (!box || !lbImg) return;

  function renderLightbox() {
    if (!list.length) return;
    var item = list[index];
    var src = item.getAttribute('data-src');
    var cat = item.getAttribute('data-cat') || 'Photograph';
    var title = item.getAttribute('data-title') || '';
    lbImg.src = src;
    lbImg.alt = title || cat;
    lbCat.textContent = cat;
    lbT.textContent = title;
    lbCount.textContent = (index + 1) + ' / ' + list.length;

    /* Preload the neighbours for instant arrow navigation. */
    if (list.length > 1) {
      [list[(index + 1) % list.length], list[(index - 1 + list.length) % list.length]]
        .forEach(function (el) { var im = new Image(); im.src = el.getAttribute('data-src'); });
    }
  }

  function openLightbox(items, start) {
    list = items;
    index = start < 0 ? 0 : start;
    lastFocus = items[index];
    renderLightbox();
    box.classList.add('open');
    box.setAttribute('aria-hidden', 'false');
    document.body.classList.add('vlock');
    document.documentElement.classList.add('vlock');
    if (lbX) lbX.focus();
  }

  window.openLightbox = function (src, category) {
    var fake = document.createElement('button');
    fake.setAttribute('data-src', src);
    fake.setAttribute('data-cat', category || 'Photograph');
    openLightbox([fake], 0);
  };

  window.closeLightbox = function () {
    if (!box.classList.contains('open')) return;
    box.classList.remove('open');
    box.setAttribute('aria-hidden', 'true');
    lbImg.removeAttribute('src');
    lbCat.textContent = '';
    lbT.textContent = '';
    lbCount.textContent = '';
    document.body.classList.remove('vlock');
    document.documentElement.classList.remove('vlock');
    if (lastFocus && lastFocus.focus) lastFocus.focus();
  };

  function step(delta) {
    if (!list.length) return;
    index = (index + delta + list.length) % list.length;
    renderLightbox();
  }

  document.addEventListener('click', function (e) {
    var item = e.target.closest ? e.target.closest('.photo-item[data-src]') : null;
    if (!item) return;
    e.preventDefault();
    e.stopPropagation();
    var grid = item.closest('.photo-grid');
    var items = grid ? Array.prototype.slice.call(grid.querySelectorAll('.photo-item[data-src]')) : [item];
    openLightbox(items, items.indexOf(item));
  });

  box.addEventListener('click', function (e) {
    if (e.target === box || (e.target.closest && e.target.closest('.lb-shell') === null)) {
      window.closeLightbox();
    }
  });
  if (lbX) lbX.addEventListener('click', function (e) { e.preventDefault(); e.stopPropagation(); window.closeLightbox(); });
  if (lbPrev) lbPrev.addEventListener('click', function (e) { e.preventDefault(); e.stopPropagation(); step(-1); });
  if (lbNext) lbNext.addEventListener('click', function (e) { e.preventDefault(); e.stopPropagation(); step(1); });

  document.addEventListener('keydown', function (e) {
    if (!box.classList.contains('open')) return;
    if (e.key === 'Escape') {
      e.preventDefault();
      window.closeLightbox();
    } else if (e.key === 'ArrowLeft') {
      e.preventDefault();
      step(-1);
    } else if (e.key === 'ArrowRight') {
      e.preventDefault();
      step(1);
    }
  });
})();
</script>


</body>
</html>
