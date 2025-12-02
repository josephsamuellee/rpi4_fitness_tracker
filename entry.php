<?php
require 'db.php';
$db = db();

$today = date('Y-m-d');

// Create today's record if not exists
$db->exec("
INSERT OR IGNORE INTO daily_records(
    day, ate_protein, hit_veg, no_unplanned_snacks, 
    workout_12min, ran, dinner_by_830, updated_at
)
VALUES ('$today', 0,0,0,0,0,0, datetime('now'))
");

// Handle POST update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fields = [
        'ate_protein',
        'hit_veg',
        'no_unplanned_snacks',
        'workout_12min',
        'ran',
        'dinner_by_830'
    ];

    foreach ($fields as $f) {
        $val = isset($_POST[$f]) ? 1 : 0;
        $db->exec("UPDATE daily_records SET $f = $val WHERE day = '$today'");
    }
    $db->exec("UPDATE daily_records SET updated_at = datetime('now') WHERE day = '$today'");
}

// Fetch today's state
$res = $db->query("SELECT * FROM daily_records WHERE day='$today'");
$row = $res->fetchArray(SQLITE3_ASSOC);
?>

<h2>Daily Fitness Tracker (<?= $today ?>)</h2>

<form method="POST" style="font-size:1.3rem;">
    <label><input type="checkbox" name="ate_protein" <?= $row['ate_protein'] ? 'checked' : '' ?>> Ate protein at both meals</label><br><br>

    <label><input type="checkbox" name="hit_veg" <?= $row['hit_veg'] ? 'checked' : '' ?>> Hit veg at least once</label><br><br>

    <label><input type="checkbox" name="no_unplanned_snacks" <?= $row['no_unplanned_snacks'] ? 'checked' : '' ?>> No unplanned snacks</label><br><br>

    <label><input type="checkbox" name="workout_12min" <?= $row['workout_12min'] ? 'checked' : '' ?>> 12-min workout</label><br><br>

    <label><input type="checkbox" name="ran" <?= $row['ran'] ? 'checked' : '' ?>> Ran (if a run day)</label><br><br>

    <label><input type="checkbox" name="dinner_by_830" <?= $row['dinner_by_830'] ? 'checked' : '' ?>> Finished dinner by 8:30 PM</label><br><br>

    <button type="submit" style="font-size:2rem; padding:10px 20px;">Save</button>
</form>

