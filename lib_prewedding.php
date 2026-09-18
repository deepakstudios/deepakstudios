<?php
/**
 * lib_prewedding.php — shared data + renderer for the Pre-Wedding pages.
 *
 * Photos: drop files into  photos/prewedding/  and they appear automatically
 *         (jpg, jpeg, png, webp, gif, avif). No code changes, no deploy —
 *         the homepage service card + nav both open prewedding.php.
 * Videos: the six couple films — byte-for-byte the homepage PWVIDS showcase.
 */

error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

function esc(string $v): string
{
    return htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
}

/* ---- photos (auto-scan photos/prewedding/) ---- */
$photos = [];
$dir = __DIR__ . '/photos/prewedding';
$extensions = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'avif'];
if (is_dir($dir)) {
    foreach (new DirectoryIterator($dir) as $file) {
        if ($file->isDot() || !$file->isFile()) {
            continue;
        }
        $ext = strtolower($file->getExtension());
        if (in_array($ext, $extensions, true)) {
            $photos[] = $file->getFilename();
        }
    }
}
sort($photos, SORT_STRING | SORT_FLAG_CASE);
$photoTotal = count($photos);

/* ---- videos (the six couple films — homepage PWVIDS, exact) ---- */
$videos = [
    ['n' => 'Sushant & Suman',  'id' => 'yvW6COhgwV0'],
    ['n' => 'Gaju & Samapti',   'id' => 'YfccOo4h4Kk'],
    ['n' => 'Ajay & Deboshree', 'id' => 'Tcm2kFq0-sA'],
    ['n' => 'Mohan & Ritu',     'id' => 'LhjChNCyl9E'],
    ['n' => 'Ritu & Mohan',     'id' => 'NEJ4yZ-cs6g'],
    ['n' => 'Kusum & Ajay',     'id' => 'r0EflaC0a_U'],
];
$videoTotal = count($videos);

/* ---- hub cards ---- */
$sections = [
    ['label' => 'Photos', 'icon' => 'camera', 'url' => 'prewedding_photos.php'],
    ['label' => 'Videos', 'icon' => 'film',   'url' => 'prewedding_videos.php'],
];
