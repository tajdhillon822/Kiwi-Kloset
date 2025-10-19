<?php
require 'db.php';
$popularity = in_enum('popularity',['most','least'],'most');
$orderSql = ($popularity==='least') ? 'ORDER BY times_rented ASC, c.id ASC' : 'ORDER BY times_rented DESC, c.id ASC';
function qall($sql,$params=[]){ try{$s=db()->prepare($sql);$s->execute($params);return $s->fetchAll();}catch(Throwable $e){return ['__error__'=>$e->getMessage()];}}
$topCostumes = qall("
  SELECT c.id,c.name,c.category,COUNT(r.id) AS times_rented
  FROM costumes c LEFT JOIN rentals r ON r.costume_id=c.id
  GROUP BY c.id,c.name,c.category
  $orderSql LIMIT 10
");
$topRevenue = qall("
  SELECT c.id,c.name,c.category,
  SUM(GREATEST(TIMESTAMPDIFF(DAY,r.start_datetime,COALESCE(r.end_datetime,NOW()))+1,1)*c.daily_rate) AS revenue
  FROM costumes c JOIN rentals r ON r.costume_id=c.id
  GROUP BY c.id,c.name,c.category
  ORDER BY revenue DESC LIMIT 1
");
$topCategories = qall("
  SELECT c.category,COUNT(r.id) AS rentals_count
  FROM costumes c LEFT JOIN rentals r ON r.costume_id=c.id
  GROUP BY c.category ORDER BY rentals_count DESC LIMIT 3
");
$branchMonth = qall("
  SELECT b.id AS branch_id,b.name AS branch_name,COUNT(r.id) AS rentals_this_month
  FROM branches b LEFT JOIN costumes c ON c.branch_id=b.id
  LEFT JOIN rentals r ON r.costume_id=c.id
    AND YEAR(r.start_datetime)=YEAR(CURDATE())
    AND MONTH(r.start_datetime)=MONTH(CURDATE())
  GROUP BY b.id,b.name ORDER BY rentals_this_month DESC
");
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Kiwi Kloset — Statistics</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-grid">
<?php nav('Stats'); ?>

<section class="hero deep">
  <div class="container">
    <h1>Insights & Trends</h1>
    <p class="lead">Live rental statistics across the business.</p>
  </div>
</section>

<main class="container">
  <div class="card elevation">
    <div class="between">
      <h2>Top 10 <?=h(ucfirst($popularity))?> Rented Costumes</h2>
      <div class="segmented">
        <a class="<?= $popularity==='most'?'on':''?>" href="stats.php?popularity=most">Most</a>
        <a class="<?= $popularity==='least'?'on':''?>" href="stats.php?popularity=least">Least</a>
      </div>
    </div>
    <?php
      if(isset($topCostumes['__error__'])) echo '<div class="alert error">'.h($topCostumes['__error__']).'</div>';
      elseif(!$topCostumes) echo '<div class="empty">No data found.</div>';
      else {
        echo '<div class="table-wrap"><table class="table fancy"><thead><tr><th>ID</th><th>Name</th><th>Category</th><th>Times Rented</th></tr></thead><tbody>';
        foreach($topCostumes as $r){
          echo '<tr><td>'.h($r['id']).'</td><td>'.h($r['name']).'</td><td>'.h($r['category']).'</td><td>'.h($r['times_rented']).'</td></tr>';
        }
        echo '</tbody></table></div>';
      }
    ?>
  </div>

  <div class="spacer"></div>

  <div class="grid-2">
    <div class="card elevation">
      <h2>Top Revenue Costume</h2>
      <?php
        if(isset($topRevenue['__error__'])) echo '<div class="alert error">'.h($topRevenue['__error__']).'</div>';
        elseif(!$topRevenue) echo '<div class="empty">No revenue data.</div>';
        else { $r=$topRevenue[0];
          echo '<div class="metric"><div class="metric-label">'.h($r['name']).' ('.h($r['category']).')</div>
                <div class="metric-value">$'.h(number_format($r['revenue'],2)).'</div></div>';
        }
      ?>
    </div>

    <div class="card elevation">
      <h2>Top 3 Categories</h2>
      <?php
        if(isset($topCategories['__error__'])) echo '<div class="alert error">'.h($topCategories['__error__']).'</div>';
        elseif(!$topCategories) echo '<div class="empty">No category data.</div>';
        else {
          echo '<ol class="ranked">';
          foreach($topCategories as $r){
            echo '<li><span>'.h($r['category']).'</span><em>'.h($r['rentals_count']).'</em></li>';
          }
          echo '</ol>';
        }
      ?>
    </div>
  </div>

  <div class="spacer"></div>

  <div class="card elevation">
    <h2>Branch Rentals — This Month</h2>
    <?php
      if(isset($branchMonth['__error__'])) echo '<div class="alert error">'.h($branchMonth['__error__']).'</div>';
      elseif(!$branchMonth) echo '<div class="empty">No branch data.</div>';
      else {
        echo '<div class="table-wrap"><table class="table fancy"><thead><tr><th>Branch</th><th>Rentals</th></tr></thead><tbody>';
        foreach($branchMonth as $r){
          echo '<tr><td>'.h($r['branch_name']).'</td><td>'.h($r['rentals_this_month']).'</td></tr>';
        }
        echo '</tbody></table></div>';
      }
    ?>
  </div>
</main>
</body>
</html>
