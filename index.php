<?php
require 'db.php';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Kiwi Kloset — All Costumes</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-grid">
<?php nav('Home'); ?>

<section class="hero">
  <div class="container">
    <h1>Costume Catalogue</h1>
    <p class="lead">Browse the full inventory. Use <strong>Rentals</strong> to view history or <strong>Add</strong> to register a new costume.</p>
  </div>
</section>

<main class="container">
  <div class="card elevation">
    <?php
    try {
      $st = db()->query("SELECT id, name, category, size, daily_rate, is_available FROM costumes ORDER BY id ASC");
      $rows = $st->fetchAll();
      if ($rows) {
        echo '<div class="table-wrap"><table class="table fancy"><thead><tr>
          <th>ID</th><th>Name</th><th>Category</th><th>Size</th><th>Rate</th><th>Status</th>
        </tr></thead><tbody>';
        foreach ($rows as $r) {
          $badge = $r['is_available'] ? '<span class="chip success">Available</span>' : '<span class="chip">Unavailable</span>';
          echo '<tr>
            <td>'.h($r['id']).'</td>
            <td>'.h($r['name']).'</td>
            <td>'.h($r['category']).'</td>
            <td>'.h($r['size']).'</td>
            <td>$'.h(number_format((float)$r['daily_rate'],2)).'</td>
            <td>'.$badge.'</td>
          </tr>';
        }
        echo '</tbody></table></div>';
      } else {
        echo '<div class="empty">No costumes found.</div>';
      }
    } catch (Throwable $e) {
      echo '<div class="alert error">Database error: '.h($e->getMessage()).'</div>';
    }
    ?>
  </div>
</main>
</body>
</html>
