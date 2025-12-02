<?php
require 'db.php';
date_default_timezone_set('America/Los_Angeles');

$db = db();
$day = $_POST['day'];

$fields = [
    "ate_protein",
    "hit_veg",
    "no_unplanned_snacks",
    "workout_12min",
    "ran",
    "dinner_by_830"
];

$data = [];
foreach ($fields as $f) {
    $data[$f] = isset($_POST[$f]) ? 1 : 0;
}

$stmt = $db->prepare("
    INSERT INTO daily_records(day, ate_protein, hit_veg, no_unplanned_snacks, workout_12min, ran, dinner_by_830, updated_at)
    VALUES (:day, :a, :b, :c, :d, :e, :f, DATETIME('now'))
    ON CONFLICT(day) DO UPDATE SET
        ate_protein = excluded.ate_protein,
        hit_veg = excluded.hit_veg,
        no_unplanned_snacks = excluded.no_unplanned_snacks,
        workout_12min = excluded.workout_12min,
        ran = excluded.ran,
        dinner_by_830 = excluded.dinner_by_830,
        updated_at = DATETIME('now')
");

$stmt->bindValue(':day', $day);
$stmt->bindValue(':a', $data["ate_protein"]);
$stmt->bindValue(':b', $data["hit_veg"]);
$stmt->bindValue(':c', $data["no_unplanned_snacks"]);
$stmt->bindValue(':d', $data["workout_12min"]);
$stmt->bindValue(':e', $data["ran"]);
$stmt->bindValue(':f', $data["dinner_by_830"]);

$stmt->execute();

echo "Saved for $day";

