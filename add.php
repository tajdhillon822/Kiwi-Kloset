<?php
// add.php — Add a new costume (branch_id is REQUIRED)
require 'db.php';

$ok = false;
$errors = [];

// Sticky form values (so the form repopulates after errors)
$vals = [
  'name'      => '',
  'category'  => '',
  'size'      => '',
  'rate'      => '',
  'branch_id' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // ---- Collect & sanitize inputs
  $vals['name']      = trim($_POST['name']      ?? '');
  $vals['category']  = trim($_POST['category']  ?? '');
  $vals['size']      = trim($_POST['size']      ?? '');
  $vals['rate']      = trim($_POST['rate']      ?? '');
  $vals['branch_id'] = trim($_POST['branch_id'] ?? '');

  // ---- Basic validation
  if ($vals['name'] === '')      { $errors[] = "Name is required."; }
  if ($vals['category'] === '')  { $errors[] = "Category is required."; }
  if ($vals['size'] === '')      { $errors[] = "Size is required."; }

  if ($vals['rate'] === '' || !is_numeric($vals['rate']) || (float)$vals['rate'] <= 0) {
    $errors[] = "Daily rate must be a positive number.";
  }

  if ($vals['branch_id'] === '' || !ctype_digit($vals['branch_id']) || (int)$vals['branch_id'] < 1) {
    $errors[] = "Branch ID must be a positive whole number.";
  }

  // ---- If basic checks passed, verify the branch actually exists
  if (!$errors) {
    try {
      $q = db()->prepare("SELECT 1 FROM branches WHERE id = ? LIMIT 1");
      $q->execute([(int)$vals['branch_id']]);
      if (!$q->fetch()) {
        $errors[] = "Branch ID ".h($vals['branch_id'])." does not exist.";
      }
    } catch (Throwable $e) {
      $errors[] = "Database error while checking branch: ".$e->getMessage();
    }
  }

  // ---- Insert if everything is valid
  if (!$errors) {
    try {
      $stmt = db()->prepare("
        INSERT INTO costumes (name, category, size, daily_rate, branch_id, is_available)
        VALUES (?, ?, ?, ?, ?, 1)
      ");
      $stmt->execute([
        $vals['name'],
        $vals['category'],
        $vals['size'],
        (float)$vals['rate'],
        (int)$vals['branch_id']
      ]);
      $ok = true;
    } catch (Throwable $e) {
      $errors[] = "Database error: ".$e->getMessage();
    }
  }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Kiwi Kloset — Add Costume</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-grid">
<?php nav('Add'); ?>

<section class="hero accent">
  <div class="container">
    <h1>Add a Costume</h1>
    <p class="lead">Register a new costume into the catalogue. All fields are required.</p>
  </div>
</section>

<main class="container">
  <div class="card elevation">
    <?php if ($ok): ?>
      <div class="alert success">✅ Costume added successfully.</div>
      <p class="m-sm">
        <strong>Name:</strong> <?=h($vals['name'])?> &nbsp;|&nbsp;
        <strong>Category:</strong> <?=h($vals['category'])?> &nbsp;|&nbsp;
        <strong>Size:</strong> <?=h($vals['size'])?> &nbsp;|&nbsp;
        <strong>Rate:</strong> $<?=h(number_format((float)$vals['rate'], 2))?> &nbsp;|&nbsp;
        <strong>Branch ID:</strong> <?=h($vals['branch_id'])?>
      </p>
      <div class="actions">
        <a class="btn" href="add.php">Add Another</a>
        <a class="btn ghost" href="index.php">Back to Catalogue</a>
      </div>
    <?php else: ?>
      <?php if ($errors): ?>
        <div class="alert error">
          <strong>Please fix the following:</strong>
          <ul>
            <?php foreach ($errors as $e): ?>
              <li><?=h($e)?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <!-- Add Costume Form -->
      <form method="post" class="form-grid" novalidate>
        <div>
          <label for="name">Name</label>
          <input id="name" name="name" required placeholder="e.g. Pirate Captain"
                 value="<?=h($vals['name'])?>">
        </div>

        <div>
          <label for="category">Category</label>
          <input id="category" name="category" required placeholder="e.g. Historical"
                 value="<?=h($vals['category'])?>">
        </div>

        <div>
          <label for="size">Size</label>
          <input id="size" name="size" required placeholder="e.g. M / 10-12"
                 value="<?=h($vals['size'])?>">
        </div>

        <div>
          <label for="rate">Daily Rate ($)</label>
          <input id="rate" name="rate" type="number" step="0.01" min="0.01" required
                 placeholder="e.g. 15.00" value="<?=h($vals['rate'])?>">
        </div>

        <div>
          <label for="branch_id">Branch ID</label>
          <input id="branch_id" name="branch_id" type="number" min="1" step="1" required
                 placeholder="e.g. 1" value="<?=h($vals['branch_id'])?>">
          <p class="small">Must be the ID of an existing branch.</p>
        </div>

        <div class="actions">
          <button type="submit">Add Costume</button>
          <a class="btn ghost" href="index.php">Back to Catalogue</a>
        </div>
      </form>
    <?php endif; ?>
  </div>
</main>
</body>
</html>

