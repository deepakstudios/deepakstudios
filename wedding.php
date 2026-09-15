<?php
/**
 * wedding.php — Wedding Photography overview hub.
 *
 * Lists the five album pages (a.php – e.php) with live photo counts.
 * Album pages pull their photos automatically from:
 *     photos/wedding/<letter>/   (jpg, jpeg, png, webp, gif, avif)
 */

declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

$esc = static function (string $v): string {
    return htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
};

$albums = [
    'a' => 'Album A',
    'b' => 'Album B',
    'c' => 'Album C',
    'd' => 'Album D',
    'e' => 'Album E',
];
$extensions = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'avif'];

$counts = [];
foreach ($albums as $letter => $_label) {
    $dir = __DIR__ . '/photos/wedding/' . $letter;
    $n = 0;
    if (is_dir($dir)) {
        foreach (new DirectoryIterator($dir) as $file) {
            if ($file->isDot() || !$file->isFile()) {
                continue;
            }
            $ext = strtolower(pathinfo($file->getFilename(), PATHINFO_EXTENSION));
            if (in_array($ext, $extensions, true)) {
                $n++;
            }
        }
    }
    $counts[$letter] = $n;
}
$grand = array_sum($counts);

header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Wedding Photography | Deepak Studios</title>
<style>
  * { box-sizing: border-box; }
  html, body { margin: 0; padding: 0; background: #0d0d0d; color: #f5efe0;
               font-family: Georgia, "Times New Roman", serif; }
  a { color: #c9a86a; }
  header { padding: 2.2rem 1rem 1.3rem; text-align: center; background: #161616;
           border-bottom: 1px solid #2a2a2a; }
  header h1 { margin: 0; font-size: 2.2rem; letter-spacing: 2px; }
  header p { margin: .6rem 0 0; color: #c9a86a; }
  .crumbs { margin-top: .9rem; font-size: .88rem; color: #8a8a8a; }
  .crumbs a { text-decoration: none; }
  .crumbs a:hover { text-decoration: underline; }
  main { max-width: 1000px; margin: 0 auto; padding: 2rem 1.2rem 3rem; }
  .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(230px, 1fr)); gap: 1.1rem; }
  .card { display: block; text-decoration: none; color: inherit; background: #161616;
          border: 1px solid #2a2a2a; border-radius: 10px; padding: 1.6rem 1.4rem;
          transition: border-color .3s, transform .3s; }
  .card:hover { border-color: #c9a86a; transform: translateY(-3px); }
  .card .letter { font-size: 2rem; font-weight: 700; color: #c9a86a; font-family: "Playfair Display", Georgia, serif; }
  .card h3 { margin: .55rem 0 .35rem; font-size: 1.15rem; font-weight: 600; }
  .card small { color: #8a8a8a; }
  footer { text-align: center; padding: 1.6rem; color: #666; font-size: .85rem; }
</style>
</head>
<body>
<header>
  <h1>Wedding Photography</h1>
  <p><?= $grand ?> photo<?= $grand === 1 ? '' : 's' ?> across <?= count($albums) ?> albums</p>
  <div class="crumbs"><a href="index.html">Home</a> &rsaquo; Wedding Photography</div>
</header>

<main>
  <div class="grid">
    <?php foreach ($albums as $letter => $label): ?>
      <a class="card" href="<?= $letter ?>.php">
        <div class="letter"><?= strtoupper($letter) ?></div>
        <h3><?= $esc($label) ?></h3>
        <small><?= $counts[$letter] ?> photo<?= $counts[$letter] === 1 ? '' : 's' ?></small>
      </a>
    <?php endforeach; ?>
  </div>
</main>

<footer>&copy; <?= date('Y') ?> Deepak Studios. All rights reserved.</footer>
</body>
</html>