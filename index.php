<?php
require 'db.php';
date_default_timezone_set('America/Los_Angeles');
$today = date("Y-m-d");

//prepopulate the database fetch such that the fresh page will display 
//current entered day's data (if exists)
$db = db();

// Get record for selected day
$stmt = $db->prepare("SELECT * FROM daily_records WHERE day = :day");
$stmt->bindValue(':day', $today);
$record = $stmt->execute()->fetchArray(SQLITE3_ASSOC);

$labels = [
    "ate_protein" => "Ate protein at both meals",
    "hit_veg" => "Hit veg at least once",
    "no_unplanned_snacks" => "No unplanned snacks",
    "workout_12min" => "Did 12-min workout",
    "ran" => "Ran (if a run day)",
    "dinner_by_830" => "Finished dinner by 8:30 PM"
];

// If no record exists, default to blanks
//if (!$record) {
//    $record = [
//        "ate_protein" => 0,
//        "hit_veg" => 0,
//        "no_unplanned_snacks" => 0,
//        "workout_12min" => 0,
//        "ran" => 0,
//        "dinner_by_830" => 0
//    ];
//}
//?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Fitness Tracker</title>

<style>
body { font-family: sans-serif; margin: 0; padding: 0; }
.top-buttons { display: flex; height: 20vh; }
.top-buttons button {
    flex: 1;
    font-size: 24px;
    border: none;
}
.active { background: #4CAF50; color: white; }
.hidden { display: none; }

.checkbox-row { padding: 16px; font-size: 20px; }
.save-btn { width: 100%; padding: 20px; font-size: 20px; background: #2196F3; color: white; }

.chart-container { width: 95%; margin: auto; }
</style>

<script>
let mode = "entry";

function switchMode(m) {
    mode = m;
    document.getElementById('entry').classList.toggle('hidden', m !== "entry");
    document.getElementById('dash').classList.toggle('hidden', m !== "dash");

    document.getElementById('btn1').classList.toggle('active', m === "entry");
    document.getElementById('btn2').classList.toggle('active', m === "dash");

    if (m === "dash") loadCharts();
}

function saveData() {
    const data = new FormData(document.getElementById('form'));

    fetch('save.php', { method: 'POST', body: data })
      .then(r => r.text())
      .then(alert);
}

function loadCharts() {
    fetch('get_data.php?range=7')
      .then(r => r.json())
      .then(data => drawSevenDayChart(data));

    fetch('get_data.php?range=30')
      .then(r => r.json())
      .then(data => drawHeatmap("heat30", data));

    fetch('get_data.php?range=ytd')
      .then(r => r.json())
      .then(data => drawHeatmap("heatytd", data));
}
</script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="charts.js"></script>
</head>

<body>

<div class="top-buttons">
    <button id="btn1" class="active" onclick="switchMode('entry')">data_entry</button>
    <button id="btn2" onclick="switchMode('dash')">review</button>
</div>

<!-- Entry mode -->
<div id="entry">
    <form id="form">
        <input type="hidden" name="day" value="<?= $today ?>">

        <?php foreach ($labels as $key => $label): ?>
            <div class="checkbox-row">
                <label>
                    <input type="checkbox"
                           name="<?= $key ?>"
                           value="1"
                           <?= ($record && $record[$key]) ? "checked" : "" ?>>
                    <?= $label ?>
                </label>
            </div>
        <?php endforeach; ?>
    </form>

    <button class="save-btn" onclick="saveData()">Save</button>
</div>

<!-- Dashboard mode -->
<div id="dash" class="hidden">
    <div class="chart-container">
        <h3>Last 7 Days</h3>
        <canvas id="sevenDay"></canvas>
    </div>

    <div class="chart-container">
        <h3>Last 30 Days (Heatmap)</h3>
        <canvas id="heat30"></canvas>
    </div>

    <div class="chart-container">
        <h3>YTD Heatmap</h3>
        <canvas id="heatytd"></canvas>
    </div>
</div>

</body>
</html>

