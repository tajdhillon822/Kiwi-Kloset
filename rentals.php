<?php
require 'db.php';
$id = in_get_int('id');
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Kiwi Kloset — Rentals</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-grid">
<?php nav('Rentals'); ?>

<section class="hero alt">
  <div class="container">
    <h1>Rental History</h1>
    <p class="lead">Look up all rentals for a specific costume.</p>
  </div>
</section>

<main class="container">
  <div class="card elevation">
    <form method="get" class="form-grid">
      <div>
        <label for="id">Costume ID</label>
        <input type="number" name="id" id="id" min="1" value="<?=h($id??'')?>" required placeholder="e.g. 12">
      </div>
      <div class="actions">
        <button type="submit">View Rentals</button>
        <a class="btn ghost" href="index.php">Back to Catalogue</a>
      </div>
    </form>
  </div>

  <div class="spacer"></div>

  <div class="card elevation">
    <?php
    if ($id !== null) {
      try {
        $st = db()->prepare("
          SELECT r.id, c.name, r.start_datetime, r.end_datetime, r.customer_id
          FROM rentals r
          JOIN costumes c ON r.costume_id = c.id
          WHERE c.id = ?
          ORDER BY r.start_datetime DESC
        ");
        $st->execute([$id]);
        $rows = $st->fetchAll();
        if ($rows) {
          echo '<div class="table-wrap"><table class="table fancy"><thead><tr>
            <th>Rental #</th><th>Costume</th><th>Start</th><th>End</th><th>Customer</th>
          </tr></thead><tbody>';
          foreach ($rows as $r) {
            $end = $r['end_datetime'] ? h($r['end_datetime']) : '<span class="chip warn">Not Returned</span>';
            echo '<tr>
              <td>'.h($r['id']).'</td>
              <td>'.h($r['name']).'</td>
              <td>'.h($r['start_datetime']).'</td>
              <td>'.$end.'</td>
              <td>#'.h($r['customer_id']).'</td>
            </tr>';
          }
          echo '</tbody></table></div>';
        } else {
          echo '<div class="empty">No rentals found for that costume ID.</div>';
        }
      } catch (Throwable $e) {
        echo '<div class="alert error">Database error: '.h($e->getMessage()).'</div>';
      }
    } else {
      echo '<div class="empty">Enter a Costume ID above to see records.</div>';
    }
    ?>
  </div>
</main>
</body>
</html>
