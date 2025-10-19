<?php
// db.php — safer headers + PDO + helpers + navbar

// ---------- DB CONFIG ----------
const DB_HOST = 'localhost';
const DB_NAME = 'amatenga';
const DB_USER = 'kk_admin';
const DB_PASS = 'root';

// ---------- SECURITY HEADERS ----------
function send_security_headers() {
  if (!headers_sent()) {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('Referrer-Policy: no-referrer-when-downgrade');
    // Permissive enough for testing; tighten later if desired
    header("Content-Security-Policy: default-src 'self'; img-src 'self' data:; style-src 'self' 'unsafe-inline';");
  }
}
send_security_headers();

// ---------- PDO CONNECTION ----------
function db() {
  static $pdo = null;
  if ($pdo === null) {
    try {
      $pdo = new PDO(
        'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [
          PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
          PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
      );
    } catch (Throwable $e) {
      http_response_code(500);
      echo "<h1>Database connection failed</h1>";
      echo "<pre>".htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8')."</pre>";
      exit;
    }
  }
  return $pdo;
}

// ---------- HELPERS ----------
function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function in_get_int($key, $min=1){
  if(!isset($_GET[$key])) return null;
  $v = trim($_GET[$key]); if(!ctype_digit($v)) return null;
  $i = (int)$v; return $i >= $min ? $i : null;
}
function in_enum($key, $allowed, $default=null){
  $v = $_GET[$key] ?? $default;
  return in_array($v, $allowed, true) ? $v : $default;
}

// ---------- NAVBAR ----------
function nav($active=''){
  $items = [
    'Home'    => 'index.php',
    'Rentals' => 'rentals.php',
    'Add'     => 'add.php',
    'Stats'   => 'stats.php'
  ];
  echo '<header class="topbar">';
  echo '<div class="brand"><span class="kiwi">🥝</span> Kiwi Kloset <small>Staff</small></div>';
  echo '<nav class="tabs"><ul>';
  foreach($items as $label=>$href){
    $is = (strtolower($active)===strtolower($label)) ? ' class="active"' : '';
    $labelEsc = h($label);
    $hrefEsc  = h($href);
    echo '<li'.$is.'><a href="'.$hrefEsc.'">'.$labelEsc.'</a></li>';
  }
  echo '</ul></nav></header>';
}
?>