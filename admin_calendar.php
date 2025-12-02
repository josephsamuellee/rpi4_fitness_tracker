<?php
require 'db.php';
date_default_timezone_set('America/Los_Angeles');

$db = db();

// Determine which date is selected
$selected = $_GET['day'] ?? date("Y-m-d");

// Get record for selected day
$stmt = $db->prepare("SELECT * FROM daily_records WHERE day = :day");
$stmt->bindValue(':day', $selected);
$res = $stmt->execute()->fetchArray(SQLITE3_ASSOC);

$labels = [
    "ate_protein" => "Ate protein at both meals",
    "hit_veg" => "Hit veg at least once",
    "no_unplanned_snacks" => "No unplanned snacks",
    "workout_12min" => "Did 12-min workout",
    "ran" => "Ran (if a run day)",
    "dinner_by_830" => "Finished dinner by 8:30 PM"
];

// Calendar month info
$year = isset($_GET['y']) ? intval($_GET['y']) : intval(date("Y"));
$month = isset($_GET['m']) ? intval($_GET['m']) : intval(date("n"));
$firstDay = strtotime("$year-$month-01");
$daysInMonth = intval(date("t", $firstDay));
$startWeekday = intval(date("N", $firstDay)); // 1=Mon ...7=Sun

function dayLink($y, $m, $d) {
    $dStr = sprintf("%04d-%02d-%02d", $y, $m, $d);
    return "?y=$y&m=$m&day=$dStr";
}
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin Calendar – Fitness Tracker</title>
<style>
body { font-family: sans-serif; padding: 16px; }
h2 { margin-bottom: 8px; }

.calendar { display: grid; grid-template-columns: repeat(7, 1fr); gap: 4px; margin-bottom: 20px; }
.calendar div { padding: 10px; text-align: center; border: 1px solid #ccc; }

.day { cursor: pointer; background: #f7f7f7; }
.day:hover { background: #e5f3ff; }

.selected { background: #4CAF50 !important; color: white; }
.empty { background: #ffffff; border: none; }

.checkbox-row { margin: 12px 0; }
.save-btn {
    margin-top: 16px; padding: 14px;
    width: 100%; font-size: 18px;
    background: #2196F3; color: white; border: none;
}
</style>
</head>
<body>

<h2>Admin Calendar</h2>

<!-- Date picker -->
<form method="GET">
    <input type="date" name="day" value="<?= $selected ?>" onchange="this.form.submit()">
    <input type="hidden" name="y" value="<?= $year ?>">
    <input type="hidden" name="m" value="<?= $month ?>">
</form>

<br>

<!-- Month Selector -->
<div>
    <a href="?y=<?= $year ?>&m=<?= $month==1 ? 12 : $month-1 ?>&day=<?= $selected ?>">◀ Prev</a>
    &nbsp;&nbsp;
    <strong><?= date("F Y", $firstDay) ?></strong>
    &nbsp;&nbsp;
    <a href="?y=<?= $year ?>&m=<?= $month==12 ? 1 : $month+1 ?>&day=<?= $selected ?>">Next ▶</a>
</div>

<br>

<!-- Calendar Grid -->
<div class="calendar">
    <div><strong>Mon</strong></div>
    <div><strong>Tue</strong></div>
    <div><strong>Wed</strong></div>
    <div><strong>Thu</strong></div>
    <div><strong>Fri</strong></div>
    <div><strong>Sat</strong></div>
    <div><strong>Sun</strong></div>

    <?php
    // Fill blank spaces before first day
    for ($i=1; $i < $startWeekday; $i++) echo "<div class='empty'></div>";

    // Print days
    for ($d=1; $d <= $daysInMonth; $d++) {
        $dateStr = sprintf("%04d-%02d-%02d", $year, $month, $d);
        $cls = ($dateStr == $selected) ? "day selected" : "day";
        echo "<a href='" . dayLink($year,$month,$d) . "' style='text-decoration:none;color:inherit;'>
                <div class='$cls'>$d</div>
              </a>";
    }
    ?>
</div>

<hr>

<h3>Editing: <?= $selected ?></h3>
<p><em>(If no record exists yet, all values default to unchecked.)</em></p>

<!-- Readback -->
<div>
    <h4>Current Values</h4>
    <ul>
        <?php foreach ($labels as $key => $label): ?>
            <li><?= $label ?>:
                <strong><?= $res && $res[$key] ? "YES" : "NO" ?></strong>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

<hr>

<!-- Editor -->
<form method="POST" action="admin_save.php">
    <input type="hidden" name="day" value="<?= $selected ?>">
    <input type="hidden" name="y" value="<?= $year ?>">
    <input type="hidden" name="m" value="<?= $month ?>">


    <?php foreach ($labels as $key => $label): ?>
        <div class="checkbox-row">
            <label>
                <input type="checkbox"
                       name="<?= $key ?>"
                       value="1"
                       <?= ($res && $res[$key]) ? "checked" : "" ?>>
                <?= $label ?>
            </label>
        </div>
    <?php endforeach; ?>

    <button class="save-btn" type="submit">Save</button>
</form>

</body>
</html>
